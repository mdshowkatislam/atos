<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AccessUploadControllerOld extends Controller
{
    private $apiKey = 'zk-sync-2026';
    
    public function upload(Request $request)
    {
        // Log::info("UUUU1");
        // Simple API key check
        $authHeader = $request->header('Authorization');
        if (!$authHeader || $authHeader !== 'Bearer ' . $this->apiKey) {
            Log::error('Invalid API key', ['header' => $authHeader]);
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Check file
        if (!$request->hasFile('mdb_file')) {
            Log::error('No file uploaded');
            return response()->json(['error' => 'No file uploaded'], 400);
        }

        $file = $request->file('mdb_file');

        try {
            // Save uploaded file to server storage
            $originalExt = strtolower($file->getClientOriginalExtension() ?? '');
            $isSql = in_array($originalExt, ['sql', 'txt']) || str_contains($file->getClientMimeType() ?? '', 'sql');

            // Save into storage/app/public/access so public storage path is used
            $destDir = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'access');
            if (!file_exists($destDir)) {
                mkdir($destDir, 0755, true);
            }

            if ($isSql) {
                $file->move($destDir, 'incoming.sql');
                $fullPath = $destDir . DIRECTORY_SEPARATOR . 'incoming.sql';
                // Log::info("FILE MOVED TO: " . $fullPath);
            } else {
                // default to preserving legacy behaviour for MDB uploads
                $file->move($destDir, 'incoming.mdb');
                $fullPath = $destDir . DIRECTORY_SEPARATOR . 'incoming.mdb';
                // Log::info("FILE MOVED TO: " . $fullPath);
            }
            
            $fileSize = filesize($fullPath);
            // Log::info('File saved successfully', [
            //     'path' => $fullPath,
            //     'size' => $fileSize
            // ]);

            // ✅ Run sync command IN BACKGROUND using popen() with & at the end
            $this->runSyncInBackground($fullPath);
            
            // Return response IMMEDIATELY
            return response()->json([
                'status' => 'ok',
                'message' => 'File uploaded. Sync started in background.',
                'file_size' => $fileSize,
                'timestamp' => date('Y-m-d H:i:s')
            ]);

        } catch (\Exception $e) {
            Log::error('Upload failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Run sync command in background using exec() with nohup (which is enabled)
     */
    private function runSyncInBackground($filePath)
    {
        try {
            $projectRoot = base_path();
            $logFile = storage_path('logs/sync_output.log');

            // Try to find PHP 8.2+ binary
            $php82Paths = [
                '/opt/cpanel/ea-php82/root/usr/bin/php',
                '/opt/cpanel/ea-php83/root/usr/bin/php',
                '/opt/cpanel/ea-php84/root/usr/bin/php',
                '/usr/local/bin/php82',
                '/usr/bin/php82',
            ];

            $phpBinary = 'php'; // fallback
            foreach ($php82Paths as $path) {
                if (is_executable($path)) {
                    $phpBinary = $path;
                    break;
                }
            }

            // Change to project directory first, then run artisan command
            $cmd = 'cd ' . escapeshellarg($projectRoot) . ' && nohup ' . escapeshellarg($phpBinary) . ' artisan access:sync --file=' .
                   escapeshellarg($filePath) . ' >> ' .
                   escapeshellarg($logFile) . ' 2>&1 &';

            Log::info('Starting background sync with nohup exec', [
                'command' => $cmd,
                'file_path' => $filePath,
                'project_root' => $projectRoot,
                'php_binary' => $phpBinary
            ]);

            // Use exec() to run command in background
            \exec($cmd, $output, $return_var);

            // Log::info('Background sync exec result', [
            //     'return_code' => $return_var,
            //     'output' => implode("\n", $output)
            // ]);

            // Log::info('Background sync started with nohup');

            $this->storeProcessInfo(0, $filePath);

        } catch (\Exception $e) {
            Log::error('Failed to start background sync', [
                'error' => $e->getMessage(),
                'file_path' => $filePath
            ]);
        }
    }


    
    /**
     * Store process info for tracking
     */
    private function storeProcessInfo($pid, $filePath)
    {
        $infoFile = storage_path('app/access/sync_info.json');
        $info = [
            'pid' => $pid,
            'file' => basename($filePath),
            'started_at' => date('Y-m-d H:i:s'),
            'file_size' => filesize($filePath)
        ];
        
        file_put_contents($infoFile, json_encode($info, JSON_PRETTY_PRINT));
    }

    /**
     * Test endpoint - Check disabled functions
     */
    public function testDisabledFunctions()
    {
        $disabled = ini_get('disable_functions');
        return response()->json([
            'disabled_functions' => $disabled ?: 'None disabled',
            'popen_enabled' => function_exists('popen') ? 'Yes' : 'No',
            'proc_open_enabled' => function_exists('proc_open') ? 'Yes' : 'No',
            'shell_exec_enabled' => function_exists('shell_exec') ? 'Yes' : 'No',
            'exec_enabled' => function_exists('exec') ? 'Yes' : 'No',
        ]);
    }

    /**
     * Test endpoint - Test artisan command directly
     */
    public function testArtisanCommand()
    {
        $projectRoot = base_path();
        $testFile = storage_path('app/public/access/incoming.sql');
        
        // Try to find PHP 8.2+ binary
        $php82Paths = [
            '/opt/cpanel/ea-php82/root/usr/bin/php',
            '/opt/cpanel/ea-php83/root/usr/bin/php',
            '/opt/cpanel/ea-php84/root/usr/bin/php',
            '/usr/local/bin/php82',
            '/usr/bin/php82',
        ];

        $phpBinary = 'php'; // fallback
        foreach ($php82Paths as $path) {
            if (is_executable($path)) {
                $phpBinary = $path;
                break;
            }
        }
        
        // Log::info('Testing artisan command', [
        //     'project_root' => $projectRoot,
        //     'test_file' => $testFile,
        //     'file_exists' => file_exists($testFile),
        //     'php_binary' => $phpBinary
        // ]);

        $cmd = 'cd ' . escapeshellarg($projectRoot) . ' && ' . escapeshellarg($phpBinary) . ' artisan access:sync --file=' .
               escapeshellarg($testFile);

        // Log::info('Test command: ' . $cmd);

        \exec($cmd . ' 2>&1', $output, $return_var);

        return response()->json([
            'command' => $cmd,
            'return_code' => $return_var,
            'output' => implode("\n", $output),
            'project_root' => $projectRoot,
            'php_binary' => $phpBinary,
            'php_binary_exists' => is_executable($phpBinary),
            'test_file_exists' => file_exists($testFile)
        ]);
    }

    /**
     * Test endpoint - Check cURL status
     */
    public function testCurlStatus()
    {
        $curlEnabled = extension_loaded('curl');
        $curlVersion = null;
        $curlInfo = null;

        if ($curlEnabled && function_exists('curl_version')) {
            $curlVersion = curl_version();
            $curlInfo = [
                'version' => $curlVersion['version'] ?? 'Unknown',
                'ssl_version' => $curlVersion['ssl_version_number'] ?? 'Unknown',
                'host' => $curlVersion['host'] ?? 'Unknown',
            ];
        }

        return response()->json([
            'curl_enabled' => $curlEnabled ? 'Yes' : 'No',
            'curl_extension_loaded' => extension_loaded('curl') ? 'Yes' : 'No',
            'curl_function_exists' => function_exists('curl_init') ? 'Yes' : 'No',
            'curl_info' => $curlInfo,
            'allow_url_fopen' => ini_get('allow_url_fopen') ? 'Yes' : 'No',
            'disabled_functions' => ini_get('disable_functions') ?: 'None',
        ]);
    }

    /**
     * Process queued API pushes (runs in web environment with cURL)
     */
    public function processApiQueue()
    {
        $endpoint = config('api_url.endpoint') . '/accessBdStore';

        // Log::info('API Queue Processor Started', [
        //     'endpoint' => $endpoint,
        //     'curl_available' => function_exists('curl_init') ? 'YES' : 'NO',
        // ]);

        // Get pending items from queue
        $pendingItems = \DB::table('api_push_queue')
            ->where('status', 'pending')
            ->orWhere(function($q) {
                $q->where('status', 'failed')->where('retry_count', '<', 3);
            })
            ->limit(50)
            ->get();

        if ($pendingItems->isEmpty()) {
            Log::info('No pending items in queue');
            return response()->json(['status' => 'ok', 'processed' => 0]);
        }

        $processed = 0;
        $failed = 0;

        foreach ($pendingItems as $item) {
            try {
                $data = json_decode($item->student_data, true);

                // Log::info('Processing queue item', [
                //     'id' => $item->id,
                //     'records' => count($data['studentData'] ?? []),
                // ]);

                $response = \Illuminate\Support\Facades\Http::timeout(30)
                    ->withOptions(['verify' => false])
                    ->post($endpoint, $data);

                if ($response->successful()) {
                    \DB::table('api_push_queue')
                        ->where('id', $item->id)
                        ->update([
                            'status' => 'sent',
                            'updated_at' => now(),
                        ]);

                    // Log::info('Queue item sent successfully', [
                    //     'id' => $item->id,
                    //     'status' => $response->status(),
                    // ]);

                    $processed++;
                } else {
                    throw new \Exception('HTTP ' . $response->status());
                }
            } catch (\Throwable $e) {
                $retryCount = $item->retry_count + 1;

                \DB::table('api_push_queue')
                    ->where('id', $item->id)
                    ->update([
                        'status' => 'failed',
                        'retry_count' => $retryCount,
                        'last_error' => $e->getMessage(),
                        'updated_at' => now(),
                    ]);

                Log::error('Queue item failed', [
                    'id' => $item->id,
                    'error' => $e->getMessage(),
                    'retry_count' => $retryCount,
                ]);

                $failed++;
            }
        }

        return response()->json([
            'status' => 'ok',
            'processed' => $processed,
            'failed' => $failed,
            'total_items' => count($pendingItems),
        ]);
    }
}