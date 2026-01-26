<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(0);

echo "SCRIPT START\n";

// ================= CONFIG ==================

$apiKey  = 'zk-sync-2026';
$baseUrl = 'https://jmagc-atos.bidyapith.com';

$cacheFile = __DIR__ . '/config_cache.json';
$logFile   = __DIR__ . '/error.log';
$dumpFile  = __DIR__ . '/access_dump.sql';

@unlink($logFile);

function logMsg($msg)
{
    file_put_contents(
        __DIR__ . '/error.log',
        date('Y-m-d H:i:s') . ' | ' . $msg . PHP_EOL,
        FILE_APPEND
    );
}

logMsg('==== START ====');

// ------------------------------------------
// STEP 1: Fetch MDB Path
// ------------------------------------------

logMsg('Fetching config...');

$ch = curl_init($baseUrl . '/api/access/config');

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $apiKey,
        'Accept: application/json',
    ],
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error    = curl_error($ch);
curl_close($ch);

logMsg("CONFIG HTTP: $httpCode");

$data = json_decode($response, true);
$accessFile = $data['db_location'] ?? null;

if ($accessFile) {
    $accessFile = str_replace('/', '\\', $accessFile);
    file_put_contents($cacheFile, json_encode(['db_location' => $accessFile]));
    logMsg("MDB PATH FROM SERVER: $accessFile");
}

if (!$accessFile && file_exists($cacheFile)) {
    $cached = json_decode(file_get_contents($cacheFile), true);
    $accessFile = $cached['db_location'] ?? null;
    logMsg("MDB PATH FROM CACHE: $accessFile");
}

if (!$accessFile) {
    logMsg("ERROR: MDB PATH EMPTY");
    exit;
}

if (!file_exists($accessFile)) {
    logMsg("ERROR: MDB FILE NOT FOUND: $accessFile");
    exit;
}

logMsg("MDB FOUND: $accessFile");

// ------------------------------------------
// STEP 4: Dump MDB → SQL
// ------------------------------------------

logMsg('Dumping MDB → SQL');

function sqlEscape($val)
{
    if ($val === null) return 'NULL';
    return "'" . str_replace(["\\", "'"], ["\\\\", "\\'"], $val) . "'";
}

$dsn = "odbc:Driver={Microsoft Access Driver (*.mdb, *.accdb)};Dbq={$accessFile};";

try {

    $pdo = new PDO($dsn);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "-- ZKTeco SQL Dump " . date('Y-m-d H:i:s') . "\n\n";

    // USERINFO
    $sql .= "
CREATE TABLE IF NOT EXISTS `userinfo` (
  `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
  `USERID` INT NOT NULL,
  `Badgenumber` VARCHAR(50),
  `Name` VARCHAR(150),
  UNIQUE KEY `uid_unique` (`USERID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

TRUNCATE TABLE `userinfo`;
";

    $stmt = $pdo->query("SELECT USERID, Badgenumber, Name FROM USERINFO");

    $rows = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        foreach ($row as &$v) $v = $v === null ? 'NULL' : sqlEscape($v);
        $rows[] = '(' . implode(',', $row) . ')';
    }

    if ($rows) {
        $sql .= "INSERT INTO `userinfo` (USERID,Badgenumber,Name) VALUES\n";
        $sql .= implode(",\n", $rows) . ";\n\n";
    }

    // CHECKINOUT
    $sql .= "
CREATE TABLE IF NOT EXISTS `checkinout` (
  `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
  `USERID` INT NOT NULL,
  `CHECKTIME` DATETIME NOT NULL,
  UNIQUE KEY `unique_att` (`USERID`,`CHECKTIME`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

    $stmt = $pdo->query("SELECT USERID, CHECKTIME FROM CHECKINOUT");

    $rows = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        foreach ($row as &$v) $v = $v === null ? 'NULL' : sqlEscape($v);
        $rows[] = '(' . implode(',', $row) . ')';
    }

    if ($rows) {
        $sql .= "INSERT IGNORE INTO `checkinout` (USERID,CHECKTIME) VALUES\n";
        $sql .= implode(",\n", $rows) . ";\n\n";
    }

    file_put_contents($dumpFile, $sql);
    logMsg("SQL DUMP GENERATED: " . filesize($dumpFile) . " bytes");

} catch (Throwable $e) {
    logMsg("DUMP ERROR: " . $e->getMessage());
    exit;
}

// ------------------------------------------
// STEP 5: Upload SQL
// ------------------------------------------

logMsg("Uploading SQL dump...");

$ch = curl_init($baseUrl . '/api/access/upload');

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_TIMEOUT => 900,
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $apiKey,
        'Accept: application/json',
    ],
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
    CURLOPT_POSTFIELDS => [
        'mdb_file' => new CURLFile($dumpFile, 'text/sql', 'access.sql')
    ],
]);

$result   = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error    = curl_error($ch);
curl_close($ch);

logMsg("UPLOAD HTTP: $httpCode");
logMsg("UPLOAD RESPONSE: $result");

echo "SYNC COMPLETE\n";
