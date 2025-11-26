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

    protected array $userIds;

    public function __construct(array $userIds)
    {
        $this->userIds = $userIds;
    }

    public function handle()
    {
        $endpoint = rtrim(config('api_url.endpoint'), '/') . '/user_id_store';

        foreach (array_chunk($this->userIds, 500) as $chunk) {
            
            // Prepare payload
            $payload = array_map(fn($id) => ['profile_id' => (int)$id], $chunk);

            try {
                $response = Http::retry(3, 500)
                    ->timeout(40)
                    ->withHeaders([
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json'
                    ])
                    ->post($endpoint, ['profileIds' => $payload]);

                $status = $response->status();
                $data = $response->json();

                // Save log
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

                // Log a failed sync attempt
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

            // Avoid stress
            usleep(200000);
        }
    }
}
