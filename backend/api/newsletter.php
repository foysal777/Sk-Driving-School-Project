<?php
declare(strict_types=1);

/**
 * Direct Endpoint: POST /backend/api/newsletter.php
 * Body: { "email": "student@example.com", "source": "footer", "website": "" }
 */

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Response.php';
require_once __DIR__ . '/../src/Models/Newsletter.php';

use DriveMaster\Response;
use DriveMaster\Models\Newsletter;

Response::applyCors();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::error('Method not allowed. Use POST.', 405);
}

$inputRaw = file_get_contents('php://input');
$body = !empty($inputRaw) ? json_decode($inputRaw, true) : [];
if (!is_array($body)) {
    $body = $_POST;
}

// Honeypot: real users never fill this hidden field
if (!empty($body['website'])) {
    Response::success(null, 'Thanks for subscribing!', 200);
}

$email = trim((string)($body['email'] ?? ''));

if ($email === '' || strlen($email) > 254 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    Response::validationError(['email' => 'Please enter a valid email address.']);
}

$result = Newsletter::subscribe($email, (string)($body['source'] ?? 'website'));

if ($result['already']) {
    Response::success(
        ['email' => $result['subscriber']['email'], 'already_subscribed' => true],
        "You're already on our list — thanks for staying with us!",
        200
    );
}

Response::success(
    ['email' => $result['subscriber']['email'], 'already_subscribed' => false],
    "You're subscribed! Driving tips and exclusive offers are on their way.",
    201
);
