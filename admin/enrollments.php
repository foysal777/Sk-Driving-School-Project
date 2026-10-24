<?php
declare(strict_types=1);

require_once __DIR__ . '/../backend/src/Database.php';
require_once __DIR__ . '/../backend/src/Auth.php';
require_once __DIR__ . '/../backend/src/Models/Enrollment.php';

use DriveMaster\Auth;
use DriveMaster\Models\Enrollment;

$admin = Auth::requireAuth();

$alert = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $status = $_POST['status'] ?? 'pending';
    $payment = $_POST['payment_status'] ?? 'unpaid';

    if ($id > 0) {
        Enrollment::updateStatus($id, $status, $payment);
        $alert = ['type' => 'success', 'msg' => "Booking #{$id} updated."];
    }
}

$enrollments = Enrollment::all(['status' => $_GET['status'] ?? 'all']);
$stats = Enrollment::countStats();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Course Bookings | DriveMaster Admin</title>
  <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🚗</text></svg>">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Space+Grotesk:wght@600;700&display=swap" rel="stylesheet">
  <style>
    :root { --primary: #2A6B5C; --sidebar-width: 260px; --accent-green: #00b060; }
    body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; color: #0f172a; }
    .admin-sidebar {
      width: var(--sidebar-width); height: 100vh; position: fixed; top: 0; left: 0;
      background: #133a30; color: #e2e8f0; display: flex; flex-direction: column; z-index: 1050;
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
  </style>
</head>
<body>

  <!-- Sidebar -->
  <aside class="admin-sidebar">
    <a href="/admin/index.php" class="sidebar-brand">
      <div class="brand-icon-box"><i class="bi bi-speedometer2"></i></div>
      <div>
        <div class="fw-bold" style="font-family: 'Space Grotesk', sans-serif;">DriveMaster</div>
        <div class="small text-white-50" style="font-size: 0.72rem;">ADMINISTRATION</div>
      </div>
    </a>
    <ul class="sidebar-nav">
      <li><a href="/admin/index.php" class="sidebar-nav-link"><i class="bi bi-grid-fill"></i> Dashboard</a></li>
      <li><a href="/admin/inquiries.php" class="sidebar-nav-link"><i class="bi bi-envelope-fill"></i> Inquiries &amp; Leads</a></li>
      <li><a href="/admin/enrollments.php" class="sidebar-nav-link active"><i class="bi bi-calendar-check-fill"></i> Course Bookings</a></li>
      <li><a href="/admin/courses.php" class="sidebar-nav-link"><i class="bi bi-car-front-fill"></i> Courses &amp; Rates</a></li>
      <li class="mt-3 pt-3 border-top border-white-10">
        <a href="/backend/swagger/" target="_blank" class="sidebar-nav-link"><i class="bi bi-code-square text-info"></i> Swagger API Docs</a>
      </li>
      <li><a href="/index.html" target="_blank" class="sidebar-nav-link"><i class="bi bi-box-arrow-up-right text-success"></i> View Live Site</a></li>
    </ul>
    <div class="p-3 border-top border-white-10">
      <a href="/admin/logout.php" class="btn btn-outline-light btn-sm w-100 fw-semibold"><i class="bi bi-box-arrow-right me-1"></i> Sign Out</a>
    </div>
  </aside>

  <!-- Main Content -->
  <div class="admin-main">
    <header class="admin-topbar">
      <h4 class="mb-0 fw-bold" style="font-family: 'Space Grotesk', sans-serif;">Student Course Bookings &amp; Enrollments</h4>
      <div>
        <a href="/courses.html" target="_blank" class="btn btn-outline-success btn-sm fw-bold">
          <i class="bi bi-car-front me-1"></i> View Course Catalog
        </a>
      </div>
    </header>

    <div class="p-4 flex-grow-1">
      <?php if ($alert): ?>
        <div class="alert alert-<?= $alert['type'] ?> alert-dismissible fade show mb-4" role="alert">
          <i class="bi bi-check-circle-fill me-2"></i> <?= htmlspecialchars($alert['msg']) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>

      <!-- Enrollments Table -->
      <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-uppercase">
              <tr>
                <th class="ps-4">Booking Ref</th>
                <th>Student Details</th>
                <th>Selected Course</th>
                <th>Transmission</th>
                <th>Price &amp; Payment</th>
                <th>Status</th>
                <th class="text-end pe-4">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($enrollments)): ?>
                <tr>
                  <td colspan="7" class="text-center py-5 text-muted">
                    <i class="bi bi-calendar-x fs-1 d-block mb-2 text-secondary"></i>
                    No course bookings found.
                  </td>
                </tr>
              <?php else: ?>
                <?php foreach ($enrollments as $e): ?>
                  <tr>
                    <td class="ps-4">
                      <span class="badge bg-dark font-monospace text-uppercase"><?= htmlspecialchars($e['booking_reference']) ?></span>
                    </td>
                    <td>
                      <div class="fw-bold text-dark"><?= htmlspecialchars($e['student_name']) ?></div>
                      <div class="small"><a href="mailto:<?= htmlspecialchars($e['student_email']) ?>"><?= htmlspecialchars($e['student_email']) ?></a></div>
                      <div class="small text-muted"><?= htmlspecialchars($e['student_phone']) ?></div>
                    </td>
                    <td>
                      <div class="fw-bold"><?= htmlspecialchars($e['course_name']) ?></div>
                      <div class="small text-muted"><i class="bi bi-calendar-event me-1"></i> Start: <?= htmlspecialchars($e['preferred_start_date'] ?? 'Flexible') ?></div>
                    </td>
                    <td>
                      <span class="badge bg-light text-dark border text-capitalize">
                        <i class="bi bi-gear-fill text-success me-1"></i> <?= htmlspecialchars($e['preferred_transmission'] ?? 'automatic') ?>
                      </span>
                    </td>
                    <td>
                      <div class="fw-bold text-success">$<?= number_format((float)$e['course_price'], 2) ?></div>
                      <span class="badge bg-<?= $e['payment_status'] === 'paid_in_full' ? 'success' : ($e['payment_status'] === 'deposit_paid' ? 'warning' : 'secondary') ?>-subtle text-dark small">
                        <?= ucfirst(str_replace('_', ' ', $e['payment_status'] ?? 'unpaid')) ?>
                      </span>
                    </td>
                    <td>
                      <span class="badge bg-<?= $e['status'] === 'confirmed' ? 'primary' : ($e['status'] === 'in_training' ? 'success' : 'secondary') ?> px-2 py-1 rounded-pill">
                        <?= ucfirst(str_replace('_', ' ', $e['status'])) ?>
                      </span>
                    </td>
                    <td class="text-end pe-4">
                      <form method="POST" class="d-inline-flex gap-1 align-items-center">
                        <input type="hidden" name="id" value="<?= $e['id'] ?>">
                        <select name="status" class="form-select form-select-sm" style="width: 120px;">
                          <option value="pending" <?= $e['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                          <option value="confirmed" <?= $e['status'] === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                          <option value="in_training" <?= $e['status'] === 'in_training' ? 'selected' : '' ?>>In Training</option>
                          <option value="completed" <?= $e['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                          <option value="cancelled" <?= $e['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                        </select>
                        <select name="payment_status" class="form-select form-select-sm" style="width: 120px;">
                          <option value="unpaid" <?= ($e['payment_status'] ?? '') === 'unpaid' ? 'selected' : '' ?>>Unpaid</option>
                          <option value="deposit_paid" <?= ($e['payment_status'] ?? '') === 'deposit_paid' ? 'selected' : '' ?>>Deposit</option>
                          <option value="paid_in_full" <?= ($e['payment_status'] ?? '') === 'paid_in_full' ? 'selected' : '' ?>>Paid Full</option>
                        </select>
                        <button type="submit" class="btn btn-sm btn-outline-success" title="Save Changes">
                          <i class="bi bi-check-lg"></i>
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
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
