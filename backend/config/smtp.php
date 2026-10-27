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
    'username'     => (string)Env::get('SMTP_USERNAME', 'foysal.cse11@gmail.com'),
    'password'     => (string)Env::get('SMTP_PASSWORD', 'tbewlgmschbcgmjl'),
    'timeout'      => 15,

    // Sender Details
    'from_email'   => (string)Env::get('MAIL_FROM_ADDRESS', 'foysal.cse11@gmail.com'),
    'from_name'    => (string)Env::get('MAIL_FROM_NAME', 'DriveMaster Driving Academy'),

    // Administrator Email (receives notifications when user submits payment)
    'admin_email'  => (string)Env::get('ADMIN_NOTIFICATION_EMAIL', 'foysal.cse11@gmail.com'),
    'admin_name'   => (string)Env::get('ADMIN_NAME', 'DriveMaster Admin Operations'),

    // Square Sandbox Settings
    'square' => [
        'environment'    => (string)Env::get('SQUARE_ENVIRONMENT', 'sandbox'),
        'application_id' => (string)Env::get('SQUARE_SANDBOX_APP_ID', 'sandbox-sq0idb-ZLtIuIX9uJcAbZE0aN8SSQ'),
        'access_token'   => (string)Env::get('SQUARE_SANDBOX_TOKEN', 'EAAAlws4w5ChIW1aQMznDsBNEwTJ3t9Oag7wWCGn7hueXftWEheLW0K-ncVDHh4k'),
        'location_id'    => (string)Env::get('SQUARE_SANDBOX_LOCATION', 'LH2K1E77XZPS1'),
        'currency'       => (string)Env::get('SQUARE_CURRENCY', 'USD')
    ]
];
