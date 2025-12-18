<?php

namespace App\Jobs;

use App\Models\UserIdSyncLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProcessUserIdsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $rows;

    public function __construct(array $rows)
    {
        $this->rows = $rows;
    }

    public function handle()
    {
        $endpoint = rtrim(config('api_url.endpoint'), '/') . '/user_id_store';

        foreach (array_chunk($this->rows, 500) as $chunk) {

            // 👉 Send FULL rows (not only USERID)
            $payload = $chunk;

            try {
                $response = Http::retry(3, 500)
                    ->timeout(40)
                    ->withHeaders([
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json'
                    ])
                    ->post($endpoint, ['records' => $payload]);

                $status = $response->status();
                $data = $response->json();

                UserIdSyncLog::create([
                    'chunk_size' => count($payload),
                    'success' => $response->successful(),

                    'inserted_count' => $data['inserted_count'] ?? 0,
                    'errors_count' => $data['errors_count'] ?? 0,

                    'duplicate_existing_before' =>
                        $data['duplicate_existing_before'] ?? [],

                    'duplicate_from_race_condition' =>
                        $data['duplicate_from_race_condition'] ?? [],

                    'sent_payload' => $payload,
                    'api_raw_response' => $data,
                    'response_status' => $status,
                ]);

            } catch (\Exception $e) {
                UserIdSyncLog::create([
                    'chunk_size' => count($chunk),
                    'success' => false,
                    'inserted_count' => 0,
                    'errors_count' => count($chunk),
                    'sent_payload' => $payload,
                    'api_raw_response' => ['error' => $e->getMessage()],
                    'response_status' => null,
                ]);

                Log::error("ProcessUserIdsJob Exception: " . $e->getMessage());
            }

            usleep(200000); // 0.2 sec
        }
    }
}

