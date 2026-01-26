<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "SCRIPT START\n";

$apiKey  = 'zk-sync-2026';
$baseUrl = 'https://jmagc-atos.bidyapith.com';

$ch = curl_init($baseUrl . '/api/access/config');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 10,
    CURLOPT_CONNECTTIMEOUT => 5,

    // TEMPORARY: Disable SSL verification
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,

    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $apiKey,
        'Accept: application/json',
    ],
]);

echo "Before curl\n";

$response = curl_exec($ch);

echo "After curl\n";

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error    = curl_error($ch);

curl_close($ch);

echo "HTTP CODE: $httpCode\n";
echo "CURL ERROR: $error\n";
echo "RESPONSE:\n";
echo $response . "\n";
