<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AccessController extends Controller
{
    protected $accessPath;

    public function index()
    {
        return view('backend.dashboard.index');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'access_file' => 'required|mimes:mdb,accdb',
        ]);

        $path = $request->file('access_file')->storeAs('access', 'database.mdb');
        session(['access_db' => storage_path('app/' . $path)]);

        return redirect()->route('access.tables');
    }

    public function getConnectionString()
    {
        $dbPath = session('access_db');
        return "odbc:Driver={Microsoft Access Driver (*.mdb, *.accdb)};Dbq=$dbPath;";
    }
    public function listTables()
    {
        $dbPath = session('access_db');
    
        $connStr = "Driver={Microsoft Access Driver (*.mdb, *.accdb)};Dbq=$dbPath;";
        $conn = @odbc_connect($connStr, '', '');
    
        if (!$conn) {
            $conn = @odbc_connect("AccessDB", '', '');
            if (!$conn) {
                return response("Connection failed: Unable to connect using DSN-less or DSN.", 500);
            }
        }
    
        $tables = [];
        $result = odbc_tables($conn);
    
        while ($row = odbc_fetch_array($result)) {
            if ($row['TABLE_TYPE'] === 'TABLE' && !str_starts_with($row['TABLE_NAME'], 'MSys')) {
                $tables[] = $row['TABLE_NAME'];
            }
        }
    
        odbc_close($conn);
    
        return view('access.table', compact('tables'));
    }
    

    // public function listTables()
    // {
    //     $dbPath = session('access_db');
    
    //     // First try DSN-less connection
    //     $connStr = "Driver={Microsoft Access Driver (*.mdb, *.accdb)};Dbq=$dbPath;";
    //     $conn = @odbc_connect($connStr, '', '');
    
    //     // If DSN-less connection fails, try DSN-based (you must configure this DSN in ODBC settings)
    //     if (!$conn) {
    //         $conn = @odbc_connect("AccessDB", '', ''); // 'AccessDB' must be created in ODBC Data Source Administrator
    //         if (!$conn) {
    //             return response("Connection failed: Unable to connect using both DSN-less and DSN methods.", 500);
    //         }
    //     }
    
    //     $tables = [];
    
    //     // This query gets user table names from MSysObjects; requires read permission
    //     $sql = "SELECT Name FROM MSysObjects WHERE Type=1 AND Flags=0";
    //     $result = @odbc_exec($conn, $sql);
    
    //     if (!$result) {
    //         return response("Connection succeeded, but query failed. You might not have read permissions on 'MSysObjects'.", 500);
    //     }
    
    //     while ($row = odbc_fetch_array($result)) {
    //         $tables[] = $row['Name'];
    //     }
    
    //     odbc_close($conn);
    
    //     return view('access.tables', compact('tables'));
    // }
    

    public function convertToSQL($table)
    {
        // $pdo = new \PDO($this->getConnectionString());
        $pdo = new \PDO("odbc:AccessDB");

        $columns = $pdo->query("SELECT * FROM [$table] WHERE 1=0");
        $columnCount = $columns->columnCount();

        $sql = "CREATE TABLE `$table` (\n";
        for ($i = 0; $i < $columnCount; $i++) {
            $meta = $columns->getColumnMeta($i);
            $name = $meta['name'];
            $type = $this->mapType($meta['native_type'] ?? 'TEXT');
            $sql .= "  `$name` $type,\n";
        }
        $sql = rtrim($sql, ",\n") . "\n);";

        return response("<pre>$sql</pre>");
    }

    protected function mapType($accessType)
    {
        return match (strtoupper($accessType)) {
            'INTEGER' => 'INT',
            'LONG' => 'BIGINT',
            'DOUBLE' => 'DOUBLE',
            'TEXT', 'VARCHAR' => 'VARCHAR(255)',
            'DATE', 'DATETIME' => 'DATETIME',
            default => 'TEXT',
        };
    }
    public function showTable($table)
{
    $dbPath = session('access_db');
    $connStr = "Driver={Microsoft Access Driver (*.mdb, *.accdb)};Dbq=$dbPath;";
    dd([
        'path' => $dbPath,
        'exists' => file_exists($dbPath),
        'connStr' => $connStr,
    ]);
    $conn = odbc_connect($connStr, '', ''); // here i am getting false , means no connection . why ?
   
    if (!$conn) {
        return response("Failed to connect to Access database.", 500);
    }

    $rows = [];
    $columns = [];

    $result = @odbc_exec($conn, "SELECT * FROM [$table]");
    if (!$result) {
        return response("Failed to fetch data from table: $table", 500);
    }

    $colCount = odbc_num_fields($result);
    for ($i = 1; $i <= $colCount; $i++) {
        $columns[] = odbc_field_name($result, $i);
    }

    while ($row = odbc_fetch_array($result)) {
        $rows[] = $row;
    }

    odbc_close($conn);

    return view('access.show', compact('table', 'columns', 'rows'));
}

}