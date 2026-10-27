<?php
declare(strict_types=1);

/**
 * DriveMaster Driving School - Database Configuration
 * Pure PHP 8+ Core Architecture with .env Support
 */

require_once __DIR__ . '/../src/Env.php';
use DriveMaster\Env;

return [
    'driver'    => 'mysql',
    'host'      => (string)Env::get('DB_HOST', '127.0.0.1'),
    'port'      => (int)Env::get('DB_PORT', 3306),
    'database'  => (string)Env::get('DB_NAME', 'drivemaster_db'),
    'username'  => (string)Env::get('DB_USER', 'root'),
    'password'  => (string)Env::get('DB_PASS', ''),
    'charset'   => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'options'   => [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
    ],
];
