<?php
declare(strict_types=1);

require_once __DIR__ . '/../backend/src/Database.php';
require_once __DIR__ . '/../backend/src/Auth.php';

use DriveMaster\Auth;

Auth::startSession();

// If already logged in, redirect to dashboard
if (Auth::check()) {
    header('Location: /admin/index.php');
    exit();
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = (string)($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error = 'Please enter both username/email and password.';
    } else {
        $admin = Auth::login($username, $password);
        if ($admin) {
            header('Location: /admin/index.php');
            exit();
        } else {
            $error = 'Invalid credentials. Please verify your login details.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login | DriveMaster Academy</title>
  <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🔐</text></svg>">
  <link rel="stylesheet" href="../assets/vendor/fonts/fonts.css">
  <link rel="stylesheet" href="../assets/vendor/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/vendor/bootstrap-icons/bootstrap-icons.min.css">
  <style>
    :root {
      --primary: #2A6B5C;
      --primary-dark: #183e35;
      --accent-green: #00b060;
    }
    body {
      background: radial-gradient(circle at top right, #1e4d40 0%, #133a30 45%, #0b1f1a 100%);
      font-family: 'Plus Jakarta Sans', sans-serif;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1.5rem;
    }
    .login-box {
      background: #ffffff;
      border-radius: 24px;
      box-shadow: 0 25px 60px rgba(0, 0, 0, 0.35);
      max-width: 440px;
      width: 100%;
      padding: 2.75rem 2.25rem;
      position: relative;
      overflow: hidden;
    }
    .login-box::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 6px;
      background: linear-gradient(90deg, var(--accent-green) 0%, var(--primary) 100%);
    }
    .brand-icon {
      width: 52px;
      height: 52px;
      border-radius: 14px;
      background: rgba(42, 107, 92, 0.12);
      color: var(--primary);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
      margin: 0 auto 1.25rem;
    }
    .form-control {
      border: 1px solid #cbd5e1;
      border-radius: 10px;
      padding: 0.75rem 1rem;
      font-size: 0.95rem;
    }
    .form-control:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(42, 107, 92, 0.2);
    }
    .btn-login {
      background: linear-gradient(135deg, var(--primary) 0%, #174238 100%);
      color: #ffffff;
      font-weight: 700;
      padding: 0.85rem;
      border-radius: 10px;
      border: none;
      width: 100%;
      transition: all 0.25s ease;
    }
    .btn-login:hover {
      background: linear-gradient(135deg, #22594c 0%, #11332b 100%);
      color: #ffffff;
      transform: translateY(-2px);
    }
    .demo-credential-chip {
      background: #f1f5f9;
      border: 1px dashed #cbd5e1;
      border-radius: 10px;
      padding: 0.85rem 1rem;
      font-size: 0.82rem;
      color: #475569;
      cursor: pointer;
      transition: all 0.2s ease;
    }
    .demo-credential-chip:hover {
      background: #e2e8f0;
      border-color: var(--primary);
    }
  </style>
</head>
<body>

  <div class="login-box">
    <div class="brand-icon">
      <i class="bi bi-shield-lock-fill"></i>
    </div>
    <div class="text-center mb-4">
      <h2 class="fw-bold mb-1" style="font-family: 'Space Grotesk', sans-serif;">DriveMaster Portal</h2>
      <p class="text-muted small mb-0">Sign in to manage student inquiries and course bookings</p>
    </div>

    <?php if ($error): ?>
      <div class="alert alert-danger py-2 px-3 small d-flex align-items-center mb-3">
        <i class="bi bi-exclamation-circle-fill me-2"></i> <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form method="POST">
      <div class="mb-3">
        <label class="form-label small fw-bold text-dark">Email / Username</label>
        <div class="input-group">
          <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-person"></i></span>
          <input type="text" id="usernameInput" name="username" class="form-control border-start-0" placeholder="admin@drivemaster.com" required>
        </div>
      </div>

      <div class="mb-4">
        <label class="form-label small fw-bold text-dark">Password</label>
        <div class="input-group">
          <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-lock"></i></span>
          <input type="password" id="passwordInput" name="password" class="form-control border-start-0" placeholder="••••••••" required>
        </div>
      </div>

      <button type="submit" class="btn-login mb-3">
        <i class="bi bi-box-arrow-in-right me-1"></i> Sign In to Dashboard
      </button>

      <!-- Quick Demo Credentials Box -->
      <div class="demo-credential-chip mb-3" onclick="fillDemoCredentials()">
        <div class="d-flex justify-content-between align-items-center mb-1">
          <strong class="text-dark"><i class="bi bi-magic me-1"></i> Demo Credentials:</strong>
          <span class="badge bg-success-subtle text-success small">Click to autofill</span>
        </div>
        <div>User: <code>admin@drivemaster.com</code></div>
        <div>Pass: <code>Admin@123456</code></div>
      </div>

      <div class="text-center">
        <a href="../home" class="small text-muted text-decoration-none">
          <i class="bi bi-arrow-left me-1"></i> Back to Live Website
        </a>
      </div>
    </form>
  </div>

  <script>
    function fillDemoCredentials() {
      document.getElementById('usernameInput').value = 'admin@drivemaster.com';
      document.getElementById('passwordInput').value = 'Admin@123456';
    }
  </script>
</body>
</html>
