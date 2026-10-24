<?php
declare(strict_types=1);

/**
 * Direct Endpoint: GET /backend/api/courses.php
 */

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Response.php';
require_once __DIR__ . '/../src/Models/Course.php';

use DriveMaster\Response;
use DriveMaster\Models\Course;

Response::applyCors();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    Response::error('Method not allowed. Use GET.', 405);
}

$courses = Course::all();
Response::success($courses, 'Courses retrieved successfully.');
