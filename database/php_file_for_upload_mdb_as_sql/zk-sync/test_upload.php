<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$apiKey  = 'zk-sync-2026';
$baseUrl = 'https://jmagc-atos.bidyapith.com';

$file = __DIR__ . '/test.sql';

file_put_contents($file, "TEST UPLOAD OK " . date('Y-m-d H:i:s'));

echo "Uploading file...\n";

$ch = curl_init($baseUrl . '/api/access/upload');

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_TIMEOUT => 60,
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $apiKey,
        'Accept: application/json',
    ],
    CURLOPT_POSTFIELDS => [
        'mdb_file' => new CURLFile($file, 'text/sql', 'test.sql')
    ],
]);

$response = curl_exec($ch);
$http     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error    = curl_error($ch);

curl_close($ch);

echo "HTTP: $http\n";
echo "ERROR: $error\n";
echo "RESPONSE:\n$response\n";
