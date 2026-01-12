<?php

namespace App\Console\Commands;

use App\Models\ScheduledSetting;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;
use PDO;

class SyncAccessToMySQL extends Command
{
    // protected $description = 'Sync data from MS Access to MySQL and prepare formatted student data';

    protected $signature = 'access:sync {--file=}';

    public function handle()
    {
        // ------------------------------------------------------------------
        // Prevent overlapping runs (Windows Task Scheduler safe)
        // ------------------------------------------------------------------
        if (cache()->has('access_sync_running')) {
            return Command::SUCCESS;
        }
        cache()->put('access_sync_running', true, 600);  // lock for 10 minutes

        try {
            // ------------------------------------------------------------------
            // Determine input Access DB file (allow override via --file)
            // ------------------------------------------------------------------
            $cliFile = $this->option('file');

            // Load settings (single-row table design) if available
            $settings = ScheduledSetting::first();

            if (!$settings && !$cliFile) {
                \Log::error('scheduled_settings row not found');
                return Command::SUCCESS;
            }

            // If no CLI file provided, enforce schedule rules from settings
            if (!$cliFile) {
                $syncTime = (int) ($settings->value ?? 1);  // 1–7
                if ($syncTime < 1 || $syncTime > 7) {
                    $syncTime = 1;
                }

                $lastSync = $settings->last_sync;
                $now = now();
                // Decide whether sync should run
                if ($lastSync) {
                    $last = Carbon::parse($lastSync);

                    $shouldRun = match ($syncTime) {
                        1 => $last->diffInMinutes($now) >= 1,
                        2 => $last->diffInMinutes($now) >= 30,
                        3 => $last->diffInHours($now) >= 1,
                        4 => $last->diffInHours($now) >= 2,
                        5 => $now->format('H:i') === '13:00',
                        6 => in_array($now->format('H'), ['01', '13']) && $last->diffInMinutes($now) >= 1,
                        7 => $now->isBetween(
                            $now->copy()->setTime(9, 0),
                            $now->copy()->setTime(17, 0)
                        ) && $last->diffInMinutes($now) >= 1,
                        default => false,
                    };

                    if (!$shouldRun) {
                        \Log::info('access:sync skipped by schedule rule');
                        return Command::SUCCESS;
                    }
                }
  
              
            } else {
                \Log::info('access:sync EXECUTING with uploaded file: ' . $cliFile);
            }

            // Determine which Access DB file to use
            $accessFile = $cliFile ?: $settings->db_location;
 
            if (!$accessFile || !file_exists($accessFile)) {
                \Log::error('Access DB file not found: ' . $accessFile);
                return Command::FAILURE;
            }

            // ------------------------------------------------------------------
            // Connect to MS Access
            // ------------------------------------------------------------------
            $dsn = "odbc:Driver={Microsoft Access Driver (*.mdb, *.accdb)};Dbq={$accessFile};";

            $pdo = new PDO($dsn);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
                  
            // ------------------------------------------------------------------
            // Sync only required columns from USERINFO and CHECKINOUT
            // ------------------------------------------------------------------
            $tables = [
                'USERINFO' => ['cols' => ['USERID', 'Badgenumber', 'name']],
                'CHECKINOUT' => ['cols' => ['USERID', 'CHECKTIME']],
            ];

            foreach ($tables as $table => $meta) {
                $cols = implode(', ', $meta['cols']);
                $tableName = strtolower($table);

                // Create a minimal, consistent schema for each table
                if (!Schema::hasTable($tableName)) {
                    Schema::create($tableName, function (Blueprint $t) use ($table) {
                        $t->increments('id');

                        if ($table === 'USERINFO') {
                            $t->integer('USERID')->nullable();
                            $t->string('Badgenumber', 100)->nullable();
                            $t->string('name', 100)->nullable();
                          
                        }

                        if ($table === 'CHECKINOUT') {
                            $t->integer('USERID')->nullable();
                            $t->dateTime('CHECKTIME')->nullable();
                        }

                        $t->timestamps();
                    });
                }

                // Use streaming fetch to avoid loading entire table into memory
                // and support an optional filter by last_sync to only pull new rows.
                $where = '';
                $params = [];
                // Only filter by CHECKTIME when reading the CHECKINOUT table and
                // when not importing an uploaded file (CLI file should import everything).
                $useFilter = false;
                if (!$cliFile && strtoupper($table) === 'CHECKINOUT' && $settings && $settings->last_sync) {
                    $useFilter = true;
                    $where = " WHERE CHECKTIME >= ?";
                    $params[] = Carbon::parse($settings->last_sync)->format('Y-m-d H:i:s');
                }

                $fetchedCount = 0;
                $processedCount = 0;
                $firstRow = null;

                $sql = "SELECT {$cols} FROM {$table}" . $where;
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);

                $batchSize = 500;
                $batch = [];

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    // normalize empty strings to null
                    $fetchedCount++;
                    foreach ($row as $k => $v) {
                        if ($v === '') {
                            $row[$k] = null;
                        }
                    }

                    if ($firstRow === null) {
                        $firstRow = $row;
                    }

                    // add timestamps for upsert
                    $now = Carbon::now();
                    $row['updated_at'] = $now;
                    if (!isset($row['created_at'])) {
                        $row['created_at'] = $now;
                    }

                    $batch[] = $row;

                    if (count($batch) >= $batchSize) {
                        try {
                            if ($tableName === 'userinfo') {
                                DB::table('userinfo')->upsert(
                                    $batch,
                                    ['USERID'],
                                    ['Badgenumber', 'name', 'updated_at']
                                );
                                $processedCount += count($batch);
                            } else {
                                DB::table('checkinout')->upsert(
                                    $batch,
                                    ['USERID', 'CHECKTIME'],
                                    ['updated_at']
                                );
                                $processedCount += count($batch);
                            }
                        } catch (\Throwable $e) {
                            \Log::error('DB upsert error: ' . $e->getMessage());
                        }

                        // flush batch
                        $batch = [];
                    }
                }

                // remaining rows
                if (!empty($batch)) {
                    try {
                        if ($tableName === 'userinfo') {
                            DB::table('userinfo')->upsert(
                                $batch,
                                ['USERID'],
                                ['Badgenumber', 'name', 'updated_at']
                            );
                            $processedCount += count($batch);
                        } else {
                            DB::table('checkinout')->upsert(
                                $batch,
                                ['USERID', 'CHECKTIME'],
                                ['updated_at']
                            );
                            $processedCount += count($batch);
                        }
                    } catch (\Throwable $e) {
                        \Log::error('DB upsert error (final batch): ' . $e->getMessage());
                    }
                }

                \Log::info("access:sync table {$tableName} fetched {$fetchedCount} rows, upserted {$processedCount} rows");
                if ($firstRow) {
                    \Log::info('access:sync sample row for ' . $tableName, $firstRow);
                }
            }

                  \Log::info('KKK2'); 
            // ------------------------------------------------------------------
            // Push formatted data to API (send RAW checkin rows instead of aggregating)
            // ------------------------------------------------------------------
            $rows = DB::table('checkinout as c')
                ->join('userinfo as u', 'c.USERID', '=', 'u.USERID')
                ->select('u.USERID as id', 'c.CHECKTIME as check_time')
                ->orderBy('c.CHECKTIME')
                ->get();

            $studentData = [];

            \Log::info('PPPPPP');

            foreach ($rows as $row) {
                $studentData[] = [
                    'id' => $row->id,
                    'date' => Carbon::parse($row->check_time)->format('Y-m-d'),
                    'time' => Carbon::parse($row->check_time)->format('h:i:s A'),
                ];
            }

            \Log::info('PPPPPP2');
            $total = count($studentData);
            \Log::info('access:sync raw studentData count: ' . $total);

                // Send in batches to avoid remote-server limits (body size, timeouts, max_input_vars, etc.)
                $batchSize = 1000;
                $endpoint = config('api_url.endpoint') . '/accessBdStore';

                for ($i = 0; $i < $total; $i += $batchSize) {
                    $chunk = array_slice($studentData, $i, $batchSize);

                    try {
                        $response = Http::withOptions(['verify' => false])
                            ->acceptJson()
                            ->post($endpoint, [
                                'studentData' => $chunk
                            ]);

                        $status = $response->status();
                        $body = $response->body();
                        \Log::info('access:sync push chunk', [
                            'start_index' => $i,
                            'count' => count($chunk),
                            'status' => $status,
                            'body_snippet' => substr($body, 0, 1000)
                        ]);

                        if ($status >= 400) {
                            \Log::warning('access:sync push returned error status', ['status' => $status]);
                        }
                    } catch (\Throwable $e) {
                        \Log::error('access:sync HTTP error: ' . $e->getMessage());
                    }
                }

            // ------------------------------------------------------------------
            // Update last_sync
            // ------------------------------------------------------------------
            $settings->update([
                'last_sync' => now(),
            ]);

            \Log::info('✅ access:sync COMPLETED at' . now());

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            
            \Log::error('access:sync error: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return Command::FAILURE;
        } finally {
             \Log::error('www');
            // ------------------------------------------------------------------
            // Release lock
            // ------------------------------------------------------------------
            cache()->forget('access_sync_running');
        }
    }

    protected function isDateTime($value)
    {
        if (!is_string($value))
            return false;

        $formats = [
            'Y-m-d H:i:s',
            'Y-m-d',
            'm/d/Y',
            'm/d/Y H:i:s',
            'd-m-Y',
            'd/m/Y',
        ];

        foreach ($formats as $format) {
            $parsed = \DateTime::createFromFormat($format, $value);
            if ($parsed && $parsed->format($format) === $value) {
                return true;
            }
        }

        return false;
    }
}
