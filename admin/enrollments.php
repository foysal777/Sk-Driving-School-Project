<?php
declare(strict_types=1);

require_once __DIR__ . '/../backend/src/Database.php';
require_once __DIR__ . '/../backend/src/Auth.php';
require_once __DIR__ . '/../backend/src/Models/Enrollment.php';
require_once __DIR__ . '/../backend/src/Models/Inquiry.php';
require_once __DIR__ . '/../backend/src/Models/Newsletter.php';
require_once __DIR__ . '/../backend/src/Services/Mailer.php';

use DriveMaster\Auth;
use DriveMaster\Models\Enrollment;
use DriveMaster\Models\Inquiry;
use DriveMaster\Models\Newsletter;
use DriveMaster\Services\Mailer;

$admin = Auth::requireAuth();
$inquiryStats = Inquiry::countByStatus();
$subscriberCount = Newsletter::count();

$alert = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);
    $ref = trim((string)($_POST['ref'] ?? ''));

    if ($action === 'approve_square') {
        $target = !empty($ref) ? $ref : $id;
        $updated = Enrollment::approvePayment($target);
        if ($updated) {
            // Trigger SMTP Email Notification to Student
            $mailResult = Mailer::getInstance()->notifyUserPaymentApproved($updated);
            $emailNote = !empty($mailResult['delivered']) ? ' (Approval email sent to student via SMTP)' : ' (Approval email logged)';

            $alert = [
                'type' => 'success',
                'msg' => "Square payment request for " . htmlspecialchars($updated['student_name']) . " (" . htmlspecialchars($updated['booking_reference']) . ") APPROVED!{$emailNote} The student can now pay."
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
    .rejected-row {
      background-color: #fef2f2 !important;
      border-left: 4px solid #ef4444;
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
      <li><a href="index.php" class="sidebar-nav-link"><i class="bi bi-grid-fill"></i> Dashboard</a></li>
      <li>
        <a href="enrollments.php" class="sidebar-nav-link active">
          <i class="bi bi-calendar-check-fill"></i> Pending Request
          <?php if (($stats['square_pending'] ?? 0) > 0): ?>
            <span class="badge bg-warning text-dark ms-auto rounded-pill px-2"><?= $stats['square_pending'] ?></span>
          <?php endif; ?>
        </a>
      </li>
      <li>
        <a href="inquiries.php" class="sidebar-nav-link">
          <i class="bi bi-envelope-fill"></i> Contact Inquiries
          <?php if (($inquiryStats['new'] ?? 0) > 0): ?>
            <span class="badge bg-danger ms-auto rounded-pill px-2"><?= $inquiryStats['new'] ?></span>
          <?php endif; ?>
        </a>
      </li>
      <li>
        <a href="subscribers.php" class="sidebar-nav-link">
          <i class="bi bi-mailbox2"></i> Newsletter Subscribers
          <span class="badge bg-light text-dark ms-auto rounded-pill px-2"><?= $subscriberCount ?></span>
        </a>
      </li>
      <li><a href="courses.php" class="sidebar-nav-link"><i class="bi bi-car-front-fill"></i> Courses &amp; Rates</a></li>
      <li class="mt-3 pt-3 border-top border-white-10">
        <a href="../backend/swagger/" target="_blank" class="sidebar-nav-link"><i class="bi bi-code-square text-info"></i> Swagger API Docs</a>
      </li>
      <li><a href="../courses" target="_blank" class="sidebar-nav-link"><i class="bi bi-box-arrow-up-right text-success"></i> Course Catalog</a></li>
      <li><a href="../home" target="_blank" class="sidebar-nav-link"><i class="bi bi-box-arrow-up-right text-success"></i> View Live Site</a></li>
    </ul>
    <div class="p-3 border-top border-white-10">
      <a href="logout.php" class="btn btn-outline-light btn-sm w-100 fw-semibold"><i class="bi bi-box-arrow-right me-1"></i> Sign Out</a>
    </div>
  </aside>

  <!-- Main Content -->
  <div class="admin-main">
    <header class="admin-topbar justify-content-end">
      <div class="d-flex gap-2">
        <a href="../courses#courses-catalog" target="_blank" class="btn btn-outline-success btn-sm fw-bold">
          <i class="bi bi-credit-card-2-front me-1"></i>
        </a>
        <button type="button" id="btnHeaderRefresh" class="btn btn-dark btn-sm fw-bold" onclick="location.reload();">
          <i class="bi bi-arrow-clockwise me-1"></i> Refresh
        </button>
      </div>
    </header>

    <div class="p-4 flex-grow-1">
      <?php if ($alert): ?>
        <div class="alert alert-<?= $alert['type'] ?> alert-dismissible fade show mb-4 shadow-sm rounded-4" role="alert">
          <i class="bi bi-check-circle-fill me-2 fs-5"></i> <?= $alert['msg'] ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>



      <!-- Quick Search & Filter Toolbar -->
      <div class="card border-0 shadow-sm rounded-4 mb-4 p-3 bg-white">
        <div class="row g-2 align-items-center">
          <div class="col-lg-5 col-md-6">
            <div class="input-group">
              <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
              <input type="text" id="liveSearchInput" class="form-control border-start-0 ps-0" placeholder="Live Search: Type student name, phone, email, booking ref...">
            </div>
          </div>
          <div class="col-lg-4 col-md-6">
            <div class="d-flex gap-2">
              <a href="enrollments.php" class="btn btn-sm <?= $activeFilter === 'all' ? 'btn-success fw-bold' : 'btn-outline-secondary' ?>">
                All (<?= $stats['total_bookings'] ?? 0 ?>)
              </a>
              <a href="enrollments.php?filter=square_pending" class="btn btn-sm <?= $activeFilter === 'square_pending' ? 'btn-warning text-dark fw-bold' : 'btn-outline-secondary' ?>">
                ⏳ Pending Approval (<?= $stats['square_pending'] ?? 0 ?>)
              </a>
              <a href="enrollments.php?filter=paid_full" class="btn btn-sm <?= $activeFilter === 'paid_full' ? 'btn-dark fw-bold' : 'btn-outline-secondary' ?>">
                Paid Full
              </a>
            </div>
          </div>
          <div class="col-lg-3 col-md-12 text-lg-end d-flex gap-2 justify-content-lg-end">
            <button type="button" id="btnToggleAutoRefresh" class="btn btn-sm btn-outline-success fw-bold d-flex align-items-center gap-1">
              <i class="bi bi-arrow-repeat"></i> <span id="autoRefreshLabel">Auto-Refresh: ON (8s)</span>
            </button>
            <a href="enrollments.php" class="btn btn-sm btn-light border fw-semibold" title="Reset Filters &amp; Reload">
              <i class="bi bi-arrow-clockwise"></i>
            </a>
          </div>
        </div>
      </div>
      <!-- Enrollments Table -->
      <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
        <div class="card-header bg-white py-3 px-4 border-0 d-flex flex-wrap align-items-center justify-content-between gap-2 border-bottom">
          <div class="d-flex align-items-center gap-2">
            <span class="p-2 bg-success text-white rounded-3 shadow-sm">
              <i class="bi bi-person-check-fill fs-5"></i>
            </span>
            <div>
              <h5 class="fw-bold mb-0 text-dark">Pending Request List</h5>
              <small class="text-muted">Live student booking requests, transmissions &amp; payment state</small>
            </div>
          </div>
          <div class="d-flex align-items-center gap-2">
            <span class="badge bg-light text-dark border">
              Showing: <span id="visibleRowCount"><?= count($enrollments) ?></span> of <?= count($enrollments) ?>
            </span>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0" id="enrollmentsTable">
            <thead class="table-light small text-uppercase">
              <tr>
                <th class="ps-4">Booking Ref</th>
                <th>Student Details</th>
                <th>Selected Course</th>
                <th>Amount</th>
                <th>Payment Status</th>
                <th>Overall Status</th>
                <th class="text-end pe-4" style="min-width: 250px;">Approval &amp; Action</th>
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
                  $isRejected = ($payStatus === 'rejected' || ($e['status'] ?? '') === 'cancelled');
                  $rowClass = $isPendingSquare ? 'pending-row' : ($isPaidSquare ? 'approved-row' : ($isRejected ? 'rejected-row' : ''));
                ?>
                  <tr class="<?= $rowClass ?>" data-search="<?= strtolower(htmlspecialchars(($e['student_name'] ?? '') . ' ' . ($e['student_email'] ?? '') . ' ' . ($e['student_phone'] ?? '') . ' ' . ($e['booking_reference'] ?? '') . ' ' . ($e['course_name'] ?? ''))) ?>">
                    <td class="ps-4">
                      <span class="badge bg-dark font-monospace text-uppercase"><?= htmlspecialchars($e['booking_reference']) ?></span>
                      <div class="small text-muted mt-1" style="font-size: 0.72rem;">
                        <i class="bi bi-clock me-1"></i><?= htmlspecialchars(date('M d, H:i', strtotime($e['created_at'] ?? 'now'))) ?>
                      </div>
                    </td>
                    <td>
                      <div class="fw-bold text-dark fs-6"><?= htmlspecialchars($e['student_name']) ?></div>
                      <div class="small"><a href="mailto:<?= htmlspecialchars($e['student_email']) ?>" class="text-decoration-none text-muted"><i class="bi bi-envelope me-1"></i><?= htmlspecialchars($e['student_email']) ?></a></div>
                      <div class="small text-muted"><i class="bi bi-telephone me-1"></i><?= htmlspecialchars($e['student_phone']) ?></div>
                    </td>
                    <td>
                      <div class="fw-bold text-dark"><?= htmlspecialchars($e['course_name']) ?></div>
                      <div class="small text-muted">
                        <i class="bi bi-gear-fill me-1"></i> <?= ucfirst(htmlspecialchars($e['preferred_transmission'] ?? 'automatic')) ?>
                      </div>
                    </td>
                    <td>
                      <div class="fw-bold text-success fs-5">$<?= number_format((float)$e['course_price'], 2) ?></div>
                    </td>
                    <td>
                      <!-- Payment Status Tag -->
                      <?php if ($isPendingSquare): ?>
                        <span class="badge bg-warning-subtle text-dark py-1 px-2 border border-warning-subtle shadow-sm rounded-pill fw-semibold">
                          <i class="bi bi-hourglass-split text-warning me-1"></i> Payment Requested
                        </span>
                      <?php elseif ($isApprovedSquare): ?>
                        <span class="badge bg-info-subtle text-primary-emphasis py-1 px-2 border border-info-subtle rounded-pill fw-semibold">
                          <i class="bi bi-unlock-fill text-info me-1"></i> Approved - Waiting Student
                        </span>
                      <?php elseif ($isPaidSquare): ?>
                        <span class="badge bg-success-subtle text-success py-1 px-2 border border-success-subtle shadow-sm rounded-pill fw-semibold">
                          <i class="bi bi-check-circle-fill text-success me-1"></i> Paid in Full
                        </span>
                        <?php if (!empty($e['payment_tx_id'])): ?>
                          <div class="small text-muted font-monospace mt-1" style="font-size: 0.7rem;">
                            Tx: <?= htmlspecialchars(substr($e['payment_tx_id'], 0, 18)) ?>...
                          </div>
                        <?php endif; ?>
                      <?php elseif ($payStatus === 'rejected'): ?>
                        <span class="badge bg-danger-subtle text-danger py-1 px-2 border border-danger-subtle rounded-pill fw-semibold">
                          <i class="bi bi-x-circle-fill text-danger me-1"></i> Rejected
                        </span>
                      <?php else: ?>
                        <span class="badge bg-secondary-subtle text-dark border py-1 px-2 rounded-pill fw-semibold">
                          <?= ucfirst(str_replace('_', ' ', $payStatus)) ?>
                        </span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <?php 
                        $statusBadgeClass = 'bg-secondary text-white';
                        $statusIcon = 'bi-circle';
                        $statusText = ucfirst(str_replace('_', ' ', $e['status'] ?? 'pending'));
                        if ($e['status'] === 'confirmed' || $isPaidSquare) {
                          $statusBadgeClass = 'bg-success text-white';
                          $statusIcon = 'bi-check2-circle';
                        } elseif ($isPendingSquare) {
                          $statusBadgeClass = 'bg-warning text-dark';
                          $statusIcon = 'bi-clock-history';
                        } elseif ($e['status'] === 'approved_to_pay' || $isApprovedSquare) {
                          $statusBadgeClass = 'bg-info-subtle text-primary border border-info-subtle';
                          $statusIcon = 'bi-unlock';
                          $statusText = 'Approved';
                        } elseif ($e['status'] === 'cancelled' || $payStatus === 'rejected') {
                          $statusBadgeClass = 'bg-danger-subtle text-danger border border-danger-subtle';
                          $statusIcon = 'bi-x-circle';
                          $statusText = 'Rejected';
                        }
                      ?>
                      <span class="badge <?= $statusBadgeClass ?> px-2 py-1 rounded-pill fw-semibold">
                        <i class="bi <?= $statusIcon ?> me-1"></i><?= $statusText ?>
                      </span>
                    </td>
                    <td class="text-end pe-4">
                      <div class="d-inline-flex gap-2 align-items-center justify-content-end">
                        <?php if ($isPendingSquare): ?>
                          <!-- Quick Approve Payment Request -->
                          <form method="POST" class="d-inline m-0">
                            <input type="hidden" name="action" value="approve_square">
                            <input type="hidden" name="ref" value="<?= htmlspecialchars($e['booking_reference']) ?>">
                            <button type="submit" class="btn btn-sm btn-success fw-bold py-1 px-3 shadow-sm rounded-pill d-inline-flex align-items-center" title="Approve booking and send payment link to student via SMTP">
                              <i class="bi bi-check-circle-fill me-1"></i> Approve
                            </button>
                          </form>
                          <!-- Dedicated Quick Reject Button -->
                          <form method="POST" class="d-inline m-0">
                            <input type="hidden" name="action" value="reject_square">
                            <input type="hidden" name="ref" value="<?= htmlspecialchars($e['booking_reference']) ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger fw-bold py-1 px-3 shadow-sm rounded-pill d-inline-flex align-items-center" title="Reject student booking request" onclick="return confirm('Are you sure you want to reject booking <?= htmlspecialchars($e['booking_reference']) ?> for <?= htmlspecialchars($e['student_name']) ?>?')">
                              <i class="bi bi-x-circle-fill me-1"></i> Reject
                            </button>
                          </form>
                        <?php else: ?>
                          <!-- Standard Status Editor -->
                          <form method="POST" class="d-inline-flex gap-1 align-items-center justify-content-end m-0">
                            <input type="hidden" name="id" value="<?= $e['id'] ?>">
                            <select name="status" class="form-select form-select-sm rounded-pill" style="width: 105px;">
                              <option value="pending" <?= $e['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                              <option value="confirmed" <?= $e['status'] === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                              <option value="in_training" <?= $e['status'] === 'in_training' ? 'selected' : '' ?>>In Training</option>
                              <option value="completed" <?= $e['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                              <option value="cancelled" <?= $e['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                            </select>
                            <select name="payment_status" class="form-select form-select-sm rounded-pill" style="width: 105px;">
                              <option value="unpaid" <?= $payStatus === 'unpaid' ? 'selected' : '' ?>>Unpaid</option>
                              <option value="payment_requested" <?= $payStatus === 'payment_requested' ? 'selected' : '' ?>>Requested</option>
                              <option value="approved_to_pay" <?= $payStatus === 'approved_to_pay' ? 'selected' : '' ?>>Approved</option>
                              <option value="paid_in_full" <?= $payStatus === 'paid_in_full' ? 'selected' : '' ?>>Paid Full</option>
                              <option value="rejected" <?= $payStatus === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                            </select>
                            <button type="submit" class="btn btn-sm btn-outline-success rounded-circle py-1 px-2" title="Save Changes">
                              <i class="bi bi-check-lg"></i>
                            </button>
                          </form>
                        <?php endif; ?>

                        <!-- View Details Modal Trigger -->
                        <button type="button" class="btn btn-sm btn-light border py-1 px-2 rounded-circle shadow-sm" data-bs-toggle="modal" data-bs-target="#detailsModal_<?= $e['id'] ?>" title="View Full Student Details">
                          <i class="bi bi-info-circle text-primary"></i>
                        </button>
                      </div>

                      <!-- Details Modal for this booking -->
                      <div class="modal fade text-start" id="detailsModal_<?= $e['id'] ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                          <div class="modal-content rounded-4 border-0 shadow">
                            <div class="modal-header bg-dark text-white px-4 py-3">
                              <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-success font-monospace"><?= htmlspecialchars($e['booking_reference']) ?></span>
                                <h6 class="modal-title mb-0 fw-bold">Student Booking Overview</h6>
                              </div>
                              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4">
                              <div class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom">
                                <div class="p-3 bg-light rounded-circle text-primary fs-3">
                                  <i class="bi bi-person-badge-fill"></i>
                                </div>
                                <div>
                                  <h5 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($e['student_name']) ?></h5>
                                  <div class="text-muted small"><?= htmlspecialchars($e['student_email']) ?> &bull; <?= htmlspecialchars($e['student_phone']) ?></div>
                                </div>
                              </div>

                              <div class="row g-2 mb-3 small">
                                <div class="col-6">
                                  <span class="text-muted d-block">Course Package:</span>
                                  <strong class="text-dark"><?= htmlspecialchars($e['course_name']) ?></strong>
                                </div>
                                <div class="col-6 text-end">
                                  <span class="text-muted d-block">Fee:</span>
                                  <strong class="text-success fs-6">$<?= number_format((float)$e['course_price'], 2) ?></strong>
                                </div>
                                <div class="col-6 mt-2">
                                  <span class="text-muted d-block">Transmission:</span>
                                  <span class="badge bg-light text-dark border"><?= ucfirst(htmlspecialchars($e['preferred_transmission'] ?? 'automatic')) ?></span>
                                </div>
                                <div class="col-6 mt-2 text-end">
                                  <span class="text-muted d-block">Pickup Location:</span>
                                  <span class="text-dark"><?= htmlspecialchars(!empty($e['pickup_address']) ? $e['pickup_address'] : 'Not specified') ?></span>
                                </div>
                              </div>

                              <div class="p-3 bg-light rounded-3 mb-3 small">
                                <div class="d-flex justify-content-between mb-1">
                                  <span class="text-muted">Payment Gateway:</span>
                                  <strong>Square Online</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                  <span class="text-muted">Payment Status:</span>
                                  <span class="badge bg-<?= $isPaidSquare ? 'success' : ($isPendingSquare ? 'warning text-dark' : ($payStatus === 'rejected' ? 'danger' : 'secondary')) ?>">
                                    <?= ucfirst(str_replace('_', ' ', $payStatus)) ?>
                                  </span>
                                </div>
                                <?php if (!empty($e['payment_tx_id'])): ?>
                                  <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">Square Tx ID:</span>
                                    <span class="font-monospace fw-bold text-dark"><?= htmlspecialchars($e['payment_tx_id']) ?></span>
                                  </div>
                                <?php endif; ?>
                                <?php if (!empty($e['receipt_url']) && $e['receipt_url'] !== '#'): ?>
                                  <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">Square Receipt:</span>
                                    <a href="<?= htmlspecialchars($e['receipt_url']) ?>" target="_blank" class="fw-bold text-success text-decoration-none">Open Receipt <i class="bi bi-box-arrow-up-right ms-1"></i></a>
                                  </div>
                                <?php endif; ?>
                                <div class="d-flex justify-content-between">
                                  <span class="text-muted">Created Timestamp:</span>
                                  <span><?= htmlspecialchars($e['created_at'] ?? 'now') ?></span>
                                </div>
                              </div>

                              <?php if (!empty($e['admin_notes'])): ?>
                                <div class="p-2 bg-warning-subtle text-dark rounded-3 small mb-2">
                                  <strong>Notes:</strong> <?= htmlspecialchars($e['admin_notes']) ?>
                                </div>
                              <?php endif; ?>
                            </div>
                            <div class="modal-footer bg-light border-0 py-2">
                              <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Close</button>
                            </div>
                          </div>
                        </div>
                      </div>

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
  <script>
    // 1. Live Instant Client-Side Search
    const searchInput = document.getElementById('liveSearchInput');
    const tableRows = document.querySelectorAll('#enrollmentsTable tbody tr[data-search]');
    const countBadge = document.getElementById('visibleRowCount');

    if (searchInput) {
      searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        let visibleCount = 0;

        tableRows.forEach(row => {
          const haystack = row.getAttribute('data-search') || row.innerText.toLowerCase();
          if (!query || haystack.includes(query)) {
            row.style.display = '';
            visibleCount++;
          } else {
            row.style.display = 'none';
          }
        });

        if (countBadge) {
          countBadge.textContent = visibleCount;
        }
      });
    }

    // 2. Auto-Refresh Toggle
    let autoRefreshActive = true;
    let refreshInterval = null;
    const btnRefresh = document.getElementById('btnToggleAutoRefresh');
    const refreshLabel = document.getElementById('autoRefreshLabel');

    function startAutoRefresh() {
      if (refreshInterval) clearInterval(refreshInterval);
      refreshInterval = setInterval(() => {
        // Only auto-reload if no modal is currently open and no text is being typed
        const anyModalOpen = document.querySelector('.modal.show');
        const isTyping = (document.activeElement && (document.activeElement.tagName === 'INPUT' || document.activeElement.tagName === 'SELECT'));
        if (!anyModalOpen && !isTyping) {
          location.reload();
        }
      }, 8000);
    }

    if (btnRefresh && refreshLabel) {
      startAutoRefresh();

      btnRefresh.addEventListener('click', function() {
        autoRefreshActive = !autoRefreshActive;
        if (autoRefreshActive) {
          startAutoRefresh();
          refreshLabel.textContent = 'Auto-Refresh: ON (8s)';
          btnRefresh.classList.replace('btn-outline-secondary', 'btn-outline-success');
        } else {
          if (refreshInterval) clearInterval(refreshInterval);
          refreshLabel.textContent = 'Auto-Refresh: PAUSED';
          btnRefresh.classList.replace('btn-outline-success', 'btn-outline-secondary');
        }
      });
    }
  </script>
</body>
</html>
