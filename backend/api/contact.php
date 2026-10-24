<?php
declare(strict_types=1);

/**
 * Direct Endpoint: POST /backend/api/contact.php
 */

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Response.php';
require_once __DIR__ . '/../src/Models/Inquiry.php';

use DriveMaster\Response;
use DriveMaster\Models\Inquiry;

Response::applyCors();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::error('Method not allowed. Use POST.', 405);
}

$inputRaw = file_get_contents('php://input');
$body = !empty($inputRaw) ? json_decode($inputRaw, true) : [];
if (!is_array($body)) {
    $body = $_POST;
}

$name = trim((string)($body['full_name'] ?? $body['name'] ?? ''));
$email = trim((string)($body['email'] ?? ''));
$message = trim((string)($body['message'] ?? ''));

$errors = [];
if (empty($name)) {
    $errors['full_name'] = 'Full Name is required.';
}
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'A valid email address is required.';
}
if (empty($message)) {
    $errors['message'] = 'Message content cannot be empty.';
}

if (!empty($errors)) {
    Response::validationError($errors);
}

$inquiry = Inquiry::create([
    'full_name'          => $name,
    'email'              => $email,
    'phone'              => $body['phone'] ?? '',
    'subject'            => $body['subject'] ?? 'General Inquiry',
    'pickup_location'    => $body['pickup_location'] ?? $body['location'] ?? '',
    'timeline'           => $body['timeline'] ?? '',
    'preferred_schedule' => $body['preferred_schedule'] ?? 'Flexible',
    'message'            => $message,
]);

Response::success(
    $inquiry,
    'Thank you! Your message has been received. Our instructor team will contact you shortly.',
    201
);
