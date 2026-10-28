<?php
declare(strict_types=1);

/**
 * Clean URL Router for PHP Built-in Server
 * Renders pages using the master HTML layout template
 */

require_once __DIR__ . '/templates/render.php';

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH));
$filePath = __DIR__ . $uri;

// 1. Redirect legacy .html to clean URL (301 Permanent Redirect)
if (preg_match('#^/(index|home|about|courses|gallery|contact|cart|product)\.html$#i', $uri, $matches)) {
    $name = strtolower($matches[1]);
    $target = ($name === 'index') ? '/home' : '/' . $name;
    header("Location: {$target}", true, 301);
    exit();
}

// 2. If it is an existing static file or backend/admin script, serve directly
if ($uri !== '/' && file_exists($filePath) && !is_dir($filePath)) {
    return false;
}

// 3. If directory has index.php (e.g. /admin/, /backend/swagger/), let PHP handle it
if (is_dir($filePath) && file_exists(rtrim($filePath, '/') . '/index.php')) {
    return false;
}

// 4. Redirect trailing slashes on clean routes (e.g. /home/ or /home/?aaa -> /home)
if ($uri !== '/' && str_ends_with($uri, '/')) {
    $cleanTarget = rtrim($uri, '/');
    header("Location: {$cleanTarget}", true, 301);
    exit();
}

// 5. Prevent and redirect invalid query strings on /home
$rawQuery = $_SERVER['QUERY_STRING'] ?? '';
if (!empty($rawQuery) && ($uri === '/' || $uri === '/home')) {
    header("Location: /home", true, 301);
    exit();
}

// 6. Map clean URL routes
$routes = [
    '/'        => 'home',
    '/home'    => 'home',
    '/about'   => 'about',
    '/courses' => 'courses',
    '/gallery' => 'gallery',
    '/contact' => 'contact',
    '/product' => 'product',
    '/cart'    => 'cart',
];

if (isset($routes[$uri])) {
    renderPage($routes[$uri]);
    exit();
}

// Fallback to let PHP handle standard 404 or remaining scripts
return false;
