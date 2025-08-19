<?php
/**
 * Quick deployment script to apply changes from admin form to live site
 * This can be called directly or via the admin interface
 */

// Include the main update functionality
require_once 'update-content.php';

// Function to regenerate site from current config
function regenerateSite() {
    $config_file = 'content-config.json';
    $output_file = 'index.html';
    
    // Load current configuration
    if (!file_exists($config_file)) {
        throw new Exception('Configuration file not found');
    }
    
    $config = json_decode(file_get_contents($config_file), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON in configuration file');
    }
    
    // Generate HTML from current config
    $html_content = generateHTMLFromConfig($config);
    
    // Write to index.html
    if (file_put_contents($output_file, $html_content) === false) {
        throw new Exception('Failed to write index.html file');
    }
    
    return true;
}

// Function to generate HTML from configuration
function generateHTMLFromConfig($config) {
    $template = file_get_contents('index-template.html');
    if ($template === false) {
        throw new Exception('Template file not found');
    }
    
    // Apply replacements using the same logic as update-content.php
    return replacePlaceholders($template, $config);
}

// Handle OPTIONS request for CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    exit(0);
}

// If called directly via HTTP
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['regenerate'])) {
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    
    try {
        regenerateSite();
        echo json_encode([
            'success' => true,
            'message' => 'Site regenerated successfully from current configuration'
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
    exit;
}

// If called directly via command line
if (php_sapi_name() === 'cli') {
    try {
        regenerateSite();
        echo "✅ Site regenerated successfully from current configuration!\n";
        echo "📄 index.html has been updated with the latest content.\n";
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
        exit(1);
    }
}
?>
