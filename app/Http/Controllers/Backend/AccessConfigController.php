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
           \Log::info('AAA2');

        $settings = ScheduledSetting::first();

        if (!$settings || !$settings->db_location) {
            return response()->json([
                'enabled' => false
            ]);
        }
        $uploadedFile = $this->option('file');

        if ($uploadedFile && file_exists($uploadedFile)) {
            // 🔁 FILE COMES FROM LOCAL PC UPLOAD
            $accessFile = $uploadedFile;
        } else {
            // 🔁 FALLBACK (legacy/manual run)
            $accessFile = $settings->db_location;
        }

        if (!$accessFile || !file_exists($accessFile)) {
            \Log::error('Access DB file not found: ' . $accessFile);
            return response()->json([
                'enabled' => false
            ]);
        }

        return response()->json([
            'enabled' => true,
            'db_location' => $settings->db_location,
        ]);
    }
}
