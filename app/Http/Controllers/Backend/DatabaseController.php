<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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

    public function show($db)
    {
        try {
             $allTables = [];
   
            $tables = \DB::select("
                SELECT TABLE_NAME
                FROM INFORMATION_SCHEMA.TABLES
                WHERE TABLE_SCHEMA = ?
            ", [$db]);

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

    public function showTable($database, $table)
    {
        $data = \DB::table($table)->get();
        return view('backend.database.table', compact('data', 'table', 'database'));
    }
}
