<?php
declare(strict_types=1);

/**
 * DriveMaster Driving School - Central Entry Point
 * Renders pages using the master HTML layout template
 */

require_once __DIR__ . '/templates/render.php';

// 1. Strip trailing slashes and sanitize invalid queries
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$parsed = parse_url($requestUri);
$path = $parsed['path'] ?? '/';
$query = $parsed['query'] ?? '';

if ($path !== '/' && str_ends_with($path, '/')) {
    $cleanPath = rtrim($path, '/');
    header("Location: {$cleanPath}", true, 301);
    exit();
}

if (($path === '/home' || $path === '/') && !empty($query)) {
    parse_str($query, $params);
    unset($params['page']);
    if (!empty($params)) {
        header("Location: /home", true, 301);
        exit();
    }
}

// 2. Resolve requested page from query parameter (set by .htaccess) or URI path
$page = $_GET['page'] ?? null;

if ($page === null) {
    $trimmed = trim((string)$path, '/');
    
    // Strip trailing extensions if any
    $trimmed = preg_replace('/\.(php|html)$/i', '', $trimmed);
    
    $page = ($trimmed === '' || $trimmed === 'index') ? 'home' : $trimmed;
}

$page = strtolower(trim((string)$page));
if ($page === '' || $page === 'index') {
    $page = 'home';
}

renderPage($page);
