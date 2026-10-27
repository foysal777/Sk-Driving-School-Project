<?php
declare(strict_types=1);

/**
 * Unified Endpoint: /backend/api/enrollments.php
 * Handles Course Enrollments, Square Dummy Payment Gateway Requests & Approvals
 */

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Response.php';
require_once __DIR__ . '/../src/Models/Enrollment.php';
require_once __DIR__ . '/../src/Services/Mailer.php';

use DriveMaster\Response;
use DriveMaster\Models\Enrollment;
use DriveMaster\Services\Mailer;

Response::applyCors();

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? $_POST['action'] ?? '';

// ==========================================
// 1. GET: Check Enrollment / Payment Status
// ==========================================
if ($method === 'GET') {
    $ref = trim((string)($_GET['ref'] ?? ''));
    if (empty($ref)) {
        Response::error('Booking reference (ref) parameter is required.', 400);
    }

    $enrollment = Enrollment::findByRef($ref);
    if (!$enrollment) {
        Response::error('Booking reference not found.', 404);
    }

    Response::success([
        'booking_reference' => $enrollment['booking_reference'],
        'student_name'      => $enrollment['student_name'],
        'student_email'     => $enrollment['student_email'],
        'course_name'       => $enrollment['course_name'],
        'course_price'      => (float)$enrollment['course_price'],
        'payment_gateway'   => $enrollment['payment_gateway'] ?? 'square',
        'payment_status'    => $enrollment['payment_status'], // 'payment_requested', 'approved_to_pay', 'paid_in_full', 'rejected'
        'payment_tx_id'     => $enrollment['payment_tx_id'] ?? null,
        'card_last4'        => $enrollment['card_last4'] ?? null,
        'status'            => $enrollment['status'],
        'can_pay'           => ($enrollment['payment_status'] === 'approved_to_pay'),
        'is_paid'           => ($enrollment['payment_status'] === 'paid_in_full'),
        'created_at'        => $enrollment['created_at'],
        'updated_at'        => $enrollment['updated_at'] ?? $enrollment['created_at']
    ], 'Status fetched successfully.');
}

if ($method !== 'POST') {
    Response::error('Method not allowed. Use GET or POST.', 405);
}

// Parse JSON or form payload
$inputRaw = file_get_contents('php://input');
$body = !empty($inputRaw) ? json_decode($inputRaw, true) : [];
if (!is_array($body)) {
    $body = $_POST;
}
if (empty($action) && !empty($body['action'])) {
    $action = $body['action'];
}

// ==========================================
// 2. POST: Complete Square Sandbox Payment
// (Student pays once approved by admin)
// ==========================================
if ($action === 'pay_square' || $action === 'complete_square_payment') {
    $ref = trim((string)($body['booking_reference'] ?? $body['ref'] ?? ''));
    if (empty($ref)) {
        Response::error('Booking reference is required to complete payment.', 400);
    }

    $cardDetails = [
        'card_number' => $body['card_number'] ?? '4242424242424242',
        'exp_month'   => $body['exp_month'] ?? '12',
        'exp_year'    => $body['exp_year'] ?? '28',
        'cvv'         => $body['cvv'] ?? '123',
        'postal_code' => $body['postal_code'] ?? '90210',
        'brand'       => $body['card_brand'] ?? 'Square Sandbox Visa'
    ];

    $result = Enrollment::completeSquarePayment($ref, $cardDetails);

    if (!$result) {
        Response::error('Booking reference not found.', 404);
    }

    if (isset($result['error'])) {
        Response::error($result['message'], 403, ['error_code' => $result['error']]);
    }

    // Trigger SMTP Email Notification: Payment Successful Receipt to User
    $mailInfo = Mailer::getInstance()->notifyUserPaymentSuccessReceipt($result);

    Response::success([
        'booking_reference' => $result['booking_reference'],
        'student_name'      => $result['student_name'],
        'course_name'       => $result['course_name'],
        'course_price'      => (float)$result['course_price'],
        'payment_gateway'   => 'Square Test Sandbox',
        'payment_status'    => 'paid_in_full',
        'payment_tx_id'     => $result['payment_tx_id'],
        'card_last4'        => $result['card_last4'],
        'paid_at'           => $result['paid_at'] ?? date('Y-m-d H:i:s'),
        'status'            => 'confirmed',
        'email_sent'        => $mailInfo['delivered'] ?? false,
        'email_status'      => $mailInfo['status'] ?? 'sent',
        'receipt_url'       => !empty($result['receipt_url']) ? $result['receipt_url'] : '#'
    ], 'Square Sandbox payment of $' . number_format((float)$result['course_price'], 2) . ' authorized & confirmed! Receipt emailed to student via SMTP.', 200);
}

// ==========================================
// 3. POST: Admin Approve Payment Request
// ==========================================
if ($action === 'approve_square') {
    $refOrId = $body['booking_reference'] ?? $body['ref'] ?? $body['id'] ?? '';
    if (empty($refOrId)) {
        Response::error('Booking reference or ID is required.', 400);
    }

    $result = Enrollment::approvePayment($refOrId);
    if (!$result) {
        Response::error('Booking not found.', 404);
    }

    // Trigger SMTP Email Notification: Notify Student that payment is approved!
    $mailInfo = Mailer::getInstance()->notifyUserPaymentApproved($result);

    Response::success([
        'enrollment'   => $result,
        'email_sent'   => $mailInfo['delivered'] ?? false,
        'email_status' => $mailInfo['status'] ?? 'sent'
    ], 'Payment request approved! Approval notification sent to student email via SMTP.');
}

// ==========================================
// 4. POST: Stage 1 - Request Square Payment
// (Student submits booking to admin panel)
// ==========================================
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

// Clean price if passed as string like "$420"
$rawPrice = $body['course_price'] ?? 0;
if (is_string($rawPrice)) {
    $rawPrice = (float)str_replace(['$', ',', ' '], '', $rawPrice);
}
$body['course_price'] = (float)$rawPrice;

// If payment gateway is Square (or requested via Square button)
$isSquare = ($body['payment_gateway'] ?? 'square') === 'square';

if ($isSquare) {
    $enrollment = Enrollment::requestSquarePayment($body);
    
    // Trigger SMTP Email Notification: Notify Admin about new payment request
    $mailInfo = Mailer::getInstance()->notifyAdminNewPaymentRequest($enrollment);

    Response::success(
        [
            'enrollment'        => $enrollment,
            'booking_reference' => $enrollment['booking_reference'],
            'admin_email_sent'  => $mailInfo['delivered'] ?? false,
            'email_status'      => $mailInfo['status'] ?? 'sent'
        ],
        'Square Sandbox payment request sent to Admin Panel! Notification emailed to Admin via SMTP.',
        201
    );
}

$enrollment = Enrollment::create($body);
Response::success(
    [
        'enrollment'        => $enrollment,
        'booking_reference' => $enrollment['booking_reference']
    ],
    'Enrollment request received successfully. Reference: ' . $enrollment['booking_reference'],
    201
);
