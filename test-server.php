<?php
// Simple test to verify PHP server is working
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

echo json_encode([
    'status' => 'working',
    'message' => 'PHP server is running correctly',
    'time' => date('Y-m-d H:i:s'),
    'php_version' => PHP_VERSION
]);
?>
