<?php
declare(strict_types=1);

/**
 * DriveMaster Driving School - SMTP Configuration
 * Configure your SMTP credentials here (e.g., Gmail, Mailtrap, Brevo, SendGrid, or custom host)
 */

return [
    // SMTP Server Settings
    'host'         => $_ENV['SMTP_HOST'] ?? 'sandbox.smtp.mailtrap.io',
    'port'         => (int)($_ENV['SMTP_PORT'] ?? 587),
    'encryption'   => $_ENV['SMTP_ENCRYPTION'] ?? 'tls', // 'tls', 'ssl', or 'none'
    'auth'         => (bool)($_ENV['SMTP_AUTH'] ?? true),
    'username'     => $_ENV['SMTP_USERNAME'] ?? 'mailtrap_sandbox_user',
    'password'     => $_ENV['SMTP_PASSWORD'] ?? 'mailtrap_sandbox_pass',
    'timeout'      => 10,

    // Sender Details
    'from_email'   => $_ENV['MAIL_FROM_ADDRESS'] ?? 'notifications@drivemaster-school.com',
    'from_name'    => $_ENV['MAIL_FROM_NAME'] ?? 'DriveMaster Driving Academy',

    // Administrator Email (receives notifications when user submits payment)
    'admin_email'  => $_ENV['ADMIN_NOTIFICATION_EMAIL'] ?? 'admin@drivemaster-school.com',
    'admin_name'   => 'DriveMaster Admin Operations',

    // Square Sandbox Settings
    'square' => [
        'environment'    => 'sandbox',
        'application_id' => $_ENV['SQUARE_SANDBOX_APP_ID'] ?? 'sandbox-sq0idb-drivemaster-demo-2026',
        'access_token'   => $_ENV['SQUARE_SANDBOX_TOKEN'] ?? 'EAAAEOtest_sandbox_token_drivemaster_live',
        'location_id'    => $_ENV['SQUARE_SANDBOX_LOCATION'] ?? 'LSANDBOX_LOC_01',
        'currency'       => 'USD'
    ]
];
