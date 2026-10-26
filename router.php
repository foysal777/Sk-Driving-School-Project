<?php
declare(strict_types=1);

/**
 * Clean URL Router for PHP Built-in Server
 * Enables /home, /about, /courses, /gallery, /contact without .html extension
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH));

// Check if request is for existing non-html static asset (css, js, images, php api, admin, etc.)
$filePath = __DIR__ . $uri;

// 1. Redirect legacy .html to clean URL (301 Permanent Redirect)
if (preg_match('#^/(index|home|about|courses|gallery|contact)\.html$#i', $uri, $matches)) {
    $name = strtolower($matches[1]);
    $target = ($name === 'index') ? '/home' : '/' . $name;
    header("Location: {$target}", true, 301);
    exit();
}

// 2. If it is an existing file (and not a directory), serve directly (e.g. backend/api/..., assets/..., admin/...)
if ($uri !== '/' && file_exists($filePath) && !is_dir($filePath)) {
    return false;
}

// 3. If directory has index.php (e.g. /admin/), let PHP handle it
if (is_dir($filePath) && file_exists(rtrim($filePath, '/') . '/index.php')) {
    return false;
}

// 4. Map clean URL routes
$routes = [
    '/'        => 'index.html',
    '/home'    => 'index.html',
    '/about'   => 'about.html',
    '/courses' => 'courses.html',
    '/gallery' => 'gallery.html',
    '/contact' => 'contact.html',
];

$normalizedUri = rtrim($uri, '/');
if ($normalizedUri === '') {
    $normalizedUri = '/';
}

if (isset($routes[$normalizedUri])) {
    $targetFile = __DIR__ . '/' . $routes[$normalizedUri];
    if (file_exists($targetFile)) {
        header('Content-Type: text/html; charset=UTF-8');
        readfile($targetFile);
        exit();
    }
}

// Check if a corresponding .html file exists in root (e.g. /my-page -> my-page.html)
$candidateHtml = __DIR__ . '/' . ltrim($normalizedUri, '/') . '.html';
if (file_exists($candidateHtml)) {
    header('Content-Type: text/html; charset=UTF-8');
    readfile($candidateHtml);
    exit();
}

// Return false to let PHP handle standard 404 or remaining scripts
return false;
