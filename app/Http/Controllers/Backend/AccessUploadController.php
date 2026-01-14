<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AccessUploadController extends Controller
{
    private $apiKey = 'zk-sync-2026';
    
    public function upload(Request $request)
    {
        Log::info("UUUU1");
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
        
        Log::info('MDB Upload started', [
            'name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'ip' => $request->ip()
        ]);

        try {
            // Save uploaded MDB to server storage
            $file->storeAs('access', 'incoming.mdb');
            $fullPath = storage_path('app/access/incoming.mdb');
            
            $fileSize = filesize($fullPath);
            Log::info('File saved successfully', [
                'path' => $fullPath,
                'size' => $fileSize
            ]);

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
     * Run sync command in background using popen() (which is enabled)
     */
 private function runSyncInBackground($filePath)
{
    try {
        // Detect CLI PHP binary
        $phpBinary = trim(shell_exec('which php'));
        if (!$phpBinary || !is_executable($phpBinary)) {
            // Fallbacks for cPanel environments
            $possiblePaths = [
                '/usr/bin/php',
                '/opt/cpanel/ea-php83/root/usr/bin/php',
                '/opt/cpanel/ea-php82/root/usr/bin/php',
            ];
            foreach ($possiblePaths as $path) {
                if (is_executable($path)) {
                    $phpBinary = $path;
                    break;
                }
            }
        }

        if (!$phpBinary) {
            $phpBinary = 'php'; // last fallback
        }

        $artisanPath = base_path('artisan');

        // Log output to a file instead of /dev/null
        $logFile = storage_path('logs/sync_output.log');

        $cmd = escapeshellarg($phpBinary) . ' ' .
               escapeshellarg($artisanPath) . ' access:sync --file=' .
               escapeshellarg($filePath) . ' >> ' .
               escapeshellarg($logFile) . ' 2>&1 &';

        \Log::info('Starting background sync with popen', [
            'command' => $cmd,
            'file_path' => $filePath
        ]);

        $handle = popen($cmd, 'r');
        if ($handle) {
            \Log::info('Background sync started with popen');
            pclose($handle);
            $this->storeProcessInfo(0, $filePath);
        } else {
            \Log::error('Failed to start process with popen');
        }

    } catch (\Exception $e) {
        \Log::error('Failed to start background sync', [
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
}