<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ScheduledSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class AccessConfigController extends Controller
{
    public function config(Request $request)
    {
           \Log::info('QQQ');

        $settings = ScheduledSetting::first();

        if (!$settings || !$settings->db_location) {
            return response()->json([
                'enabled' => false
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

        if (!$accessFile || !file_exists($accessFile)) {

            \Log::error('kkk-Access DB file not found: ' . $accessFile);
            return response()->json([
                'enabled' => false
            ]);
        }

        return response()->json([
            'enabled' => true,
            'db_location' => $accessFile,
        ]);
    }
}
