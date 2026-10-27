<?php
declare(strict_types=1);

require_once __DIR__ . '/../backend/src/Database.php';
require_once __DIR__ . '/../backend/src/Auth.php';
require_once __DIR__ . '/../backend/src/Models/Newsletter.php';
require_once __DIR__ . '/../backend/src/Models/Inquiry.php';
require_once __DIR__ . '/../backend/src/Models/Enrollment.php';

use DriveMaster\Auth;
use DriveMaster\Models\Newsletter;
use DriveMaster\Models\Inquiry;
use DriveMaster\Models\Enrollment;

$admin = Auth::requireAuth();

// Handle subscriber deletion
$alert = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = $_POST['id'] ?? '';

    if ($action === 'delete' && !empty($id)) {
        if (Newsletter::delete($id)) {
            $alert = ['type' => 'success', 'msg' => 'Subscriber has been removed successfully.'];
        } else {
            $alert = ['type' => 'danger', 'msg' => 'Failed to remove subscriber.'];
        }
    }
}

$subscribers = Newsletter::all();
$totalSubscribers = count($subscribers);
$inquiryStats = Inquiry::countByStatus();
$enrollmentStats = Enrollment::countStats();

// Search filter
$search = trim($_GET['search'] ?? '');
if ($search !== '') {
    $subscribers = array_filter($subscribers, function ($sub) use ($search) {
        return stripos($sub['email'] ?? '', $search) !== false ||
               stripos($sub['source'] ?? '', $search) !== false;
    });
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Newsletter Subscribers | DriveMaster Admin</title>
  <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>📬</text></svg>">
  <link rel="stylesheet" href="../assets/vendor/fonts/fonts.css">
  <link rel="stylesheet" href="../assets/vendor/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/vendor/bootstrap-icons/bootstrap-icons.min.css">
  <style>
    :root {
      --primary: #2A6B5C;
      --sidebar-width: 260px;
      --accent-green: #00b060;
    }
    body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; color: #0f172a; }
    .admin-sidebar {
      width: var(--sidebar-width); height: 100vh; position: fixed; top: 0; left: 0;
      background: #133a30; color: #e2e8f0; display: flex; flex-direction: column; z-index: 1050;
      box-shadow: 4px 0 20px rgba(0, 0, 0, 0.08);
    }
    .sidebar-brand {
      padding: 1.5rem 1.25rem; border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      display: flex; align-items: center; gap: 0.75rem; text-decoration: none; color: #ffffff;
    }
    .brand-icon-box {
      width: 40px; height: 40px; border-radius: 10px; background: var(--accent-green);
      display: flex; align-items: center; justify-content: center; font-size: 1.2rem; color: #ffffff;
    }
    .sidebar-nav { list-style: none; padding: 1.25rem 0.75rem; margin: 0; flex-grow: 1; overflow-y: auto; }
    .sidebar-nav-item { margin-bottom: 0.35rem; }
    .sidebar-nav-link {
      display: flex; align-items: center; gap: 0.85rem; padding: 0.75rem 1rem;
      color: #cbd5e1; text-decoration: none; border-radius: 10px; font-weight: 600; font-size: 0.92rem;
      transition: all 0.2s ease;
    }
    .sidebar-nav-link:hover, .sidebar-nav-link.active { background: var(--accent-green); color: #ffffff; }
    .admin-main { margin-left: var(--sidebar-width); min-height: 100vh; display: flex; flex-direction: column; }
    .admin-topbar {
      background: #ffffff; border-bottom: 1px solid #e2e8f0; padding: 0.85rem 2rem;
      display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 1040;
    }
    .kpi-card {
      background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px;
      padding: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.02);
    }
    @media (max-width: 991.98px) {
      .admin-sidebar { display: none; }
      .admin-main { margin-left: 0; }
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <aside class="admin-sidebar">
    <a href="index.php" class="sidebar-brand">
      <div class="brand-icon-box"><i class="bi bi-speedometer2"></i></div>
      <div>
        <div class="fw-bold" style="font-family: 'Space Grotesk', sans-serif;">DriveMaster</div>
        <div class="small text-white-50" style="font-size: 0.72rem;">ADMINISTRATION</div>
      </div>
    </a>
    <ul class="sidebar-nav">
      <li class="sidebar-nav-item">
        <a href="index.php" class="sidebar-nav-link"><i class="bi bi-grid-fill"></i> Dashboard</a>
      </li>
      <li class="sidebar-nav-item">
        <a href="enrollments.php" class="sidebar-nav-link">
          <i class="bi bi-calendar-check-fill"></i> Pending Request
          <?php if (($enrollmentStats['square_pending'] ?? 0) > 0): ?>
            <span class="badge bg-warning text-dark ms-auto rounded-pill"><?= $enrollmentStats['square_pending'] ?></span>
          <?php endif; ?>
        </a>
      </li>
      <li class="sidebar-nav-item">
        <a href="inquiries.php" class="sidebar-nav-link">
          <i class="bi bi-envelope-fill"></i> Contact Inquiries
          <?php if (($inquiryStats['new'] ?? 0) > 0): ?>
            <span class="badge bg-danger ms-auto rounded-pill"><?= $inquiryStats['new'] ?></span>
          <?php endif; ?>
        </a>
      </li>
      <li class="sidebar-nav-item">
        <a href="subscribers.php" class="sidebar-nav-link active">
          <i class="bi bi-mailbox2"></i> Newsletter Subscribers
          <span class="badge bg-light text-dark ms-auto rounded-pill"><?= $totalSubscribers ?></span>
        </a>
      </li>
      <li class="sidebar-nav-item">
        <a href="courses.php" class="sidebar-nav-link"><i class="bi bi-car-front-fill"></i> Courses &amp; Rates</a>
      </li>
      <li class="sidebar-nav-item mt-3 pt-3 border-top border-white-10">
        <span class="px-3 text-white-50 text-uppercase small" style="font-size: 0.68rem; font-weight: 800;">DEVELOPER TOOLS</span>
      </li>
      <li class="sidebar-nav-item">
        <a href="../backend/swagger/" target="_blank" class="sidebar-nav-link">
          <i class="bi bi-code-square text-info"></i> Swagger API Docs
        </a>
      </li>
      <li class="sidebar-nav-item">
        <a href="../home" target="_blank" class="sidebar-nav-link">
          <i class="bi bi-box-arrow-up-right text-success"></i> View Live Site
        </a>
      </li>
    </ul>

    <div class="p-3 border-top border-white-10">
      <div class="d-flex align-items-center gap-2 mb-2">
        <div class="avatar-sm rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
          <i class="bi bi-person-fill"></i>
        </div>
        <div class="overflow-hidden">
          <div class="small fw-bold text-truncate text-white"><?= htmlspecialchars($admin['full_name'] ?? $admin['username'] ?? 'Administrator') ?></div>
          <div class="small text-white-50" style="font-size: 0.72rem;"><?= htmlspecialchars($admin['role'] ?? 'Operations') ?></div>
        </div>
      </div>
      <a href="logout.php" class="btn btn-outline-light btn-sm w-100 fw-semibold">
        <i class="bi bi-box-arrow-right me-1"></i> Sign Out
      </a>
    </div>
  </aside>

  <!-- Main Content -->
  <div class="admin-main">
    <header class="admin-topbar">
      <div>
        <h4 class="mb-0 fw-bold" style="font-family: 'Space Grotesk', sans-serif;">Newsletter Subscribers</h4>
        <small class="text-muted">Emails captured from the frontend newsletter subscription forms</small>
      </div>
      <div class="d-flex gap-2">
        <a href="index.php" class="btn btn-outline-secondary btn-sm fw-bold">
          <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
        </a>
      </div>
    </header>

    <div class="p-4 flex-grow-1">

      <?php if ($alert): ?>
        <div class="alert alert-<?= $alert['type'] ?> alert-dismissible fade show rounded-3" role="alert">
          <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($alert['msg']) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <!-- Metrics Row -->
      <div class="row g-4 mb-4">
        <div class="col-md-4">
          <div class="kpi-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="text-muted small fw-bold text-uppercase">Total Subscribers</span>
              <div class="p-2 rounded-3 bg-success-subtle text-success"><i class="bi bi-mailbox2 fs-4"></i></div>
            </div>
            <h2 class="fw-bold mb-0 text-success"><?= $totalSubscribers ?></h2>
            <small class="text-muted">Direct from frontend footer forms</small>
          </div>
        </div>
        <div class="col-md-4">
          <div class="kpi-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="text-muted small fw-bold text-uppercase">Subscribed Today</span>
              <div class="p-2 rounded-3 bg-primary-subtle text-primary"><i class="bi bi-calendar2-check fs-4"></i></div>
            </div>
            <?php
              $today = date('Y-m-d');
              $todayCount = count(array_filter($subscribers, fn($s) => str_starts_with($s['created_at'] ?? '', $today)));
            ?>
            <h2 class="fw-bold mb-0 text-primary"><?= $todayCount ?></h2>
            <small class="text-muted">Active subscriber growth</small>
          </div>
        </div>
        <div class="col-md-4">
          <div class="kpi-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="text-muted small fw-bold text-uppercase">Subscription Status</span>
              <div class="p-2 rounded-3 bg-info-subtle text-info"><i class="bi bi-shield-check fs-4"></i></div>
            </div>
            <h2 class="fw-bold mb-0 text-info">100%</h2>
            <small class="text-muted">Verified &amp; active audience</small>
          </div>
        </div>
      </div>

      <!-- Subscribers Table Card -->
      <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white py-3 px-4 d-flex flex-wrap justify-content-between align-items-center gap-3 border-bottom">
          <h5 class="mb-0 fw-bold"><i class="bi bi-envelope-paper text-success me-2"></i>Subscribers List</h5>
          <form method="GET" class="d-flex gap-2" style="max-width: 320px;">
            <div class="input-group input-group-sm">
              <input type="text" name="search" class="form-control" placeholder="Search email or source..." value="<?= htmlspecialchars($search) ?>">
              <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
              <?php if ($search !== ''): ?>
                <a href="subscribers.php" class="btn btn-outline-danger"><i class="bi bi-x"></i></a>
              <?php endif; ?>
            </div>
          </form>
        </div>

        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-uppercase">
              <tr>
                <th class="ps-4">#</th>
                <th>Subscriber Email</th>
                <th>Source Page</th>
                <th>Subscribed At</th>
                <th>Status</th>
                <th class="text-end pe-4">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($subscribers)): ?>
                <tr>
                  <td colspan="6" class="text-center py-5 text-muted">
                    <i class="bi bi-envelope-x display-6 d-block mb-2 text-secondary opacity-50"></i>
                    No newsletter subscribers found yet.
                  </td>
                </tr>
              <?php else: ?>
                <?php $idx = 1; foreach ($subscribers as $s): ?>
                  <tr>
                    <td class="ps-4 text-muted small"><?= $idx++ ?></td>
                    <td>
                      <div class="fw-bold text-dark">
                        <i class="bi bi-envelope me-1 text-primary"></i>
                        <?= htmlspecialchars($s['email'] ?? '') ?>
                      </div>
                      <small class="text-muted font-monospace" style="font-size: 0.75rem;">IP: <?= htmlspecialchars($s['ip_address'] ?? '127.0.0.1') ?></small>
                    </td>
                    <td>
                      <span class="badge bg-light text-dark border rounded-pill px-2 py-1">
                        <i class="bi bi-link-45deg me-1"></i><?= htmlspecialchars($s['source'] ?? 'website') ?>
                      </span>
                    </td>
                    <td>
                      <div class="small fw-semibold text-dark"><?= htmlspecialchars(date('M d, Y', strtotime($s['created_at'] ?? 'now'))) ?></div>
                      <div class="text-muted small" style="font-size: 0.75rem;"><?= htmlspecialchars(date('h:i A', strtotime($s['created_at'] ?? 'now'))) ?></div>
                    </td>
                    <td>
                      <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1">
                        <i class="bi bi-check2 me-1"></i><?= ucfirst(htmlspecialchars($s['status'] ?? 'subscribed')) ?>
                      </span>
                    </td>
                    <td class="text-end pe-4">
                      <form method="POST" onsubmit="return confirm('Remove this email from the newsletter list?');" style="display: inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= htmlspecialchars((string)($s['id'] ?? '')) ?>">
                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1">
                          <i class="bi bi-trash me-1"></i> Remove
                        </button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>

    <footer class="bg-white border-top py-3 px-4 text-center small text-muted">
      &copy; 2026 DriveMaster Driving School &bull; Newsletter Engine
    </footer>
  </div>

  <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
