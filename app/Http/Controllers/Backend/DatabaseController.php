<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessUserIdsJob;
use App\Models\ScheduledSetting;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        // dd($databases);

        return view('backend.database.index', compact('databaseNames'));
    }

    public function updateSchedule(Request $request)
    {
        Log::info('updatehit');
        $isApi = $request->route() && str_starts_with($request->route()->getPrefix(), 'api');

        if ($isApi) {
            // Validate inside 'dbdata' key
            $validated = validator($request->input('dbdata', []), [
                'location' => 'nullable|string',
                'syncTimeName' => 'required|in:1,2,3,4,5,6,7',
            ])->validate();

            ScheduledSetting::updateOrCreate(
                ['key' => 'sync_time'],
                [
                    'value' => $validated['syncTimeName'],
                    'db_location' => $validated['location'] ?? null,
                ]
            );
        } else {
            // Validate flat keys directly
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
        }

        return redirect()->back()->with('success', 'Schedule and DB location updated!');
    }

    public function showColumn($table)
    {
        try {
            $allTables = [];

            $tables = DB::select('
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
        Log::info('YYYY');
        try {
            // $data = DB::select('SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = ? ', ['atos']);

            $data = DB::select('SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = ? 
            
            AND TABLE_NAME IN (?,?)', ['bidyapith_jmagc_atos', 'checkinout', 'userinfo']);  // $data = \DB::select('SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = ? ', ['atos']);
            $result = [];

            foreach ($data as $table) {
                $columns = DB::select("SHOW COLUMNS FROM `$table->TABLE_NAME`");
                $result[] = [
                    'name' => $table->TABLE_NAME,
                    'columns' => array_map(fn($col) => $col->Field, $columns),
                ];
            }
        } catch (QueryException $e) {
            Log::error('API push failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            dd($e, $e->getMessage());
            return redirect()->back()->with('error', 'Could not fetch columns.(From Controller)');
        }

        // dd($result);
        // dd($result[0]['name']);

        return view('backend.database.table', compact('result'));
    }

    public function showSelected(Request $request)
    {
        $table = $request->input('table');
        $columns = $request->input('columns') ?? [];

        // Check if table contains ID column
        $hasId = \Schema::hasColumn($table, 'id');

        if ($hasId && !in_array('id', $columns)) {
            array_unshift($columns, 'id');
        }

        if (empty($columns)) {
            return redirect()->back()->withErrors('Select at least one column.');
        }

        // Fetch paginated data
        $data = DB::table($table)
            ->select($columns)
            ->paginate(5);

        // ⭐ Fetch last sync log (important!)
        $lastLog = DB::table('user_id_sync_logs')
            ->orderBy('id', 'DESC')
            ->first();

        return view('selected_columns_view', compact('table', 'columns', 'data', 'lastLog'));
    }

    public function sendSelected(Request $request)
    {
        dd('hi');
        return redirect()->back()->with('success', 'Selected columns will be sent in every 3 minuts!');
    }

    //    public function saveUserinfoTableData(Request $request)
    // {
    //     // Extract directly from request
    //     $userIds = array_unique(array_column($request->input('data'), 'USERID'));

    //     ProcessUserIdsJob::dispatch($userIds);

    //     return redirect()->back()->with('success', 'Data is being processed in background!');
    // }

    public function sendAllUserId(Request $request)
    {
        $table = $request->input('table');
        $columns = $request->input('columns', []);

        if (!$table || empty($columns)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid table or columns.'
            ], 400);
        }

        if (!\Schema::hasTable($table)) {
            return response()->json([
                'success' => false,
                'message' => 'Table not found.'
            ], 404);
        }

        foreach ($columns as $col) {
            if (!\Schema::hasColumn($table, $col)) {
                return response()->json([
                    'success' => false,
                    'message' => "Column '{$col}' not found."
                ], 400);
            }
        }

        // Fetch all rows (full rows you selected)
        $allData = DB::table($table)->select($columns)->get();

        Log::info('Fetched rows: ' . json_encode($allData, JSON_PRETTY_PRINT));

        // Convert Collection → array of associative arrays
        $rowsArray = $allData->map(fn($row) => (array) $row)->toArray();

        // Send full rows to the job
        ProcessUserIdsJob::dispatch($rowsArray);

        return response()->json([
            'success' => true,
            'total_records' => $allData->count(),
            'records' => $allData
        ]);
    }
}
