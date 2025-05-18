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
        public string $table,  // e.g. "orders"
        public array $columns  // e.g. ["id","total"]
    ) {}

    public function handle(): void
    {
        // --- very important: validate/whitelist to avoid SQL‑inject --------
        $allowedTables = config('snapshot.tables');
        $allowedColumns = config('snapshot.columns')[$this->table] ?? [];

        abort_unless(in_array($this->table, $allowedTables), 400);
        abort_unless(collect($this->columns)
            ->every(fn($c) => in_array($c, $allowedColumns)), 400);
        // ------------------------------------------------------------------

        $payload = DB::table($this->table)
            ->select($this->columns)
            ->get()
            ->toJson();

        Http::post('https://other-server.example/api/endpoint', [
            'data' => $payload,
            'table' => $this->table,
            'columns' => $this->columns,
        ]);
    }
       
}
