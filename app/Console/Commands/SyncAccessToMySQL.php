<?php

namespace App\Console\Commands;

use App\Models\ScheduledSetting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class SyncAccessToMySQL extends Command
{
    protected $signature = 'access:sync {--file=}';
    protected $description = 'Sync Access SQL data to MySQL and push to API';

   public function handle()
{
    Log::info('================ access:sync START ================');
    Log::info('Command invoked', [
        'file_option' => $this->option('file'),
        'php_sapi' => php_sapi_name(),
        'pid' => getmypid(),
    ]);

    $lockFile = storage_path('logs/access_sync.lock');

    // Check if lock exists and process is running
    if (file_exists($lockFile)) {
        $lockData = json_decode(file_get_contents($lockFile), true);
        $previousPid = $lockData['pid'] ?? null;
        $lockTime = isset($lockData['time']) ? strtotime($lockData['time']) : 0;
        $lockAgeSeconds = time() - $lockTime;

        Log::info('Lock file analysis', [
            'previous_pid' => $previousPid,
            'lock_age_seconds' => $lockAgeSeconds,
            'lock_time' => $lockData['time'] ?? 'unknown',
        ]);

        // If lock is older than 300 seconds (5 minutes), consider it stale
        if ($lockAgeSeconds > 300) {
            Log::warning('Stale lock file detected — forcing cleanup', [
                'pid' => $previousPid,
                'age_seconds' => $lockAgeSeconds,
            ]);
            @unlink($lockFile);
        } elseif ($previousPid) {
            // Lock is recent, check if process actually exists
            $processRunning = false;

            if (PHP_OS_FAMILY === 'Linux') {
                $processRunning = @posix_kill($previousPid, 0);
                Log::info('Linux process check', [
                    'pid' => $previousPid,
                    'is_running' => $processRunning ? 'YES' : 'NO',
                ]);
            } elseif (PHP_OS_FAMILY === 'Windows') {
                $output = shell_exec("tasklist /FI \"PID eq {$previousPid}\" 2>nul");
                $processRunning = strpos($output, (string)$previousPid) !== false;
                Log::info('Windows process check', [
                    'pid' => $previousPid,
                    'is_running' => $processRunning ? 'YES' : 'NO',
                ]);
            }

            if ($processRunning) {
                Log::warning('Sync already running by process', ['pid' => $previousPid]);
                return Command::SUCCESS;
            } else {
                // Process is dead but lock is recent — cleanup
                Log::info('Previous process is dead — removing stale lock');
                @unlink($lockFile);
            }
        }
    }

    // Create lock file
    $myPid = getmypid();
    file_put_contents($lockFile, json_encode(['pid' => $myPid, 'time' => now()->toDateTimeString()]));
    Log::info('Lock file created', ['pid' => $myPid]);

    try {
        $cliFile = $this->option('file');
        $settings = ScheduledSetting::first();

        if (!$settings && !$cliFile) {
            Log::error('No scheduled_settings row found and no --file provided');
            return Command::SUCCESS;
        }

        // Schedule check
        if (!$cliFile && $settings?->last_sync) {
            $syncTime = (int) ($settings->value ?? 1);
            $syncTime = ($syncTime < 1 || $syncTime > 7) ? 1 : $syncTime;

            $last = Carbon::parse($settings->last_sync);
            $now = now();

            $shouldRun = match ($syncTime) {
                1 => $last->diffInMinutes($now) >= 1,
                2 => $last->diffInMinutes($now) >= 30,
                3 => $last->diffInHours($now) >= 1,
                4 => $last->diffInHours($now) >= 2,
                5 => $now->format('H:i') === '13:00',
                6 => in_array($now->format('H'), ['01','13']),
                7 => $now->between($now->copy()->setTime(9,0), $now->copy()->setTime(17,0)),
                default => false,
            };

            if (!$shouldRun) {
                Log::info('Skipped due to schedule');
                return Command::SUCCESS;
            }
        }

        // SQL import
        $accessFile = $cliFile ?: storage_path('app/public/access/incoming.sql');
        if (!file_exists($accessFile)) {
            Log::error('SQL file not found', ['path' => $accessFile]);
            return Command::FAILURE;
        }

        Log::info('SQL import starting', ['file' => $accessFile, 'size' => filesize($accessFile)]);

        DB::beginTransaction();
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $sql = file_get_contents($accessFile);
        foreach ($this->splitSqlStatements($sql) as $stmt) {
            if (!trim($stmt)) continue;

            try {
                DB::statement($stmt);
            } catch (\Throwable $e) {
                Log::warning('SQL statement failed', [
                    'error' => $e->getMessage(),
                    'sql_preview' => substr($stmt, 0, 200),
                ]);
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        if (DB::getPdo()->inTransaction()) DB::commit();
        Log::info('DB transaction committed');

        // Prepare student data in chunks
        Log::info('Preparing student data');

        $endpoint = config('api_url.endpoint') . '/accessBdStore';
        $chunkSize = 1000;

        $batchCount = 0;
        DB::table('checkinout as c')
            ->join('userinfo as u', 'c.USERID', '=', 'u.USERID')
            ->orderBy('c.CHECKTIME')
            ->chunk($chunkSize, function($rows) use (&$batchCount) {
                $batch = [];
                foreach ($rows as $row) {
                    $batch[] = [
                        'id' => $row->USERID,
                        'date' => Carbon::parse($row->CHECKTIME)->format('Y-m-d'),
                        'time' => Carbon::parse($row->CHECKTIME)->format('h:i:s A'),
                    ];
                }

                if (!empty($batch)) {
                    $batchCount++;
                    
                    // Store in queue table instead of pushing directly
                    DB::table('api_push_queue')->insert([
                        'student_data' => json_encode(['studentData' => $batch]),
                        'status' => 'pending',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    Log::info('Batch queued for API push', [
                        'batch_num' => $batchCount,
                        'records' => count($batch),
                    ]);
                }
            });

        Log::info('All batches queued', ['total_batches' => $batchCount]);

        if ($settings) $settings->update(['last_sync' => now()]);

        Log::info('================ access:sync COMPLETED ================');
        return Command::SUCCESS;

    } catch (\Throwable $e) {
        Log::critical('access:sync FATAL ERROR', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
        return Command::FAILURE;
    } finally {
        @unlink($lockFile);
        Log::info('Lock file removed');
    }
}


    private function splitSqlStatements(string $sql): array
    {
        $statements = [];
        $current = '';
        $inSingle = $inDouble = $escape = false;

        foreach (str_split($sql) as $ch) {
            $current .= $ch;

            if ($escape) { $escape = false; continue; }
            if ($ch === '\\') { $escape = true; continue; }
            if ($ch === "'" && !$inDouble) { $inSingle = !$inSingle; continue; }
            if ($ch === '"' && !$inSingle) { $inDouble = !$inDouble; continue; }

            if ($ch === ';' && !$inSingle && !$inDouble) {
                $statements[] = trim($current);
                $current = '';
            }
        }

        if (trim($current)) {
            $statements[] = trim($current);
        }

        return $statements;
    }

    private function pushViaStream($endpoint, $data)
    {
        $payload = json_encode(['studentData' => $data]);

        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\n",
                'content' => $payload,
                'timeout' => 30,
                'ignore_errors' => true,
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ]);

        $response = @file_get_contents($endpoint, false, $context);

        if ($response === false) {
            throw new \Exception('Stream API call failed');
        }

        Log::info('API response (stream)', [
            'body' => substr($response, 0, 300),
        ]);
    }
}
