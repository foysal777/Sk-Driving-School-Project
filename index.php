<?php
declare(strict_types=1);

/**
 * DriveMaster Driving School - Central Entry Point
 * Renders pages using the master HTML layout template
 */

require_once __DIR__ . '/templates/render.php';

// 1. Resolve requested page from query parameter (set by .htaccess) or URI path
$page = $_GET['page'] ?? null;

if ($page === null) {
    $uriPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $trimmed = trim((string)$uriPath, '/');
    
    // Strip trailing extensions if any
    $trimmed = preg_replace('/\.(php|html)$/i', '', $trimmed);
    
    $page = ($trimmed === '' || $trimmed === 'index') ? 'home' : $trimmed;
}

$page = strtolower(trim((string)$page));
if ($page === '' || $page === 'index') {
    $page = 'home';
}

renderPage($page);
