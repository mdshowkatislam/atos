<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class ProcessAccessSync implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;

    /**
     * Create a new job instance.
     */
    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('Processing access sync job', ['file_path' => $this->filePath]);

            // Call the artisan command
            Artisan::call('access:sync', [
                '--file' => $this->filePath
            ]);

            Log::info('Access sync completed successfully', ['file_path' => $this->filePath]);
        } catch (\Exception $e) {
            Log::error('Failed to process access sync', [
                'error' => $e->getMessage(),
                'file_path' => $this->filePath
            ]);
            throw $e;
        }
    }
}
