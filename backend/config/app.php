<?php
declare(strict_types=1);

/**
 * DriveMaster Driving School - App Configuration
 * Pure PHP 8+ Core Architecture
 */

return [
    'name'           => 'DriveMaster Driving School API & Admin',
    'version'        => '1.0.0',
    'environment'    => $_ENV['APP_ENV'] ?? 'development',
    'debug'          => (bool)($_ENV['APP_DEBUG'] ?? true),
    'timezone'       => 'UTC',
    'url'            => $_ENV['APP_URL'] ?? 'http://localhost:8000',
    'admin_email'    => 'admin@drivemaster-school.com',
    'session_name'   => 'drivemaster_admin_session',
    'session_lifetime' => 86400, // 24 hours
    'cors'           => [
        'allowed_origins' => ['*'],
        'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
        'allowed_headers' => ['Content-Type', 'Authorization', 'X-Requested-With'],
    ],
];
