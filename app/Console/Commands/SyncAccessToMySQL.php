<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use PDO;

class SyncAccessToMySQL extends Command
{
    protected $description = 'Command description';

    protected $signature = 'access:sync';

    public function handle()
    {
        $accessFile = 'C:\ZKTeco\ZKAccess3.5\Access.mdb';

        $dsn = "odbc:Driver={Microsoft Access Driver (*.mdb, *.accdb)};Dbq=$accessFile;";

        try {
            $pdo = new \PDO($dsn);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);

            $tables = ['USERINFO', 'CHECKINOUT'];

            foreach ($tables as $table) {
                $stmt = $pdo->query("SELECT * FROM $table");
                $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                $this->info('Fetched ' . count($data) . " rows from $table");

                if (empty($data)) {
                    $this->warn("No data found in $table. Skipping table creation and insertion.");
                    continue;
                }

                $lowerTableName = strtolower($table);
                $sampleRow = $data[0];

                // Create table if it doesn't exist
                if (!Schema::hasTable($lowerTableName)) {
                    Schema::create($lowerTableName, function (Blueprint $table) use ($sampleRow) {
                        $table->increments('id');

                        foreach ($sampleRow as $col => $value) {
                            if ($col === 'id')
                                continue;
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

                // Insert or update data
                foreach ($data as $row) {
                    if ($lowerTableName === 'userinfo') {
                        try {
                            foreach ($row as $key => $value) {
                                if ($value === '') {
                                    $row[$key] = null;
                                }
                            }
                            DB::table($lowerTableName)->updateOrInsert(
                                ['Badgenumber' => $row['Badgenumber']],
                                $row
                            );
                        } catch (\Exception $e) {
                            $this->error('Insert failed for USERINFO: ' . $e->getMessage());
                            $this->error('Data: ' . json_encode($row));
                        }
                    } elseif ($lowerTableName === 'checkinout') {
                        try {
                            foreach ($row as $key => $value) {
                                if ($value === '') {
                                    $row[$key] = null;
                                }
                            }
                            DB::table($lowerTableName)->updateOrInsert(
                                ['LOGID' => $row['LOGID']],
                                $row
                            );
                        } catch (\Exception $e) {
                            $this->error('Insert failed for USERINFO: ' . $e->getMessage());
                            $this->error('Data: ' . json_encode($row));
                        }
                    }
                }

                $this->info("Inserted into $lowerTableName");
            }
        } catch (\PDOException $e) {
            $this->error('Connection failed: ' . $e->getMessage());
        }
    }

    // Helper method to detect datetime strings
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
