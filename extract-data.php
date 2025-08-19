<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();
}

try {
    // Read the current index.html file
    $indexPath = __DIR__ . '/index.html';
    
    if (!file_exists($indexPath)) {
        throw new Exception('index.html file not found');
    }
    
    $html = file_get_contents($indexPath);
    if ($html === false) {
        throw new Exception('Failed to read index.html');
    }
    
    // Initialize extracted data structure
    $extractedData = [
        'site' => [
            'title' => '',
            'meta_description' => ''
        ],
        'personal' => [
            'name' => '',
            'tagline' => '',
            'profile_image' => 'images/pic00.jpg',
            'cta_text' => ''
        ],
        'work' => [
            'title' => '',
            'subtitle' => '',
            'services' => [],
            'footer_text' => '',
            'cta_text' => ''
        ],
        'portfolio' => [
            'title' => '',
            'subtitle' => '',
            'items' => [],
            'footer_text' => '',
            'cta_text' => ''
        ],
        'contact' => [
            'title' => '',
            'subtitle' => '',
            'form_action' => '#',
            'social_links' => []
        ],
        'navigation' => [],
        'copyright' => [
            'text' => '',
            'design_credit' => 'Design: HTML5 UP',
            'design_url' => 'http://html5up.net'
        ]
    ];
    
    // Extract title from <title> tag
    if (preg_match('/<title[^>]*>(.*?)<\/title>/i', $html, $matches)) {
        $extractedData['site']['title'] = trim($matches[1]);
    }
    
    // Extract meta description
    if (preg_match('/<meta\s+name=["\']description["\']\s+content=["\']([^"\']*)["\'][^>]*>/i', $html, $matches)) {
        $extractedData['site']['meta_description'] = trim($matches[1]);
    }
    
    // Extract personal name from the main heading
    if (preg_match('/<h1[^>]*><strong[^>]*>(.*?)<\/strong><\/h1>/i', $html, $matches)) {
        $extractedData['personal']['name'] = trim($matches[1]);
    }
    
    // Extract personal tagline (content in the <p> tag after the name)
    if (preg_match('/<h1[^>]*>.*?<\/h1>\s*<\/header>\s*<p[^>]*>(.*?)<\/p>/is', $html, $matches)) {
        $extractedData['personal']['tagline'] = trim($matches[1]);
    }
    
    // Extract profile image source
    if (preg_match('/<img\s+src=["\']([^"\']*)["\'][^>]*alt=["\'][^"\']*["\']/i', $html, $matches)) {
        $extractedData['personal']['profile_image'] = trim($matches[1]);
    }
    
    // Extract CTA text from the main button
    if (preg_match('/<a\s+href=["\']#work["\'][^>]*>(.*?)<\/a>/i', $html, $matches)) {
        $extractedData['personal']['cta_text'] = trim($matches[1]);
    }
    
    // Extract work section title and subtitle
    if (preg_match('/<article\s+id=["\']work["\'][^>]*>.*?<h2[^>]*>(.*?)<\/h2>\s*<p[^>]*>(.*?)<\/p>/is', $html, $matches)) {
        $extractedData['work']['title'] = trim($matches[1]);
        $extractedData['work']['subtitle'] = trim($matches[2]);
    }
    
    // Extract work footer and CTA
    if (preg_match('/<article\s+id=["\']work["\'][^>]*>.*?<footer[^>]*>.*?<p[^>]*>(.*?)<\/p>\s*<a[^>]*>(.*?)<\/a>/is', $html, $matches)) {
        $extractedData['work']['footer_text'] = trim($matches[1]);
        $extractedData['work']['cta_text'] = trim($matches[2]);
    }
    
    // Extract portfolio section title and subtitle
    if (preg_match('/<article\s+id=["\']portfolio["\'][^>]*>.*?<h2[^>]*>(.*?)<\/h2>\s*<p[^>]*>(.*?)<\/p>/is', $html, $matches)) {
        $extractedData['portfolio']['title'] = trim($matches[1]);
        $extractedData['portfolio']['subtitle'] = trim($matches[2]);
    }
    
    // Extract portfolio footer and CTA
    if (preg_match('/<article\s+id=["\']portfolio["\'][^>]*>.*?<footer[^>]*>.*?<p[^>]*>(.*?)<\/p>\s*<a[^>]*>(.*?)<\/a>/is', $html, $matches)) {
        $extractedData['portfolio']['footer_text'] = trim($matches[1]);
        $extractedData['portfolio']['cta_text'] = trim($matches[2]);
    }
    
    // Extract contact section title and subtitle
    if (preg_match('/<article\s+id=["\']contact["\'][^>]*>.*?<h2[^>]*>(.*?)<\/h2>\s*<p[^>]*>(.*?)<\/p>/is', $html, $matches)) {
        $extractedData['contact']['title'] = trim($matches[1]);
        $extractedData['contact']['subtitle'] = trim($matches[2]);
    }
    
    // Extract form action
    if (preg_match('/<form[^>]*action=["\']([^"\']*)["\'][^>]*>/i', $html, $matches)) {
        $extractedData['contact']['form_action'] = trim($matches[1]);
    }
    
    // Extract copyright text
    if (preg_match('/<li[^>]*>(.*?)<\/li><li[^>]*>Design: HTML5 UP/i', $html, $matches)) {
        $extractedData['copyright']['text'] = trim($matches[1]);
    }
    
    // Extract navigation items (look for navigation placeholders or existing nav items)
    // Since the template uses {{NAVIGATION_ITEMS}}, we'll provide default nav items
    if (strpos($html, '{{NAVIGATION_ITEMS}}') !== false) {
        $extractedData['navigation'] = [
            ['label' => 'Top', 'href' => '#top'],
            ['label' => 'Work', 'href' => '#work'],
            ['label' => 'Portfolio', 'href' => '#portfolio'],
            ['label' => 'Contact', 'href' => '#contact']
        ];
    }
    
    // Add default content if sections are empty or have placeholders
    if (empty($extractedData['work']['title']) || strpos($html, '{{SERVICES_ITEMS}}') !== false) {
        if (empty($extractedData['work']['title'])) {
            $extractedData['work']['title'] = "Here's what I do.";
            $extractedData['work']['subtitle'] = "Brief description of your services.";
            $extractedData['work']['footer_text'] = "Ready to work together?";
            $extractedData['work']['cta_text'] = "See some of my recent work";
        }
        $extractedData['work']['services'] = [
            [
                'title' => 'Web Development',
                'description' => 'Creating modern, responsive websites and web applications.',
                'icon' => 'fa-code',
                'icon_solid' => false
            ],
            [
                'title' => 'UI/UX Design', 
                'description' => 'Designing user-friendly interfaces and experiences.',
                'icon' => 'fa-paint-brush',
                'icon_solid' => false
            ]
        ];
    }
    
    // Add default portfolio content
    if (empty($extractedData['portfolio']['title']) || strpos($html, '{{PORTFOLIO_ITEMS}}') !== false) {
        if (empty($extractedData['portfolio']['title'])) {
            $extractedData['portfolio']['title'] = "Here's some of my recent work.";
            $extractedData['portfolio']['subtitle'] = "A collection of projects I've worked on.";
            $extractedData['portfolio']['footer_text'] = "Like what you see?";
            $extractedData['portfolio']['cta_text'] = "Get in touch with me";
        }
        $extractedData['portfolio']['items'] = [
            [
                'title' => 'Project One',
                'description' => 'Description of your first project.',
                'image' => 'images/pic01.jpg',
                'link' => '#'
            ],
            [
                'title' => 'Project Two',
                'description' => 'Description of your second project.',
                'image' => 'images/pic02.jpg',
                'link' => '#'
            ]
        ];
    }
    
    // Add default contact content
    if (empty($extractedData['contact']['title'])) {
        $extractedData['contact']['title'] = "Get in touch with me.";
        $extractedData['contact']['subtitle'] = "Let's work together on something great.";
    }
    
    // Add default site title if empty
    if (empty($extractedData['site']['title'])) {
        $extractedData['site']['title'] = "Miniport by HTML5 UP";
        $extractedData['site']['meta_description'] = "Personal portfolio website";
    }
    
    // Add default personal tagline if empty
    if (empty($extractedData['personal']['tagline'])) {
        $extractedData['personal']['tagline'] = "Tell visitors about yourself and what makes you unique.";
    }
    
    // Add default copyright text if empty
    if (empty($extractedData['copyright']['text'])) {
        $extractedData['copyright']['text'] = "© " . $extractedData['personal']['name'] . ". All rights reserved.";
    }
    
    // Add sample social links
    $extractedData['contact']['social_links'] = [
        [
            'platform' => 'Twitter',
            'url' => 'https://twitter.com/',
            'icon' => 'fa-twitter'
        ],
        [
            'platform' => 'LinkedIn',
            'url' => 'https://linkedin.com/in/',
            'icon' => 'fa-linkedin'
        ]
    ];
    
    // Clean up empty values
    foreach ($extractedData as &$section) {
        if (is_array($section)) {
            foreach ($section as &$value) {
                if (is_string($value)) {
                    $value = html_entity_decode($value, ENT_QUOTES, 'UTF-8');
                    $value = strip_tags($value);
                    $value = trim($value);
                }
            }
        }
    }
    
    echo json_encode($extractedData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ], JSON_PRETTY_PRINT);
}
?>
