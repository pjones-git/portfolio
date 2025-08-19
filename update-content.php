<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuration
$config_file = 'content-config.json';
$images_dir = 'images/';
$upload_dir = 'uploads/';
$max_file_size = 5 * 1024 * 1024; // 5MB
$allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

// Ensure upload directory exists
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Function to sanitize input
function sanitizeInput($input) {
    if (is_array($input)) {
        return array_map('sanitizeInput', $input);
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Function to validate and upload image
function uploadImage($file, $prefix = 'img') {
    global $upload_dir, $max_file_size, $allowed_extensions;
    
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    // Check file size
    if ($file['size'] > $max_file_size) {
        throw new Exception('File size exceeds maximum allowed size');
    }
    
    // Check file extension
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($file_extension, $allowed_extensions)) {
        throw new Exception('Invalid file type. Allowed types: ' . implode(', ', $allowed_extensions));
    }
    
    // Generate unique filename
    $filename = $prefix . '_' . uniqid() . '.' . $file_extension;
    $destination = $upload_dir . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        // Resize image if needed (optional)
        resizeImage($destination, 800, 600); // Max 800x600 for regular images
        return $upload_dir . $filename;
    }
    
    throw new Exception('Failed to upload image');
}

// Function to resize image (basic implementation)
function resizeImage($file_path, $max_width, $max_height) {
    $image_info = getimagesize($file_path);
    if (!$image_info) return false;
    
    $width = $image_info[0];
    $height = $image_info[1];
    $type = $image_info[2];
    
    // Check if resize is needed
    if ($width <= $max_width && $height <= $max_height) {
        return true;
    }
    
    // Calculate new dimensions
    $ratio = min($max_width / $width, $max_height / $height);
    $new_width = intval($width * $ratio);
    $new_height = intval($height * $ratio);
    
    // Create new image
    $new_image = imagecreatetruecolor($new_width, $new_height);
    
    // Load original image
    switch ($type) {
        case IMAGETYPE_JPEG:
            $source = imagecreatefromjpeg($file_path);
            break;
        case IMAGETYPE_PNG:
            $source = imagecreatefrompng($file_path);
            imagealphablending($new_image, false);
            imagesavealpha($new_image, true);
            break;
        case IMAGETYPE_GIF:
            $source = imagecreatefromgif($file_path);
            break;
        default:
            return false;
    }
    
    // Resize
    imagecopyresampled($new_image, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
    
    // Save resized image
    switch ($type) {
        case IMAGETYPE_JPEG:
            imagejpeg($new_image, $file_path, 85);
            break;
        case IMAGETYPE_PNG:
            imagepng($new_image, $file_path);
            break;
        case IMAGETYPE_GIF:
            imagegif($new_image, $file_path);
            break;
    }
    
    imagedestroy($source);
    imagedestroy($new_image);
    
    return true;
}

// Function to create backup
function createBackup($config_file) {
    $backup_dir = 'backups/';
    if (!is_dir($backup_dir)) {
        mkdir($backup_dir, 0755, true);
    }
    
    $backup_filename = $backup_dir . 'config_backup_' . date('Y-m-d_H-i-s') . '.json';
    if (file_exists($config_file)) {
        copy($config_file, $backup_filename);
        return $backup_filename;
    }
    return false;
}

// Main processing
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Create backup before making changes
        $backup_file = createBackup($config_file);
        
        // Load existing configuration
        $config = [];
        if (file_exists($config_file)) {
            $config = json_decode(file_get_contents($config_file), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON in configuration file');
            }
        }
        
        // Process form data
        $form_data = $_POST;
        
        // Sanitize all input data
        $form_data = sanitizeInput($form_data);
        
        // Handle file uploads
        if (!empty($_FILES)) {
            // Profile image upload
            if (isset($_FILES['personal']['tmp_name']['profile_image']) && 
                $_FILES['personal']['error']['profile_image'] === UPLOAD_ERR_OK) {
                
                $profile_file = [
                    'name' => $_FILES['personal']['name']['profile_image'],
                    'tmp_name' => $_FILES['personal']['tmp_name']['profile_image'],
                    'error' => $_FILES['personal']['error']['profile_image'],
                    'size' => $_FILES['personal']['size']['profile_image']
                ];
                
                $uploaded_path = uploadImage($profile_file, 'profile');
                $form_data['personal']['profile_image'] = $uploaded_path;
            }
            
            // Portfolio images upload
            if (isset($_FILES['portfolio']['tmp_name']['items'])) {
                foreach ($_FILES['portfolio']['tmp_name']['items'] as $index => $item) {
                    if (isset($item['image_file']) && 
                        $_FILES['portfolio']['error']['items'][$index]['image_file'] === UPLOAD_ERR_OK) {
                        
                        $portfolio_file = [
                            'name' => $_FILES['portfolio']['name']['items'][$index]['image_file'],
                            'tmp_name' => $_FILES['portfolio']['tmp_name']['items'][$index]['image_file'],
                            'error' => $_FILES['portfolio']['error']['items'][$index]['image_file'],
                            'size' => $_FILES['portfolio']['size']['items'][$index]['image_file']
                        ];
                        
                        $uploaded_path = uploadImage($portfolio_file, 'portfolio');
                        $form_data['portfolio']['items'][$index]['image'] = $uploaded_path;
                    }
                }
            }
        }
        
        // Update configuration with form data
        if (isset($form_data['site'])) {
            $config['site'] = array_merge($config['site'] ?? [], $form_data['site']);
        }
        
        if (isset($form_data['personal'])) {
            $config['personal'] = array_merge($config['personal'] ?? [], $form_data['personal']);
        }
        
        if (isset($form_data['work'])) {
            $config['work'] = array_merge($config['work'] ?? [], $form_data['work']);
            
            // Handle services array - rebuild to ensure proper indexing
            if (isset($form_data['work']['services'])) {
                $config['work']['services'] = [];
                foreach ($form_data['work']['services'] as $service) {
                    if (!empty($service['title']) && !empty($service['description'])) {
                        $service['icon_solid'] = isset($service['icon_solid']);
                        $config['work']['services'][] = $service;
                    }
                }
            }
        }
        
        if (isset($form_data['portfolio'])) {
            $config['portfolio'] = array_merge($config['portfolio'] ?? [], $form_data['portfolio']);
            
            // Handle portfolio items - rebuild to ensure proper indexing
            if (isset($form_data['portfolio']['items'])) {
                $config['portfolio']['items'] = [];
                foreach ($form_data['portfolio']['items'] as $item) {
                    if (!empty($item['title']) && !empty($item['description'])) {
                        $config['portfolio']['items'][] = $item;
                    }
                }
            }
        }
        
        if (isset($form_data['contact'])) {
            $config['contact'] = array_merge($config['contact'] ?? [], $form_data['contact']);
            
            // Handle social links - rebuild to ensure proper indexing
            if (isset($form_data['contact']['social_links'])) {
                $config['contact']['social_links'] = [];
                foreach ($form_data['contact']['social_links'] as $link) {
                    if (!empty($link['platform']) && !empty($link['url'])) {
                        $config['contact']['social_links'][] = $link;
                    }
                }
            }
        }
        
        if (isset($form_data['navigation'])) {
            // Handle navigation - rebuild to ensure proper indexing
            $config['navigation'] = [];
            foreach ($form_data['navigation'] as $nav_item) {
                if (!empty($nav_item['label']) && !empty($nav_item['href'])) {
                    $config['navigation'][] = $nav_item;
                }
            }
        }
        
        if (isset($form_data['copyright'])) {
            $config['copyright'] = array_merge($config['copyright'] ?? [], $form_data['copyright']);
        }
        
        // Save updated configuration
        $json_string = json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Failed to encode configuration to JSON');
        }
        
        if (file_put_contents($config_file, $json_string) === false) {
            throw new Exception('Failed to write configuration file');
        }
        
        // Update the main HTML file with new content
        updateHtmlFile($config);
        
        // Return success response
        echo json_encode([
            'success' => true,
            'message' => 'Configuration updated successfully',
            'backup_file' => $backup_file
        ]);
        
    } catch (Exception $e) {
        // Return error response
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

// Function to update the main HTML file
function updateHtmlFile($config) {
    $template_file = 'index-template.html';
    $output_file = 'index.html';
    
    // If template doesn't exist, create it from current index.html
    if (!file_exists($template_file)) {
        if (file_exists($output_file)) {
            copy($output_file, $template_file);
        } else {
            throw new Exception('Neither template nor index file exists');
        }
    }
    
    $html_content = file_get_contents($template_file);
    if ($html_content === false) {
        throw new Exception('Failed to read template file');
    }
    
    // Replace placeholders with actual content
    $html_content = replacePlaceholders($html_content, $config);
    
    // Write updated HTML
    if (file_put_contents($output_file, $html_content) === false) {
        throw new Exception('Failed to write updated HTML file');
    }
}

// Function to replace placeholders in HTML content
function replacePlaceholders($html_content, $config) {
    // Site title and meta
    $html_content = str_replace('{{SITE_TITLE}}', $config['site']['title'] ?? 'Miniport by HTML5 UP', $html_content);
    $html_content = str_replace('{{META_DESCRIPTION}}', $config['site']['meta_description'] ?? '', $html_content);
    
    // Personal information
    $html_content = str_replace('{{PERSONAL_NAME}}', $config['personal']['name'] ?? '', $html_content);
    $html_content = str_replace('{{PERSONAL_TAGLINE}}', $config['personal']['tagline'] ?? '', $html_content);
    $html_content = str_replace('{{PROFILE_IMAGE}}', $config['personal']['profile_image'] ?? 'images/pic00.jpg', $html_content);
    $html_content = str_replace('{{PERSONAL_CTA}}', $config['personal']['cta_text'] ?? 'Learn about what I do', $html_content);
    
    // Work section
    $html_content = str_replace('{{WORK_TITLE}}', $config['work']['title'] ?? '', $html_content);
    $html_content = str_replace('{{WORK_SUBTITLE}}', $config['work']['subtitle'] ?? '', $html_content);
    $html_content = str_replace('{{WORK_FOOTER}}', $config['work']['footer_text'] ?? '', $html_content);
    $html_content = str_replace('{{WORK_CTA}}', $config['work']['cta_text'] ?? '', $html_content);
    
    // Portfolio section
    $html_content = str_replace('{{PORTFOLIO_TITLE}}', $config['portfolio']['title'] ?? '', $html_content);
    $html_content = str_replace('{{PORTFOLIO_SUBTITLE}}', $config['portfolio']['subtitle'] ?? '', $html_content);
    $html_content = str_replace('{{PORTFOLIO_FOOTER}}', $config['portfolio']['footer_text'] ?? '', $html_content);
    $html_content = str_replace('{{PORTFOLIO_CTA}}', $config['portfolio']['cta_text'] ?? '', $html_content);
    
    // Contact section
    $html_content = str_replace('{{CONTACT_TITLE}}', $config['contact']['title'] ?? '', $html_content);
    $html_content = str_replace('{{CONTACT_SUBTITLE}}', $config['contact']['subtitle'] ?? '', $html_content);
    $html_content = str_replace('{{CONTACT_FORM_ACTION}}', $config['contact']['form_action'] ?? '#', $html_content);
    
    // Copyright
    $html_content = str_replace('{{COPYRIGHT_TEXT}}', $config['copyright']['text'] ?? '', $html_content);
    $html_content = str_replace('{{DESIGN_CREDIT}}', $config['copyright']['design_credit'] ?? 'Design: HTML5 UP', $html_content);
    $html_content = str_replace('{{DESIGN_URL}}', $config['copyright']['design_url'] ?? 'http://html5up.net', $html_content);
    
    // Generate dynamic content for repeating sections
    $html_content = generateNavigationHtml($html_content, $config['navigation'] ?? []);
    $html_content = generateServicesHtml($html_content, $config['work']['services'] ?? []);
    $html_content = generatePortfolioHtml($html_content, $config['portfolio']['items'] ?? []);
    $html_content = generateSocialLinksHtml($html_content, $config['contact']['social_links'] ?? []);
    
    return $html_content;
}

// Function to generate navigation HTML
function generateNavigationHtml($html_content, $navigation) {
    $nav_html = '';
    foreach ($navigation as $nav_item) {
        $nav_html .= '<li><a href="' . htmlspecialchars($nav_item['href']) . '">' . htmlspecialchars($nav_item['label']) . '</a></li>' . "\n";
    }
    return str_replace('{{NAVIGATION_ITEMS}}', $nav_html, $html_content);
}

// Function to generate services HTML
function generateServicesHtml($html_content, $services) {
    $services_html = '';
    foreach ($services as $service) {
        $icon_class = 'icon ' . ($service['icon_solid'] ? 'solid ' : '') . 'featured ' . htmlspecialchars($service['icon']);
        $services_html .= '<div class="col-4 col-6-medium col-12-small">' . "\n";
        $services_html .= '    <section class="box style1">' . "\n";
        $services_html .= '        <span class="' . $icon_class . '"></span>' . "\n";
        $services_html .= '        <h3>' . htmlspecialchars($service['title']) . '</h3>' . "\n";
        $services_html .= '        <p>' . htmlspecialchars($service['description']) . '</p>' . "\n";
        $services_html .= '    </section>' . "\n";
        $services_html .= '</div>' . "\n";
    }
    return str_replace('{{SERVICES_ITEMS}}', $services_html, $html_content);
}

// Function to generate portfolio HTML
function generatePortfolioHtml($html_content, $portfolio_items) {
    $portfolio_html = '';
    foreach ($portfolio_items as $item) {
        $portfolio_html .= '<div class="col-4 col-6-medium col-12-small">' . "\n";
        $portfolio_html .= '    <article class="box style2">' . "\n";
        $portfolio_html .= '        <a href="' . htmlspecialchars($item['link']) . '" class="image featured"><img src="' . htmlspecialchars($item['image']) . '" alt="" /></a>' . "\n";
        $portfolio_html .= '        <h3><a href="' . htmlspecialchars($item['link']) . '">' . htmlspecialchars($item['title']) . '</a></h3>' . "\n";
        $portfolio_html .= '        <p>' . htmlspecialchars($item['description']) . '</p>' . "\n";
        $portfolio_html .= '    </article>' . "\n";
        $portfolio_html .= '</div>' . "\n";
    }
    return str_replace('{{PORTFOLIO_ITEMS}}', $portfolio_html, $html_content);
}

// Function to generate social links HTML
function generateSocialLinksHtml($html_content, $social_links) {
    $social_html = '';
    foreach ($social_links as $link) {
        $social_html .= '<li><a href="' . htmlspecialchars($link['url']) . '" class="icon brands ' . htmlspecialchars($link['icon']) . '"><span class="label">' . htmlspecialchars($link['platform']) . '</span></a></li>' . "\n";
    }
    return str_replace('{{SOCIAL_LINKS}}', $social_html, $html_content);
}
?>
