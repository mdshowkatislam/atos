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
    protected $description = 'Sync data from MS Access to MySQL and prepare formatted student data';

    protected $signature = 'access:sync';

    public function handle()
    {

     
        $accessFile = ScheduledSetting::value('db_location');

        $dsn = "odbc:Driver={Microsoft Access Driver (*.mdb, *.accdb)};Dbq=$accessFile;";

        try {
            $pdo = new PDO($dsn);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

            $tables = ['USERINFO', 'CHECKINOUT'];

            foreach ($tables as $table) {
                $stmt = $pdo->query("SELECT * FROM $table");
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $this->info('Fetched ' . count($data) . " rows from $table");

                if (empty($data)) {
                    $this->warn("No data found in $table. Skipping table creation and insertion.");
                    continue;
                }

                $lowerTableName = strtolower($table);
                $sampleRow = $data[0];

                if (!Schema::hasTable($lowerTableName)) {
                    Schema::create($lowerTableName, function (Blueprint $table) use ($sampleRow) {
                        $table->increments('id');

                        foreach ($sampleRow as $col => $value) {
                            if ($col === 'id') continue;
                            if (is_numeric($value) && !str_contains((string) $value, '.')) {
                                $table->integer($col)->nullable();
                            } elseif (is_numeric($value)) {
                                $table->float($col)->nullable();
                            } elseif ($this->isDateTime($value)) {
                                $table->dateTime($col)->nullable();
                            } elseif (strlen($value) > 255) {
                                $table->text($col)->nullable();
                            } else {
                                $table->string($col, 255)->nullable();
                            }
                        }

                        $table->timestamps();
                    });

                    $this->info("Created table: $lowerTableName");
                }

           
                foreach ($data as $row) {
                    foreach ($row as $key => $value) {
                        if ($value === '') {
                            $row[$key] = null;
                        }
                    }

                    try {
                        if ($lowerTableName === 'userinfo') {
                            DB::table($lowerTableName)->updateOrInsert(
                                ['Badgenumber' => $row['Badgenumber']],
                                // ['USERID' => $row['USERID']],
                                $row
                            );
                        } elseif ($lowerTableName === 'checkinout') {
                            DB::table($lowerTableName)->updateOrInsert(
                                ['LOGID' => $row['LOGID']],
                                $row
                            );
                        }
                    } catch (\Exception $e) {
                        $this->error("Insert failed for $lowerTableName: " . $e->getMessage());
                        $this->error('Data: ' . json_encode($row));
                    }
                }

                $this->info("Inserted into $lowerTableName");
            }

            $today = Carbon::today()->toDateString();

            $checkins = DB::table('checkinout as c')
                ->join('userinfo as u', 'c.USERID', '=', 'u.USERID')
                ->select(
                    'u.Badgenumber as id',
                    DB::raw('MIN(c.CHECKTIME) as in_time'),
                    DB::raw('MAX(c.CHECKTIME) as out_time'),
                    'c.MachineId'
                )
                // ->whereDate('c.CHECKTIME', $today)
                ->groupBy('u.Badgenumber', 'c.MachineId')
                ->get();

            $studentData = [];

            foreach ($checkins as $checkin) {
                $studentData[] = [
                    'id' => $checkin->id,
                    'machine_id' => $checkin->MachineId,
                    'in_time' => Carbon::parse($checkin->in_time)->format('h:i A'),
                    'out_time' => Carbon::parse($checkin->out_time)->format('h:i A'),
                ];
            }

            \Log::info('Formatted studentData:', $studentData);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'accept' => 'application/json',
            ])
                ->withOptions(['verify' => false])
                ->post(config('api_url.endpoint'), ['studentData' => $studentData]);

            \Log::info('API Response:', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

        } catch (\PDOException $e) {
            $this->error('Connection failed: ' . $e->getMessage());
        }
    }

    protected function isDateTime($value)
    {
        if (!is_string($value)) return false;

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
