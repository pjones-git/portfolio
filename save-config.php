<?php
/**
 * Save configuration endpoint for admin interface
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT') {
    try {
        // Get the raw POST data
        $input = file_get_contents('php://input');
        
        // Validate JSON
        $config = json_decode($input, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON data');
        }
        
        // Save to content-config.json
        $result = file_put_contents('content-config.json', $input);
        
        if ($result === false) {
            throw new Exception('Failed to save configuration file');
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Configuration saved successfully',
            'bytes_written' => $result
        ]);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error' => 'Method not allowed'
    ]);
}
?>
