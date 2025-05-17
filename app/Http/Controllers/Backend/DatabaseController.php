<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ScheduledSetting;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;

class DatabaseController extends Controller
{
    public function index()
    {
        $databases = \DB::select("
        SELECT DISTINCT TABLE_SCHEMA
        FROM INFORMATION_SCHEMA.TABLES
        WHERE TABLE_NAME = 'checkinout'
    ");

        $databaseNames = array_map(function ($db) {
            return $db->TABLE_SCHEMA;
        }, $databases);

        // $allTables = [];
        // foreach ($databaseNames as $dbName) {
        //     $tables = \DB::select("
        //         SELECT TABLE_NAME
        //         FROM INFORMATION_SCHEMA.TABLES
        //         WHERE TABLE_SCHEMA = ?
        //     ", [$dbName]);

        //     $allTables[$dbName] = array_map(function ($table) {
        //         return $table->TABLE_NAME;
        //     }, $tables);
        // }

        // // Debug output
        // dd($allTables);

        return view('backend.database.index', compact('databaseNames'));
    }

    public function updateSchedule(Request $request)
    {
        $validated = $request->validate([
            'location' => 'nullable|string',
            'syncTimeName' => 'required|in:1,2,3,4,5,6,7',
        ]);

        ScheduledSetting::updateOrCreate(
            ['key' => 'sync_time'],
            [
                'value' => $validated['syncTimeName'],
                'db_location' => $validated['location'] ?? null,
            ]
        );

        return redirect()->back()->with('success', 'Schedule and DB location updated!');
    }

    public function show($db)
    {
        try {
            $allTables = [];

            $tables = \DB::select('
                SELECT TABLE_NAME
                FROM INFORMATION_SCHEMA.TABLES
                WHERE TABLE_SCHEMA = ?
            ', [$db]);

            $allTables[$db] = array_map(function ($table) {
                return $table->TABLE_NAME;
            }, $tables);
            return response()->json($allTables);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Could not fetch columns.(From Controller)'], 500);
        }
        //  $columns = \DB::select('SELECT COLUMN_NAME, DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ?', [$db]);
        // return response()->json($columns);
    }

    public function showTable()
    {
        try {
            
            // $data = \DB::select('SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = ? ', ['atos']);

            $data = \DB::select('SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = ? 
            
            AND TABLE_NAME IN (?,?)', ['atos', 'checkinout', 'userinfo']);  // $data = \DB::select('SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = ? ', ['atos']);
     

            

        } catch (QueryException $e) {
            dd($e, $e->getMessage());
            return redirect()->back()->with('error', 'Could not fetch columns.(From Controller)');
        }
        // dd($data);
        return view('backend.database.table', compact('data'));
    }
}
