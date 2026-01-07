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

class SyncAccessToMySQL2 extends Command
{
    // protected $description = 'Sync data from MS Access to MySQL and prepare formatted student data';

    protected $signature = 'access:sync';

    public function handle()
    {
        // ------------------------------------------------------------------
        // Prevent overlapping runs (Windows Task Scheduler safe)
        // ------------------------------------------------------------------
        if (cache()->has('access_sync_running')) {
            \Log::warning('access:sync already running, skipped');
            return Command::SUCCESS;
        }

        cache()->put('access_sync_running', true, 600);  // lock for 10 minutes

        try {
            // ------------------------------------------------------------------
            // Load settings (single-row table design)
            // ------------------------------------------------------------------
            $settings = ScheduledSetting::first();

            if (!$settings) {
                \Log::error('scheduled_settings row not found');
                return Command::SUCCESS;
            }

            $syncTime = (int) ($settings->value ?? 1);  // 1–7
            if ($syncTime < 1 || $syncTime > 7) {
                $syncTime = 1;
            }

            $lastSync = $settings->last_sync;
            $now = now();

            // ------------------------------------------------------------------
            // Decide whether sync should run
            // ------------------------------------------------------------------
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

            \Log::info('access:sync EXECUTING');

            // ------------------------------------------------------------------
            // Validate Access DB file
            // ------------------------------------------------------------------
            $accessFile = $settings->db_location;

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
            // Sync tables
            // ------------------------------------------------------------------
            $tables = ['USERINFO', 'CHECKINOUT'];

            foreach ($tables as $table) {
                $stmt = $pdo->query("SELECT * FROM {$table}");
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (empty($data)) {
                    continue;
                }

                $tableName = strtolower($table);
                $sampleRow = $data[0];

                if (!Schema::hasTable($tableName)) {
                    Schema::create($tableName, function (Blueprint $table) use ($sampleRow) {
                        $table->increments('id');
                        foreach ($sampleRow as $col => $value) {
                            if (is_numeric($value) && !str_contains((string) $value, '.')) {
                                $table->integer($col)->nullable();
                            } elseif (is_numeric($value)) {
                                $table->float($col)->nullable();
                            } elseif ($this->isDateTime($value)) {
                                $table->dateTime($col)->nullable();
                            } elseif (strlen((string) $value) > 255) {
                                $table->text($col)->nullable();
                            } else {
                                $table->string($col, 255)->nullable();
                            }
                        }
                        $table->timestamps();
                    });
                }

                foreach ($data as $row) {
                    foreach ($row as $k => $v) {
                        if ($v === '') {
                            $row[$k] = null;
                        }
                    }

                    if ($tableName === 'userinfo') {
                        DB::table($tableName)->updateOrInsert(
                            ['USERID' => $row['USERID']],
                            $row
                        );
                    }

                    if ($tableName === 'checkinout') {
                        DB::table($tableName)->updateOrInsert(
                            ['LOGID' => $row['LOGID']],
                            $row
                        );
                    }
                }
            }

            // ------------------------------------------------------------------
            // Push formatted data to API
            // ------------------------------------------------------------------
            $checkins = DB::table('checkinout as c')
                ->join('userinfo as u', 'c.USERID', '=', 'u.USERID')
                ->select(
                    'u.USERID as id',
                    DB::raw('MIN(c.CHECKTIME) as in_time'),
                    DB::raw('MAX(c.CHECKTIME) as out_time'),
                    'c.MachineId'
                )
                ->groupBy('u.USERID', DB::raw('DATE(c.CHECKTIME)'), 'c.MachineId')
                ->get();

            $studentData = [];

            foreach ($checkins as $row) {
                $studentData[] = [
                    'id' => $row->id,
                    'machine_id' => $row->MachineId,
                    'date' => Carbon::parse($row->in_time)->format('Y-m-d'),
                    'in_time' => Carbon::parse($row->in_time)->format('h:i A'),
                    'out_time' => Carbon::parse($row->out_time)->format('h:i A'),
                ];
            }

            Http::withOptions(['verify' => false])
                ->acceptJson()
                ->post(config('api_url.endpoint') . '/accessBdStore', [
                    'studentData' => $studentData
                ]);

            // ------------------------------------------------------------------
            // Update last_sync
            // ------------------------------------------------------------------
            $settings->update([
                'last_sync' => now(),
            ]);

            // \Log::info('✅ access:sync COMPLETED at ' . now());

            return Command::SUCCESS;
        } finally {
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
