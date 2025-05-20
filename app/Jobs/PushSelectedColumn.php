<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class PushSelectedColumn implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct(
        public string $table,
        public array $columns
    ) {}

    public function handle(): void
    {
        \Log::info('1log');

        $allowedTables = config('snapshot.tables');
        $allowedColumns = config('snapshot.columns')[$this->table] ?? [];

        if (!in_array($this->table, $allowedTables)) {
            \Log::warning("Table {$this->table} is not whitelisted", compact('allowedTables'));
            return;
        }

        $invalid = collect($this->columns)->reject(fn($c) => in_array($c, $allowedColumns));
        if ($invalid->isNotEmpty()) {
            \Log::warning('Columns not allowed', [
                'table' => $this->table,
                'invalidCols' => $invalid->values(),
                'allowedCols' => $allowedColumns,
            ]);
            return;
        }

        \Log::info('invalid', compact('invalid'));
        $payload = DB::table($this->table)
            ->select($this->columns)
            ->get()
            ->map(function ($row) {
                return collect([
                    'uid' => $row->USERID ?? null,
                    'machine_id' => $row->MachineId ?? null,
                    'time' => $row->created_at ?? null,
                ])
                    ->filter(fn($v) => !is_null($v))
                    ->all();
            })
            ->toArray();

        \Log::info('2log');
        \Log::info($payload);
        
        Http::post(config('snapshot.endpoint'), [
            'data' => $payload,
            'studentData' => $this->table
        ]);

    }
}
