<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ScheduledSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AccessConfigController extends Controller
{
    public function config(Request $request)
    {
        //    Log::info('QQQ');

        $settings = ScheduledSetting::first();
        
           Log::info('dbLocation: ' . ($settings ? $settings->db_location : 'NULL')    );

        if (!$settings || !$settings->db_location) {
            return response()->json([
                'enabled' => false,
                'message' => 'Database location not configured, is atos system'
            ]);
        }
        
        // Accept uploaded file via HTTP POST (field: mdb_file)
        if ($request->hasFile('mdb_file') && $request->file('mdb_file')->isValid()) {
            $file = $request->file('mdb_file');
            $destDir = storage_path('app/access');
            if (!file_exists($destDir)) {
                mkdir($destDir, 0755, true);
            }
            $destPath = $destDir . DIRECTORY_SEPARATOR . $file->getClientOriginalName();
            $file->move($destDir, $file->getClientOriginalName());
            $accessFile = $destPath;
        } else {
            // 🔁 FALLBACK (legacy/manual run)
            $accessFile = $settings->db_location;
        }

        // NOTE: when a remote client (Windows machine) calls this endpoint
        // we should not require the server to have the same Windows path.
        // Only validate physical existence when an uploaded file is provided.
        if ($request->hasFile('mdb_file')) {
            if (!$accessFile || !file_exists($accessFile)) {
                Log::error('kkk-Access DB file not found after upload: ' . $accessFile);
                return response()->json([
                    'enabled' => false
                ]);
            }
        }

        // indicate whether the server itself has direct access to the file
        $serverHasFile = is_string($settings->db_location) && file_exists($settings->db_location);

        // determine source hint: if client uploaded in this request -> server,
        // otherwise if server physically has the file -> server, else client
        $source = $request->hasFile('mdb_file') ? 'server' : ($serverHasFile ? 'server' : 'client');

        return response()->json([
            'enabled' => true,
            'db_location' => $accessFile,
            'server_has_file' => $serverHasFile,
            'source' => $source,
        ]);
    }
}


