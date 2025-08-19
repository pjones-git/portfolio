<?php
/**
 * Simple deployment script for admin interface
 */

// Handle CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Only handle GET requests with regenerate parameter
if ($_SERVER['REQUEST_METHOD'] !== 'GET' || !isset($_GET['regenerate'])) {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

try {
    // Load configuration
    $config_file = 'content-config.json';
    if (!file_exists($config_file)) {
        throw new Exception('Configuration file not found');
    }
    
    $config_json = file_get_contents($config_file);
    $config = json_decode($config_json, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON in configuration file');
    }
    
    // Load template
    $template_file = 'index-template.html';
    if (!file_exists($template_file)) {
        throw new Exception('Template file not found');
    }
    
    $template = file_get_contents($template_file);
    
    // Simple placeholder replacement
    $html = $template;
    
    // Replace site info
    $html = str_replace('{{SITE_TITLE}}', htmlspecialchars($config['site']['title'] ?? ''), $html);
    $html = str_replace('{{META_DESCRIPTION}}', htmlspecialchars($config['site']['meta_description'] ?? ''), $html);
    
    // Replace personal info
    $html = str_replace('{{PERSONAL_NAME}}', htmlspecialchars($config['personal']['name'] ?? ''), $html);
    $html = str_replace('{{PERSONAL_TAGLINE}}', htmlspecialchars($config['personal']['tagline'] ?? ''), $html);
    $html = str_replace('{{PROFILE_IMAGE}}', htmlspecialchars($config['personal']['profile_image'] ?? 'images/pic00.jpg'), $html);
    $html = str_replace('{{PERSONAL_CTA}}', htmlspecialchars($config['personal']['cta_text'] ?? ''), $html);
    
    // Replace work info
    $html = str_replace('{{WORK_TITLE}}', htmlspecialchars($config['work']['title'] ?? ''), $html);
    $html = str_replace('{{WORK_SUBTITLE}}', htmlspecialchars($config['work']['subtitle'] ?? ''), $html);
    $html = str_replace('{{WORK_FOOTER}}', htmlspecialchars($config['work']['footer_text'] ?? ''), $html);
    $html = str_replace('{{WORK_CTA}}', htmlspecialchars($config['work']['cta_text'] ?? ''), $html);
    
    // Generate services HTML
    $services_html = '';
    if (!empty($config['work']['services'])) {
        foreach ($config['work']['services'] as $service) {
            $icon_class = $service['icon_solid'] ? 'solid featured' : 'featured';
            $services_html .= sprintf(
                '<div class="col-4 col-6-medium col-12-small">
                    <section class="box style1">
                        <span class="icon %s %s"></span>
                        <h3>%s</h3>
                        <p>%s</p>
                    </section>
                </div>',
                $icon_class,
                htmlspecialchars($service['icon'] ?? 'fa-star'),
                htmlspecialchars($service['title'] ?? ''),
                htmlspecialchars($service['description'] ?? '')
            );
        }
    }
    $html = str_replace('{{SERVICES}}', $services_html, $html);
    
    // Replace portfolio info
    $html = str_replace('{{PORTFOLIO_TITLE}}', htmlspecialchars($config['portfolio']['title'] ?? ''), $html);
    $html = str_replace('{{PORTFOLIO_SUBTITLE}}', htmlspecialchars($config['portfolio']['subtitle'] ?? ''), $html);
    $html = str_replace('{{PORTFOLIO_FOOTER}}', htmlspecialchars($config['portfolio']['footer_text'] ?? ''), $html);
    $html = str_replace('{{PORTFOLIO_CTA}}', htmlspecialchars($config['portfolio']['cta_text'] ?? ''), $html);
    
    // Generate portfolio HTML
    $portfolio_html = '';
    if (!empty($config['portfolio']['items'])) {
        foreach ($config['portfolio']['items'] as $item) {
            $portfolio_html .= sprintf(
                '<div class="col-4 col-6-medium col-12-small">
                    <article class="box style2">
                        <a href="%s" class="image featured"><img src="%s" alt="" /></a>
                        <h3><a href="%s">%s</a></h3>
                        <p>%s</p>
                    </article>
                </div>',
                htmlspecialchars($item['link'] ?? '#'),
                htmlspecialchars($item['image'] ?? 'images/pic01.jpg'),
                htmlspecialchars($item['link'] ?? '#'),
                htmlspecialchars($item['title'] ?? ''),
                htmlspecialchars($item['description'] ?? '')
            );
        }
    }
    $html = str_replace('{{PORTFOLIO_ITEMS}}', $portfolio_html, $html);
    
    // Replace contact info
    $html = str_replace('{{CONTACT_TITLE}}', htmlspecialchars($config['contact']['title'] ?? ''), $html);
    $html = str_replace('{{CONTACT_SUBTITLE}}', htmlspecialchars($config['contact']['subtitle'] ?? ''), $html);
    $html = str_replace('{{CONTACT_FORM_ACTION}}', htmlspecialchars($config['contact']['form_action'] ?? '#'), $html);
    
    // Generate navigation HTML
    $nav_html = '';
    if (!empty($config['navigation'])) {
        foreach ($config['navigation'] as $nav_item) {
            $nav_html .= sprintf(
                '<li><a href="%s">%s</a></li>',
                htmlspecialchars($nav_item['href'] ?? '#'),
                htmlspecialchars($nav_item['label'] ?? '')
            );
        }
    }
    $html = str_replace('{{NAVIGATION}}', $nav_html, $html);
    
    // Generate social links HTML
    $social_html = '';
    if (!empty($config['contact']['social_links'])) {
        foreach ($config['contact']['social_links'] as $social) {
            $social_html .= sprintf(
                '<li><a href="%s" class="icon brands %s"><span class="label">%s</span></a></li>',
                htmlspecialchars($social['url'] ?? '#'),
                htmlspecialchars($social['icon'] ?? 'fa-link'),
                htmlspecialchars($social['platform'] ?? '')
            );
        }
    }
    $html = str_replace('{{SOCIAL_LINKS}}', $social_html, $html);
    
    // Replace copyright
    $html = str_replace('{{COPYRIGHT_TEXT}}', htmlspecialchars($config['copyright']['text'] ?? ''), $html);
    $html = str_replace('{{DESIGN_CREDIT}}', htmlspecialchars($config['copyright']['design_credit'] ?? 'Design: HTML5 UP'), $html);
    $html = str_replace('{{DESIGN_URL}}', htmlspecialchars($config['copyright']['design_url'] ?? 'http://html5up.net'), $html);
    
    // Write to index.html
    $result = file_put_contents('index.html', $html);
    
    if ($result === false) {
        throw new Exception('Failed to write index.html');
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Site regenerated successfully from current configuration',
        'bytes_written' => $result
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
