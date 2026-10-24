<?php
declare(strict_types=1);

require_once __DIR__ . '/../backend/src/Auth.php';

use DriveMaster\Auth;

Auth::logout();
header('Location: /admin/login.php?message=logged_out');
exit();
