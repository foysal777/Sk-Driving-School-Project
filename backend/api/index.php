<?php
declare(strict_types=1);

/**
 * DriveMaster Driving School - Core REST API Router (PHP 8+ Pure)
 */

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Response.php';
require_once __DIR__ . '/../src/Auth.php';
require_once __DIR__ . '/../src/Models/Inquiry.php';
require_once __DIR__ . '/../src/Models/Enrollment.php';
require_once __DIR__ . '/../src/Models/Course.php';

use DriveMaster\Response;
use DriveMaster\Auth;
use DriveMaster\Database;
use DriveMaster\Models\Inquiry;
use DriveMaster\Models\Enrollment;
use DriveMaster\Models\Course;

// Enable CORS
Response::applyCors();

// Parse request path and method
$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Normalize route (strip prefix e.g. /backend/api/ or /api/)
$route = preg_replace('#^/+(backend/api|api)#i', '', $uri);
$route = trim($route, '/');

// Parse JSON input body if present
$inputRaw = file_get_contents('php://input');
$body = !empty($inputRaw) ? json_decode($inputRaw, true) : [];
if (!is_array($body)) {
    $body = $_POST;
}

// ==============================================================================
// PUBLIC ROUTES
// ==============================================================================

// Health Check
if ($route === '' || $route === 'health') {
    $db = Database::getInstance();
    Response::success([
        'status'       => 'operational',
        'php_version'  => PHP_VERSION,
        'database'     => $db->isAvailable() ? 'connected' : 'disconnected (using fallback)',
        'server_time'  => date('c'),
        'swagger_ui'   => '/backend/swagger/',
        'admin_panel'  => '/admin/'
    ], 'DriveMaster API is running smoothly.');
}

// POST /contact (Submit Email Contact Inquiry)
if ($route === 'contact' && $method === 'POST') {
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
}

// POST /enrollments (Submit Course Booking)
if ($route === 'enrollments' && $method === 'POST') {
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
}

// GET /courses (List active courses)
if ($route === 'courses' && $method === 'GET') {
    $courses = Course::all();
    Response::success($courses, 'Courses retrieved successfully.');
}

// GET /courses/{idOrSlug}
if (preg_match('#^courses/([^/]+)$#', $route, $matches) && $method === 'GET') {
    $course = Course::find($matches[1]);
    if (!$course) {
        Response::notFound('Course package not found.');
    }
    Response::success($course, 'Course details retrieved.');
}

// POST /admin/login
if ($route === 'admin/login' && $method === 'POST') {
    $username = trim((string)($body['username'] ?? $body['email'] ?? ''));
    $password = (string)($body['password'] ?? '');

    if (empty($username) || empty($password)) {
        Response::validationError(['login' => 'Username and password are required.']);
    }

    $admin = Auth::login($username, $password);
    if (!$admin) {
        Response::unauthorized('Invalid username/email or password.');
    }

    Response::success([
        'user'  => $admin,
        'token' => $admin['token']
    ], 'Authentication successful.');
}

// ==============================================================================
// PROTECTED ADMIN ROUTES (Require Bearer Token or Session)
// ==============================================================================

if (str_starts_with($route, 'admin/')) {
    $admin = Auth::requireAuth();

    // GET /admin/me
    if ($route === 'admin/me' && $method === 'GET') {
        Response::success($admin, 'Admin profile retrieved.');
    }

    // POST /admin/logout
    if ($route === 'admin/logout' && $method === 'POST') {
        Auth::logout();
        Response::success(null, 'Logged out successfully.');
    }

    // GET /admin/dashboard
    if ($route === 'admin/dashboard' && $method === 'GET') {
        $inquiryStats = Inquiry::countByStatus();
        $enrollmentStats = Enrollment::countStats();
        $recentInquiries = Inquiry::all(['status' => 'all']);
        $recentEnrollments = Enrollment::all(['status' => 'all']);

        Response::success([
            'kpis' => [
                'total_inquiries'   => $inquiryStats['total'],
                'new_inquiries'     => $inquiryStats['new'],
                'total_bookings'    => $enrollmentStats['total_bookings'],
                'active_students'   => $enrollmentStats['confirmed'],
                'projected_revenue' => $enrollmentStats['revenue'],
            ],
            'recent_inquiries'   => array_slice($recentInquiries, 0, 5),
            'recent_enrollments' => array_slice($recentEnrollments, 0, 5),
        ], 'Dashboard statistics retrieved.');
    }

    // GET /admin/inquiries
    if ($route === 'admin/inquiries' && $method === 'GET') {
        $status = $_GET['status'] ?? 'all';
        $search = $_GET['search'] ?? '';
        $inquiries = Inquiry::all(['status' => $status, 'search' => $search]);
        Response::success($inquiries, 'Inquiries retrieved.');
    }

    // PUT /admin/inquiries/{id}/status
    if (preg_match('#^admin/inquiries/(\d+)/status$#', $route, $matches) && ($method === 'PUT' || $method === 'POST')) {
        $id = (int)$matches[1];
        $newStatus = $body['status'] ?? 'contacted';
        $notes = $body['admin_notes'] ?? null;

        Inquiry::updateStatus($id, $newStatus, $notes);
        Response::success(['id' => $id, 'status' => $newStatus], 'Inquiry status updated successfully.');
    }

    // DELETE /admin/inquiries/{id}
    if (preg_match('#^admin/inquiries/(\d+)$#', $route, $matches) && $method === 'DELETE') {
        $id = (int)$matches[1];
        Inquiry::delete($id);
        Response::success(['id' => $id], 'Inquiry deleted successfully.');
    }

    // GET /admin/enrollments
    if ($route === 'admin/enrollments' && $method === 'GET') {
        $status = $_GET['status'] ?? 'all';
        $enrollments = Enrollment::all(['status' => $status]);
        Response::success($enrollments, 'Enrollments retrieved.');
    }

    // PUT /admin/enrollments/{id}/status
    if (preg_match('#^admin/enrollments/(\d+)/status$#', $route, $matches) && ($method === 'PUT' || $method === 'POST')) {
        $id = (int)$matches[1];
        $newStatus = $body['status'] ?? 'confirmed';
        $payment = $body['payment_status'] ?? null;

        Enrollment::updateStatus($id, $newStatus, $payment);
        Response::success(['id' => $id, 'status' => $newStatus], 'Enrollment status updated.');
    }

    // GET /admin/courses
    if ($route === 'admin/courses' && $method === 'GET') {
        $courses = Course::all('all');
        Response::success($courses, 'All courses retrieved for admin.');
    }

    // POST /admin/courses
    if ($route === 'admin/courses' && $method === 'POST') {
        $course = Course::create($body);
        Response::success($course, 'New course created successfully.', 201);
    }

    // PUT /admin/courses/{id}
    if (preg_match('#^admin/courses/(\d+)$#', $route, $matches) && ($method === 'PUT' || $method === 'POST')) {
        $id = (int)$matches[1];
        Course::update($id, $body);
        Response::success(['id' => $id], 'Course updated successfully.');
    }

    // DELETE /admin/courses/{id}
    if (preg_match('#^admin/courses/(\d+)$#', $route, $matches) && $method === 'DELETE') {
        $id = (int)$matches[1];
        Course::delete($id);
        Response::success(['id' => $id], 'Course deleted successfully.');
    }
}

// Fallback: 404 Not Found
Response::notFound("Endpoint '/{$route}' does not exist on DriveMaster API.");
