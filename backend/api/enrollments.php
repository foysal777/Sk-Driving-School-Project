<?php
declare(strict_types=1);

/**
 * Direct Endpoint: POST /backend/api/enrollments.php
 */

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Response.php';
require_once __DIR__ . '/../src/Models/Enrollment.php';

use DriveMaster\Response;
use DriveMaster\Models\Enrollment;

Response::applyCors();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::error('Method not allowed. Use POST.', 405);
}

$inputRaw = file_get_contents('php://input');
$body = !empty($inputRaw) ? json_decode($inputRaw, true) : [];
if (!is_array($body)) {
    $body = $_POST;
}

$name = trim((string)($body['student_name'] ?? ''));
$email = trim((string)($body['student_email'] ?? ''));
$courseName = trim((string)($body['course_name'] ?? ''));

$errors = [];
if (empty($name)) {
    $errors['student_name'] = 'Student name is required.';
}
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['student_email'] = 'A valid email address is required.';
}
if (empty($courseName)) {
    $errors['course_name'] = 'Course package selection is required.';
}

if (!empty($errors)) {
    Response::validationError($errors);
}

$enrollment = Enrollment::create($body);

Response::success(
    $enrollment,
    'Enrollment request received successfully. Reference: ' . $enrollment['booking_reference'],
    201
);
