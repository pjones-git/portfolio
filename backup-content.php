<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Configuration
$config_file = 'content-config.json';
$html_file = 'index.html';
$backup_dir = 'backups/';

// Ensure backup directory exists
if (!is_dir($backup_dir)) {
    mkdir($backup_dir, 0755, true);
}

// Function to create backup
function createFullBackup() {
    global $config_file, $html_file, $backup_dir;
    
    $timestamp = date('Y-m-d_H-i-s');
    $backup_filename = $backup_dir . 'full_backup_' . $timestamp . '.zip';
    
    // Create a ZIP archive
    $zip = new ZipArchive();
    if ($zip->open($backup_filename, ZipArchive::CREATE) !== TRUE) {
        throw new Exception('Cannot create backup ZIP file');
    }
    
    // Add configuration file
    if (file_exists($config_file)) {
        $zip->addFile($config_file, 'content-config.json');
    }
    
    // Add HTML file
    if (file_exists($html_file)) {
        $zip->addFile($html_file, 'index.html');
    }
    
    // Add images directory if it exists
    if (is_dir('images/')) {
        $images = glob('images/*');
        foreach ($images as $image) {
            if (is_file($image)) {
                $zip->addFile($image, $image);
            }
        }
    }
    
    // Add uploads directory if it exists
    if (is_dir('uploads/')) {
        $uploads = glob('uploads/*');
        foreach ($uploads as $upload) {
            if (is_file($upload)) {
                $zip->addFile($upload, $upload);
            }
        }
    }
    
    $zip->close();
    
    return $backup_filename;
}

// Function to list existing backups
function listBackups() {
    global $backup_dir;
    
    $backups = [];
    $backup_files = glob($backup_dir . '*backup*.{json,zip}', GLOB_BRACE);
    
    foreach ($backup_files as $file) {
        $backups[] = [
            'filename' => basename($file),
            'path' => $file,
            'size' => filesize($file),
            'date' => date('Y-m-d H:i:s', filemtime($file)),
            'type' => pathinfo($file, PATHINFO_EXTENSION) === 'zip' ? 'Full' : 'Config Only'
        ];
    }
    
    // Sort by date, newest first
    usort($backups, function($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });
    
    return $backups;
}

// Function to restore backup
function restoreBackup($backup_file) {
    global $config_file, $html_file;
    
    if (!file_exists($backup_file)) {
        throw new Exception('Backup file not found');
    }
    
    $file_extension = pathinfo($backup_file, PATHINFO_EXTENSION);
    
    if ($file_extension === 'json') {
        // Restore config-only backup
        if (!copy($backup_file, $config_file)) {
            throw new Exception('Failed to restore configuration file');
        }
        
        // Regenerate HTML from restored config
        $config = json_decode(file_get_contents($config_file), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON in backup file');
        }
        
        // Include the update functions from update-content.php
        include_once 'update-content.php';
        updateHtmlFile($config);
        
        return 'Configuration restored successfully';
        
    } elseif ($file_extension === 'zip') {
        // Restore full backup
        $zip = new ZipArchive();
        if ($zip->open($backup_file) !== TRUE) {
            throw new Exception('Cannot open backup ZIP file');
        }
        
        // Extract files
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $filename = $zip->getNameIndex($i);
            $file_content = $zip->getFromIndex($i);
            
            // Create directory if needed
            $dir = dirname($filename);
            if (!is_dir($dir) && $dir !== '.') {
                mkdir($dir, 0755, true);
            }
            
            // Write file
            file_put_contents($filename, $file_content);
        }
        
        $zip->close();
        
        return 'Full backup restored successfully';
        
    } else {
        throw new Exception('Unsupported backup file format');
    }
}

// Main processing
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        $action = $input['action'] ?? $_POST['action'] ?? 'create';
        
        switch ($action) {
            case 'create':
                $backup_file = createFullBackup();
                echo json_encode([
                    'success' => true,
                    'message' => 'Backup created successfully',
                    'filename' => basename($backup_file),
                    'path' => $backup_file,
                    'size' => filesize($backup_file)
                ]);
                break;
                
            case 'list':
                $backups = listBackups();
                echo json_encode([
                    'success' => true,
                    'backups' => $backups
                ]);
                break;
                
            case 'restore':
                $backup_file = $input['file'] ?? $_POST['file'] ?? '';
                if (empty($backup_file)) {
                    throw new Exception('Backup file not specified');
                }
                
                // Security check - ensure file is in backup directory
                if (strpos($backup_file, $backup_dir) !== 0 && strpos($backup_file, '/') !== false) {
                    $backup_file = $backup_dir . basename($backup_file);
                }
                
                $message = restoreBackup($backup_file);
                echo json_encode([
                    'success' => true,
                    'message' => $message
                ]);
                break;
                
            case 'delete':
                $backup_file = $input['file'] ?? $_POST['file'] ?? '';
                if (empty($backup_file)) {
                    throw new Exception('Backup file not specified');
                }
                
                // Security check - ensure file is in backup directory
                if (strpos($backup_file, $backup_dir) !== 0) {
                    $backup_file = $backup_dir . basename($backup_file);
                }
                
                if (!file_exists($backup_file)) {
                    throw new Exception('Backup file not found');
                }
                
                if (unlink($backup_file)) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Backup deleted successfully'
                    ]);
                } else {
                    throw new Exception('Failed to delete backup file');
                }
                break;
                
            default:
                throw new Exception('Invalid action specified');
        }
        
    } catch (Exception $e) {
        http_response_code(400);
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
