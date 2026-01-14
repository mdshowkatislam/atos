<?php
// test_upload.php
header('Content-Type: application/json');

// Try to increase the limits
ini_set('max_execution_time', 600);
ini_set('max_input_time', 600);
set_time_limit(600);

$logFile = __DIR__ . '/../storage/logs/test_upload.log';

function logMsg($message) {
    global $logFile;
    file_put_contents($logFile, date('Y-m-d H:i:s') . ' - ' . $message . PHP_EOL, FILE_APPEND);
}

logMsg('=== TEST UPLOAD START ===');

// Check API key
$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
if ($authHeader !== 'Bearer zk-sync-2026') {
    logMsg('Invalid API key');
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Check for file
if (!isset($_FILES['mdb_file'])) {
    logMsg('No file in $_FILES');
    http_response_code(400);
    echo json_encode(['error' => 'No file uploaded']);
    exit;
}

$file = $_FILES['mdb_file'];
logMsg('File received: ' . json_encode($file));

// Save file
$uploadDir = __DIR__ . '/../storage/app/access/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$destination = $uploadDir . 'test_incoming.mdb';

if (move_uploaded_file($file['tmp_name'], $destination)) {
    logMsg('File saved: ' . $destination);
    echo json_encode([
        'success' => true,
        'message' => 'File uploaded via test script',
        'path' => $destination,
        'size' => filesize($destination)
    ]);
} else {
    logMsg('Failed to move uploaded file');
    http_response_code(500);
    echo json_encode(['error' => 'Failed to save file']);
}

logMsg('=== TEST UPLOAD END ===');