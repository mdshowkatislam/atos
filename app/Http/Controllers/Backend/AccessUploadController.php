<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class AccessUploadController extends Controller
{
    public function upload(Request $request)
    {
        \Log::info('AAA');
        $request->validate([
            'mdb_file' => 'required|file'
        ]);

        // 1️⃣ Save uploaded MDB to server temp storage
        $path = $request->file('mdb_file')->storeAs(
            'access',
            'incoming.mdb'
        );

        $fullPath = storage_path('app/' . $path);

        // 2️⃣ Run sync command WITH TEMP FILE
        Artisan::call('access:sync', [
            '--file' => $fullPath
        ]);

        return response()->json([
            'status' => 'ok'
        ]);
    }
}
