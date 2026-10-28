<?php
declare(strict_types=1);

/**
 * DriveMaster Driving School - SMTP Configuration
 * Loads environment variables from .env via Env loader
 */

require_once __DIR__ . '/../src/Env.php';
use DriveMaster\Env;

return [
    // SMTP Server Settings
    'host'         => (string)Env::get('SMTP_HOST', 'smtp.gmail.com'),
    'port'         => (int)Env::get('SMTP_PORT', 587),
    'encryption'   => (string)Env::get('SMTP_ENCRYPTION', 'tls'), // 'tls', 'ssl', or 'none'
    'auth'         => filter_var(Env::get('SMTP_AUTH', true), FILTER_VALIDATE_BOOLEAN),
    'username'     => (string)Env::get('SMTP_USERNAME', ''),
    'password'     => (string)Env::get('SMTP_PASSWORD', ''),
    'timeout'      => 15,

    // Sender Details
    'from_email'   => (string)Env::get('MAIL_FROM_ADDRESS', ''),
    'from_name'    => (string)Env::get('MAIL_FROM_NAME', 'DriveMaster Driving Academy'),

    // Administrator Email (receives notifications when user submits booking/payment)
    'admin_email'  => (string)Env::get('ADMIN_NOTIFICATION_EMAIL', ''),
    'admin_name'   => (string)Env::get('ADMIN_NAME', 'DriveMaster Admin Operations')
];
