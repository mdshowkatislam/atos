<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(0);
ini_set('memory_limit', '1024M');

echo "SCRIPT START\n";

// ================= CONFIG ==================

$apiKey    = 'zk-sync-2026';
$baseUrl   = 'https://jmagc-atos.bidyapith.com';

$cacheFile = __DIR__ . '/config_cache.json';
$logFile   = __DIR__ . '/error.log';
$dumpFile  = __DIR__ . '/access_dump.sql';

// Start fresh log file
file_put_contents($logFile, "SCRIPT START\n");

function logMsg($msg)
{
    global $logFile;
    file_put_contents(
        $logFile,
        date('Y-m-d H:i:s') . ' | ' . $msg . PHP_EOL,
        FILE_APPEND
    );
}

logMsg('==== START ====');

// ------------------------------------------
// STEP 1: Fetch MDB Path From Server
// ------------------------------------------

logMsg('Fetching config...');

$ch = curl_init($baseUrl . '/api/access/config');

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 20,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $apiKey,
        'Accept: application/json',
    ],
    CURLOPT_SSL_VERIFYPEER => false,   // disabled ONLY for local Windows SSL issue
    CURLOPT_SSL_VERIFYHOST => false,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error    = curl_error($ch);
curl_close($ch);

if ($error) {
    logMsg("CONFIG CURL ERROR: $error");
}

$accessFile = null;

if ($httpCode === 200 && $response) {
    $data = json_decode($response, true);

    if (!empty($data['db_location'])) {
        $accessFile = str_replace('/', '\\', $data['db_location']);
        file_put_contents($cacheFile, json_encode([
            'db_location' => $accessFile,
            'cached_at'   => date('Y-m-d H:i:s')
        ], JSON_PRETTY_PRINT));

        logMsg("MDB PATH FROM SERVER: $accessFile");
    } else {
        logMsg("CONFIG RESPONSE INVALID");
    }
} else {
    logMsg("CONFIG HTTP ERROR: $httpCode");
}

// ------------------------------------------
// STEP 2: Fallback Cache
// ------------------------------------------

if (!$accessFile && file_exists($cacheFile)) {
    $cached = json_decode(file_get_contents($cacheFile), true);
    $accessFile = $cached['db_location'] ?? null;
    logMsg("MDB PATH FROM CACHE: $accessFile");
}

if (!$accessFile) {
    logMsg("ERROR: No MDB path available");
    exit;
}

// ------------------------------------------
// STEP 3: Validate MDB Exists
// ------------------------------------------

if (!file_exists($accessFile)) {
    logMsg("ERROR: MDB NOT FOUND: $accessFile");
    exit;
}

if (!is_readable($accessFile)) {
    logMsg("ERROR: MDB NOT READABLE: $accessFile");
    exit;
}

logMsg("MDB FOUND: $accessFile");

// ------------------------------------------
// STEP 4: Read MDB → SQL Dump
// ------------------------------------------

logMsg('Dumping MDB → SQL...');

function sqlEscape($val)
{
    if ($val === null) return 'NULL';
    $val = str_replace(["\\", "'"], ["\\\\", "\\'"], $val);
    return "'$val'";
}

$dsn = "odbc:Driver={Microsoft Access Driver (*.mdb, *.accdb)};Dbq={$accessFile};";

try {

    $pdo = new PDO($dsn);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $tables = [
        'USERINFO'   => ['USERID', 'Badgenumber', 'name'],
        'CHECKINOUT' => ['USERID', 'CHECKTIME'],
    ];

    $sql = "-- ZK ACCESS SQL DUMP\n-- Generated: " . date('Y-m-d H:i:s') . "\n\n";

    foreach ($tables as $table => $cols) {

        $tableName = strtolower($table);

        $sql .= "DROP TABLE IF EXISTS `$tableName`;\n";
        $sql .= "CREATE TABLE `$tableName` (\n";
        $sql .= " `id` INT AUTO_INCREMENT PRIMARY KEY,\n";

        foreach ($cols as $c) {
            $type = $c === 'CHECKTIME' ? 'DATETIME' : 'VARCHAR(255)';
            $sql .= " `$c` $type NULL,\n";
        }

        $sql .= " `created_at` DATETIME NULL,\n `updated_at` DATETIME NULL\n);\n\n";

        $stmt = $pdo->query("SELECT " . implode(',', $cols) . " FROM $table");

        $rows = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            foreach ($row as &$v) {
                $v = $v !== null ? sqlEscape($v) : 'NULL';
            }
            $rows[] = '(' . implode(',', $row) . ')';
        }

        if ($rows) {
            $sql .= "INSERT INTO `$tableName` (" . implode(',', $cols) . ") VALUES\n";
            $sql .= implode(",\n", $rows) . ";\n\n";
        }
    }

    file_put_contents($dumpFile, $sql);

    logMsg("SQL DUMP GENERATED: " . filesize($dumpFile) . " bytes");

} catch (Throwable $e) {
    logMsg("DUMP ERROR: " . $e->getMessage());
    exit;
}

// ------------------------------------------
// STEP 5: Upload SQL Dump
// ------------------------------------------

logMsg("Uploading SQL dump...");

$ch = curl_init($baseUrl . '/api/access/upload');

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_TIMEOUT => 900,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $apiKey,
        'Accept: application/json',
    ],
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
    CURLOPT_POSTFIELDS => [
        'mdb_file' => new CURLFile($dumpFile, 'text/sql', 'Access.sql')
    ],
]);

$result   = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error    = curl_error($ch);
curl_close($ch);

if ($error) {
    logMsg("UPLOAD ERROR: $error");
} else {
    logMsg("UPLOAD HTTP: $httpCode");
    logMsg("UPLOAD RESPONSE: $result");
}

logMsg('==== END ====');

echo "SYNC COMPLETE\n";
