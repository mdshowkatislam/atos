<?php

namespace App\Console\Commands;

use App\Models\ScheduledSetting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;

class SyncAccessToMySQL extends Command
{
    protected $signature = 'access:sync {--file=}';
    protected $description = 'Sync Access SQL data to MySQL and push to API';

    public function handle()
    {
          \Log::info('Access_Sync_Command_Ran');
        // Prevent overlapping runs
        if (cache()->has('access_sync_running')) {
            return Command::SUCCESS;
        }

        cache()->put('access_sync_running', true, 600);

        try {
            $cliFile  = $this->option('file');
            $settings = ScheduledSetting::first();

            if (!$settings && !$cliFile) {
                \Log::error('scheduled_settings row not found');
                return Command::SUCCESS;
            }

            /**
             * ----------------------------
             * Schedule check
             * ----------------------------
             */
            if (!$cliFile) {
                $syncTime = (int) ($settings->value ?? 1);
                if ($syncTime < 1 || $syncTime > 7) $syncTime = 1;

                if ($settings->last_sync) {
                    $last = Carbon::parse($settings->last_sync);
                    $now  = now();

                    $shouldRun = match ($syncTime) {
                        1 => $last->diffInMinutes($now) >= 1,
                        2 => $last->diffInMinutes($now) >= 30,
                        3 => $last->diffInHours($now) >= 1,
                        4 => $last->diffInHours($now) >= 2,
                        5 => $now->format('H:i') === '13:00',
                        6 => in_array($now->format('H'), ['01', '13']),
                        7 => $now->between(
                                $now->copy()->setTime(9, 0),
                                $now->copy()->setTime(17, 0)
                             ),
                        default => false,
                    };

                    if (!$shouldRun) {
                        \Log::info('access:sync skipped by schedule');
                        return Command::SUCCESS;
                    }
                }
            }

            /**
             * ----------------------------
             * Import SQL file
             * ----------------------------
             */
            $accessFile = $cliFile ?: storage_path('app/public/access/incoming.sql');

            if (!file_exists($accessFile)) {
                \Log::error('SQL file not found: ' . $accessFile);
                return Command::FAILURE;
            }

            DB::beginTransaction();
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            $sql = file_get_contents($accessFile);
            foreach ($this->splitSqlStatements($sql) as $stmt) {
                if (trim($stmt)) {
                    try {
                        DB::statement($stmt);
                    } catch (\Throwable $e) {
                        \Log::warning('SQL failed', [
                            'error' => $e->getMessage(),
                            'sql'   => substr($stmt, 0, 200)
                        ]);
                    }
                }
            }

            try {
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
                $pdo = null;
                try {
                    $pdo = DB::getPdo();
                } catch (\Throwable $e) {
                    // ignore — will be handled below
                }

                if ($pdo && method_exists($pdo, 'inTransaction') && $pdo->inTransaction()) {
                    DB::commit();
                } else {
                    \Log::warning('No active DB transaction to commit');
                }
            } catch (\Throwable $e) {
                \Log::error('Transaction commit failed: ' . $e->getMessage());
                try {
                    if (isset($pdo) && $pdo && method_exists($pdo, 'inTransaction') && $pdo->inTransaction()) {
                        DB::rollBack();
                    }
                } catch (\Throwable $ex) {
                    \Log::error('Rollback failed: ' . $ex->getMessage());
                }
            }

            /**
             * ----------------------------
             * Prepare student data
             * ----------------------------
             */
            $rows = DB::table('checkinout as c')
                ->join('userinfo as u', 'c.USERID', '=', 'u.USERID')
                ->select('u.USERID as id', 'c.CHECKTIME as check_time')
                ->orderBy('c.CHECKTIME')
                ->get();

            $studentData = [];
            foreach ($rows as $row) {
                $studentData[] = [
                    'id'   => $row->id,
                    'date' => Carbon::parse($row->check_time)->format('Y-m-d'),
                    'time' => Carbon::parse($row->check_time)->format('h:i:s A'),
                ];
            }

            /**
             * ----------------------------
             * Push to API (batched)
             * ----------------------------
             */
            $endpoint  = config('api_url.endpoint') . '/accessBdStore';
            $batchSize = 1000;

            foreach (array_chunk($studentData, $batchSize) as $chunk) {
                try {
                    Http::withOptions(['verify' => false])
                        ->acceptJson()
                        ->post($endpoint, ['studentData' => $chunk]);
                } catch (\Throwable $e) {
                    \Log::error('API push failed: ' . $e->getMessage());
                }
            }

            if ($settings) {
                $settings->update(['last_sync' => now()]);
            }

            \Log::info('✅ access:sync completed');
            return Command::SUCCESS;

        } catch (\Throwable $e) {
            \Log::error('access:sync fatal error: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return Command::FAILURE;
        } finally {
            cache()->forget('access_sync_running');
        }
    }

    /**
     * Split SQL dump into individual statements
     */
    private function splitSqlStatements(string $sql): array
    {
        $statements = [];
        $current = '';
        $inSingle = false;
        $inDouble = false;
        $escape = false;

        foreach (str_split($sql) as $ch) {
            $current .= $ch;

            if ($escape) { $escape = false; continue; }
            if ($ch === "\\") { $escape = true; continue; }
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
}