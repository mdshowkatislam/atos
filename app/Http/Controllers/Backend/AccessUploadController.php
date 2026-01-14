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
            } else {
                // default to preserving legacy behaviour for MDB uploads
                $file->move($destDir, 'incoming.mdb');
                $fullPath = $destDir . DIRECTORY_SEPARATOR . 'incoming.mdb';
            }
            
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
        // Detect CLI PHP binary but avoid selecting php-fpm (common on FPM workers)
        $phpBinary = defined('PHP_BINARY') ? PHP_BINARY : null;
        if ($phpBinary) {
            $base = basename($phpBinary);
            if (stripos($base, 'php-fpm') !== false || stripos($phpBinary, DIRECTORY_SEPARATOR . 'sbin' . DIRECTORY_SEPARATOR) !== false) {
                // PHP_BINARY points to php-fpm — ignore it and try to find CLI php
                $phpBinary = null;
            }
        }

        if (!$phpBinary || !is_executable($phpBinary)) {
            // Common locations for CLI php
            $possiblePaths = [
                '/usr/bin/php',
                '/usr/local/bin/php',
                '/opt/cpanel/ea-php83/root/usr/bin/php',
                '/opt/cpanel/ea-php82/root/usr/bin/php',
                '/opt/cpanel/ea-php83/root/usr/sbin/php',
                '/opt/cpanel/ea-php82/root/usr/sbin/php',
            ];
            foreach ($possiblePaths as $path) {
                if (is_executable($path) && stripos(basename($path), 'php') !== false && stripos(basename($path), 'php-fpm') === false) {
                    $phpBinary = $path;
                    break;
                }
            }
        }

        if (!$phpBinary) {
            // Fallback to `php` in PATH — usually the CLI binary
            $phpBinary = 'php';
        }

        // Safety: ensure we didn't accidentally pick php-fpm
        if (stripos(basename($phpBinary), 'php-fpm') !== false) {
            $phpBinary = 'php';
        }

        $artisanPath = base_path('artisan');

        // Log output to a file instead of /dev/null
        $logFile = storage_path('logs/sync_output.log');

        $cmd = escapeshellarg($phpBinary) . ' ' .
               escapeshellarg($artisanPath) . ' access:sync --file=' .
               escapeshellarg($filePath) . ' >> ' .
               escapeshellarg($logFile) . ' 2>&1 &';

        Log::info('Starting background sync with popen', [
            'command' => $cmd,
            'file_path' => $filePath
        ]);

        $handle = popen($cmd, 'r');
        if ($handle) {
            Log::info('Background sync started with popen');
            pclose($handle);
            $this->storeProcessInfo(0, $filePath);
        } else {
            Log::error('Failed to start process with popen');
        }

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
}