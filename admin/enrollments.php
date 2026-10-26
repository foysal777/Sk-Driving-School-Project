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
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);
    $ref = trim((string)($_POST['ref'] ?? ''));

    if ($action === 'approve_square') {
        $target = !empty($ref) ? $ref : $id;
        $updated = Enrollment::approvePayment($target);
        if ($updated) {
            $alert = [
                'type' => 'success',
                'msg' => "Square payment request for " . htmlspecialchars($updated['student_name']) . " (" . htmlspecialchars($updated['booking_reference']) . ") APPROVED! The student is now permitted to enter their Square card details and complete payment."
            ];
        }
    } elseif ($action === 'reject_square') {
        $target = !empty($ref) ? $ref : $id;
        $updated = Enrollment::rejectPayment($target);
        if ($updated) {
            $alert = [
                'type' => 'warning',
                'msg' => "Payment request for " . htmlspecialchars($updated['booking_reference']) . " was rejected."
            ];
        }
    } else {
        $status = $_POST['status'] ?? 'pending';
        $payment = $_POST['payment_status'] ?? 'unpaid';
        if ($id > 0) {
            Enrollment::updateStatus($id, $status, $payment);
            $alert = ['type' => 'success', 'msg' => "Booking #{$id} updated."];
        }
    }
}

$activeFilter = $_GET['filter'] ?? 'all';
$filters = [];
if ($activeFilter === 'square_pending') {
    $filters['payment_status'] = 'payment_requested';
} elseif ($activeFilter === 'approved_to_pay') {
    $filters['payment_status'] = 'approved_to_pay';
} elseif ($activeFilter === 'paid_full') {
    $filters['payment_status'] = 'paid_in_full';
}

$enrollments = Enrollment::all($filters);
$stats = Enrollment::countStats();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Course Bookings &amp; Square Payments | DriveMaster Admin</title>
  <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🚗</text></svg>">
  <link rel="stylesheet" href="../assets/vendor/fonts/fonts.css">
  <link rel="stylesheet" href="../assets/vendor/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/vendor/bootstrap-icons/bootstrap-icons.min.css">
  <style>
    :root { --primary: #2A6B5C; --sidebar-width: 260px; --accent-green: #00b060; --square-black: #111111; }
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
    .square-badge {
      display: inline-flex; align-items: center; gap: 4px; background: #000000; color: #ffffff;
      padding: 3px 8px; border-radius: 6px; font-size: 0.75rem; font-weight: 700;
    }
    .pending-row {
      background-color: #fffbeb !important;
      border-left: 4px solid #f59e0b;
    }
    .approved-row {
      background-color: #f0fdf4 !important;
      border-left: 4px solid #10b981;
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
      <li>
        <a href="/admin/enrollments.php" class="sidebar-nav-link active">
          <i class="bi bi-calendar-check-fill"></i> Course Bookings
          <?php if (($stats['square_pending'] ?? 0) > 0): ?>
            <span class="badge bg-warning text-dark ms-auto"><?= $stats['square_pending'] ?></span>
          <?php endif; ?>
        </a>
      </li>
      <li><a href="/admin/courses.php" class="sidebar-nav-link"><i class="bi bi-car-front-fill"></i> Courses &amp; Rates</a></li>
      <li class="mt-3 pt-3 border-top border-white-10">
        <a href="/backend/swagger/" target="_blank" class="sidebar-nav-link"><i class="bi bi-code-square text-info"></i> Swagger API Docs</a>
      </li>
      <li><a href="/courses.html" target="_blank" class="sidebar-nav-link"><i class="bi bi-box-arrow-up-right text-success"></i> Course Catalog</a></li>
      <li><a href="/index.html" target="_blank" class="sidebar-nav-link"><i class="bi bi-box-arrow-up-right text-success"></i> View Live Site</a></li>
    </ul>
    <div class="p-3 border-top border-white-10">
      <a href="/admin/logout.php" class="btn btn-outline-light btn-sm w-100 fw-semibold"><i class="bi bi-box-arrow-right me-1"></i> Sign Out</a>
    </div>
  </aside>

  <!-- Main Content -->
  <div class="admin-main">
    <header class="admin-topbar">
      <div>
        <h4 class="mb-0 fw-bold" style="font-family: 'Space Grotesk', sans-serif;">Course Bookings &amp; Square Payment Approvals</h4>
        <div class="small text-muted">Review student booking requests and authorize Square payments</div>
      </div>
      <div class="d-flex gap-2">
        <a href="/courses.html#courses-catalog" target="_blank" class="btn btn-outline-success btn-sm fw-bold">
          <i class="bi bi-credit-card-2-front me-1"></i> Test Square Flow on Site
        </a>
      </div>
    </header>

    <div class="p-4 flex-grow-1">
      <?php if ($alert): ?>
        <div class="alert alert-<?= $alert['type'] ?> alert-dismissible fade show mb-4 shadow-sm" role="alert">
          <i class="bi bi-check-circle-fill me-2"></i> <?= $alert['msg'] ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>

      <!-- Quick KPI Counters -->
      <div class="row g-3 mb-4">
        <div class="col-md-3">
          <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <div class="text-muted small fw-bold">TOTAL BOOKINGS</div>
                <h3 class="fw-bold mb-0 text-dark"><?= $stats['total_bookings'] ?? 0 ?></h3>
              </div>
              <div class="p-3 bg-light rounded-3 text-primary"><i class="bi bi-calendar3 fs-4"></i></div>
            </div>
          </div>
        </div>

        <div class="col-md-3">
          <div class="card border-0 shadow-sm rounded-4 p-3 <?= ($stats['square_pending'] ?? 0) > 0 ? 'bg-warning-subtle border border-warning' : 'bg-white' ?>">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <div class="text-danger small fw-bold">SQUARE APPROVALS PENDING</div>
                <h3 class="fw-bold mb-0 text-danger"><?= $stats['square_pending'] ?? 0 ?></h3>
              </div>
              <div class="p-3 bg-warning text-dark rounded-3"><i class="bi bi-hourglass-split fs-4"></i></div>
            </div>
          </div>
        </div>

        <div class="col-md-3">
          <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <div class="text-muted small fw-bold">CONFIRMED / PAID</div>
                <h3 class="fw-bold mb-0 text-success"><?= $stats['confirmed'] ?? 0 ?></h3>
              </div>
              <div class="p-3 bg-success-subtle text-success rounded-3"><i class="bi bi-check2-circle fs-4"></i></div>
            </div>
          </div>
        </div>

        <div class="col-md-3">
          <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <div class="text-muted small fw-bold">PAID REVENUE</div>
                <h3 class="fw-bold mb-0 text-primary">$<?= number_format((float)($stats['revenue'] ?? 0), 2) ?></h3>
              </div>
              <div class="p-3 bg-primary-subtle text-primary rounded-3"><i class="bi bi-currency-dollar fs-4"></i></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Filter Navigation Tabs -->
      <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <ul class="nav nav-pills gap-2">
          <li class="nav-item">
            <a class="nav-link <?= $activeFilter === 'all' ? 'active bg-success' : 'bg-white text-dark border' ?> fw-semibold px-3 py-2" href="enrollments.php">
              All Bookings (<?= $stats['total_bookings'] ?? 0 ?>)
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= $activeFilter === 'square_pending' ? 'active bg-warning text-dark' : 'bg-white text-dark border' ?> fw-semibold px-3 py-2 position-relative" href="enrollments.php?filter=square_pending">
              ⏳ Awaiting Square Approval
              <?php if (($stats['square_pending'] ?? 0) > 0): ?>
                <span class="badge bg-danger ms-1"><?= $stats['square_pending'] ?></span>
              <?php endif; ?>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= $activeFilter === 'approved_to_pay' ? 'active bg-info text-dark' : 'bg-white text-dark border' ?> fw-semibold px-3 py-2" href="enrollments.php?filter=approved_to_pay">
              Approved (Waiting Student Payment)
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= $activeFilter === 'paid_full' ? 'active bg-dark' : 'bg-white text-dark border' ?> fw-semibold px-3 py-2" href="enrollments.php?filter=paid_full">
              Paid Full (Square)
            </a>
          </li>
        </ul>

        <div class="small text-muted">
          <i class="bi bi-info-circle me-1"></i> When user requests payment, click <strong>"Approve for Payment"</strong> to unlock their Square card terminal.
        </div>
      </div>

      <!-- Enrollments Table -->
      <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-uppercase">
              <tr>
                <th class="ps-4">Booking Ref</th>
                <th>Student Details</th>
                <th>Selected Course</th>
                <th>Amount</th>
                <th>Gateway &amp; Payment Status</th>
                <th>Overall Status</th>
                <th class="text-end pe-4" style="min-width: 220px;">Approval &amp; Action</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($enrollments)): ?>
                <tr>
                  <td colspan="7" class="text-center py-5 text-muted">
                    <i class="bi bi-calendar-x fs-1 d-block mb-2 text-secondary"></i>
                    No course bookings found for this filter.
                  </td>
                </tr>
              <?php else: ?>
                <?php foreach ($enrollments as $e): 
                  $payStatus = $e['payment_status'] ?? 'unpaid';
                  $isPendingSquare = ($payStatus === 'payment_requested');
                  $isApprovedSquare = ($payStatus === 'approved_to_pay');
                  $isPaidSquare = ($payStatus === 'paid_in_full');
                  $rowClass = $isPendingSquare ? 'pending-row' : ($isPaidSquare ? 'approved-row' : '');
                ?>
                  <tr class="<?= $rowClass ?>">
                    <td class="ps-4">
                      <span class="badge bg-dark font-monospace text-uppercase"><?= htmlspecialchars($e['booking_reference']) ?></span>
                      <div class="small text-muted mt-1" style="font-size: 0.72rem;">
                        <?= htmlspecialchars(date('M d, H:i', strtotime($e['created_at'] ?? 'now'))) ?>
                      </div>
                    </td>
                    <td>
                      <div class="fw-bold text-dark"><?= htmlspecialchars($e['student_name']) ?></div>
                      <div class="small"><a href="mailto:<?= htmlspecialchars($e['student_email']) ?>" class="text-decoration-none"><?= htmlspecialchars($e['student_email']) ?></a></div>
                      <div class="small text-muted"><i class="bi bi-telephone me-1"></i><?= htmlspecialchars($e['student_phone']) ?></div>
                    </td>
                    <td>
                      <div class="fw-bold text-dark"><?= htmlspecialchars($e['course_name']) ?></div>
                      <div class="small text-muted">
                        <i class="bi bi-gear-fill me-1"></i> <?= ucfirst(htmlspecialchars($e['preferred_transmission'] ?? 'automatic')) ?>
                      </div>
                    </td>
                    <td>
                      <div class="fw-bold text-success fs-6">$<?= number_format((float)$e['course_price'], 2) ?></div>
                    </td>
                    <td>
                      <!-- Gateway badge -->
                      <div class="mb-1">
                        <span class="square-badge">
                          <i class="bi bi-square-fill" style="font-size: 0.65rem;"></i> Square
                        </span>
                      </div>

                      <!-- Payment Status Tag -->
                      <?php if ($isPendingSquare): ?>
                        <span class="badge bg-warning text-dark py-1 px-2 border border-warning-subtle">
                          <i class="bi bi-hourglass-split me-1"></i> Payment Requested
                        </span>
                      <?php elseif ($isApprovedSquare): ?>
                        <span class="badge bg-info text-dark py-1 px-2">
                          <i class="bi bi-unlock-fill me-1"></i> Approved - Waiting Student
                        </span>
                      <?php elseif ($isPaidSquare): ?>
                        <span class="badge bg-success text-white py-1 px-2">
                          <i class="bi bi-check-all me-1"></i> Paid in Full
                        </span>
                        <?php if (!empty($e['payment_tx_id'])): ?>
                          <div class="small text-muted font-monospace mt-1" style="font-size: 0.7rem;">
                            Tx: <?= htmlspecialchars(substr($e['payment_tx_id'], 0, 18)) ?>...
                          </div>
                        <?php endif; ?>
                      <?php else: ?>
                        <span class="badge bg-secondary-subtle text-dark">
                          <?= ucfirst(str_replace('_', ' ', $payStatus)) ?>
                        </span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <span class="badge bg-<?= ($e['status'] === 'confirmed' || $isPaidSquare) ? 'success' : ($isPendingSquare ? 'warning text-dark' : 'secondary') ?> px-2 py-1 rounded-pill">
                        <?= ucfirst(str_replace('_', ' ', $e['status'] ?? 'pending')) ?>
                      </span>
                    </td>
                    <td class="text-end pe-4">
                      <?php if ($isPendingSquare): ?>
                        <!-- Quick Approve or Reject Square Request -->
                        <div class="d-inline-flex gap-1">
                          <form method="POST" class="d-inline">
                            <input type="hidden" name="action" value="approve_square">
                            <input type="hidden" name="ref" value="<?= htmlspecialchars($e['booking_reference']) ?>">
                            <button type="submit" class="btn btn-sm btn-success fw-bold py-1 px-2 shadow-sm" title="Approve student to proceed with Square payment">
                              <i class="bi bi-check-circle-fill me-1"></i> Approve Payment
                            </button>
                          </form>
                          <form method="POST" class="d-inline">
                            <input type="hidden" name="action" value="reject_square">
                            <input type="hidden" name="ref" value="<?= htmlspecialchars($e['booking_reference']) ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2" title="Reject request" onclick="return confirm('Are you sure you want to reject this request?')">
                              <i class="bi bi-x-circle"></i>
                            </button>
                          </form>
                        </div>
                      <?php else: ?>
                        <!-- Standard Status Editor -->
                        <form method="POST" class="d-inline-flex gap-1 align-items-center justify-content-end">
                          <input type="hidden" name="id" value="<?= $e['id'] ?>">
                          <select name="status" class="form-select form-select-sm" style="width: 110px;">
                            <option value="pending" <?= $e['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="confirmed" <?= $e['status'] === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                            <option value="in_training" <?= $e['status'] === 'in_training' ? 'selected' : '' ?>>In Training</option>
                            <option value="completed" <?= $e['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                            <option value="cancelled" <?= $e['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                          </select>
                          <select name="payment_status" class="form-select form-select-sm" style="width: 110px;">
                            <option value="unpaid" <?= $payStatus === 'unpaid' ? 'selected' : '' ?>>Unpaid</option>
                            <option value="payment_requested" <?= $payStatus === 'payment_requested' ? 'selected' : '' ?>>Requested</option>
                            <option value="approved_to_pay" <?= $payStatus === 'approved_to_pay' ? 'selected' : '' ?>>Approved</option>
                            <option value="paid_in_full" <?= $payStatus === 'paid_in_full' ? 'selected' : '' ?>>Paid Full</option>
                          </select>
                          <button type="submit" class="btn btn-sm btn-outline-success" title="Save Changes">
                            <i class="bi bi-check-lg"></i>
                          </button>
                        </form>
                      <?php endif; ?>
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

  <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
