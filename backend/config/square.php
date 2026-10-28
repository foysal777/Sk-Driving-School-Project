<?php
declare(strict_types=1);

/**
 * DriveMaster Driving School - Square Payment Gateway Configuration
 * Loads environment variables from .env via Env loader
 */

require_once __DIR__ . '/../src/Env.php';
use DriveMaster\Env;

return [
    'environment'    => (string)Env::get('SQUARE_ENVIRONMENT', 'sandbox'),
    'application_id' => (string)Env::get('SQUARE_SANDBOX_APP_ID', ''),
    'access_token'   => (string)Env::get('SQUARE_SANDBOX_TOKEN', ''),
    'location_id'    => (string)Env::get('SQUARE_SANDBOX_LOCATION', ''),
    'currency'       => (string)Env::get('SQUARE_CURRENCY', 'USD')
];
