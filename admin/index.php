<?php
declare(strict_types=1);

require_once __DIR__ . '/../backend/src/Database.php';
require_once __DIR__ . '/../backend/src/Auth.php';
require_once __DIR__ . '/../backend/src/Models/Inquiry.php';
require_once __DIR__ . '/../backend/src/Models/Enrollment.php';
require_once __DIR__ . '/../backend/src/Models/Course.php';
require_once __DIR__ . '/../backend/src/Models/Newsletter.php';

use DriveMaster\Auth;
use DriveMaster\Database;
use DriveMaster\Models\Inquiry;
use DriveMaster\Models\Enrollment;
use DriveMaster\Models\Course;
use DriveMaster\Models\Newsletter;

$admin = Auth::requireAuth();
$db = Database::getInstance();
$dbOnline = $db->isAvailable();

$inquiryStats = Inquiry::countByStatus();
$enrollmentStats = Enrollment::countStats();
$recentInquiries = Inquiry::all(['status' => 'all']);
$recentEnrollments = Enrollment::all(['status' => 'all']);
$newsletterCount = Newsletter::count();
$recentSubscribers = Newsletter::all();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard | DriveMaster Academy</title>
  <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🚗</text></svg>">
  <!-- Local Fonts & Icons -->
  <link rel="stylesheet" href="../assets/vendor/fonts/fonts.css">
  <link rel="stylesheet" href="../assets/vendor/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/vendor/bootstrap-icons/bootstrap-icons.min.css">
  <!-- Chart.js -->
  <script src="../assets/vendor/chartjs/chart.umd.min.js"></script>
  <style>
    :root {
      --primary: #2A6B5C;
      --primary-dark: #183e35;
      --primary-light: #eaf3f1;
      --accent-green: #00b060;
      --sidebar-width: 260px;
    }
    body {
      font-family: 'Plus Jakarta Sans', sans-serif;
      background-color: #f8fafc;
      color: #0f172a;
      overflow-x: hidden;
    }
    .admin-sidebar {
      width: var(--sidebar-width);
      height: 100vh;
      position: fixed;
      top: 0;
      left: 0;
      background: #133a30;
      color: #e2e8f0;
      display: flex;
      flex-direction: column;
      z-index: 1050;
      box-shadow: 4px 0 20px rgba(0, 0, 0, 0.08);
    }
    .sidebar-brand {
      padding: 1.5rem 1.25rem;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      display: flex;
      align-items: center;
      gap: 0.75rem;
      text-decoration: none;
      color: #ffffff;
    }
    .brand-icon-box {
      width: 40px;
      height: 40px;
      border-radius: 10px;
      background: var(--accent-green);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.2rem;
      color: #ffffff;
    }
    .sidebar-nav {
      list-style: none;
      padding: 1.25rem 0.75rem;
      margin: 0;
      flex-grow: 1;
      overflow-y: auto;
    }
    .sidebar-nav-item {
      margin-bottom: 0.35rem;
    }
    .sidebar-nav-link {
      display: flex;
      align-items: center;
      gap: 0.85rem;
      padding: 0.75rem 1rem;
      color: #cbd5e1;
      text-decoration: none;
      border-radius: 10px;
      font-weight: 600;
      font-size: 0.92rem;
      transition: all 0.2s ease;
    }
    .sidebar-nav-link:hover,
    .sidebar-nav-link.active {
      background: rgba(255, 255, 255, 0.12);
      color: #ffffff;
    }
    .sidebar-nav-link.active {
      background: var(--accent-green);
      color: #ffffff;
    }
    .admin-main {
      margin-left: var(--sidebar-width);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }
    .admin-topbar {
      background: #ffffff;
      border-bottom: 1px solid #e2e8f0;
      padding: 0.85rem 2rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      position: sticky;
      top: 0;
      z-index: 1040;
    }
    .kpi-card {
      background: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 16px;
      padding: 1.5rem;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
      transition: transform 0.2s ease;
      height: 100%;
    }
    .kpi-card:hover { transform: translateY(-3px); }
    .kpi-icon-wrap {
      width: 48px;
      height: 48px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.35rem;
    }
    .chart-card {
      background: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 16px;
      padding: 1.5rem;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
    }
    .status-badge-new { background-color: #fee2e2; color: #dc2626; font-weight: 700; }
    .status-badge-contacted { background-color: #fef3c7; color: #d97706; font-weight: 700; }
    .status-badge-enrolled { background-color: #dcfce7; color: #16a34a; font-weight: 700; }
    .status-badge-confirmed { background-color: #dbeafe; color: #2563eb; font-weight: 700; }
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
      <div class="brand-icon-box">
        <i class="bi bi-speedometer2"></i>
      </div>
      <div>
        <div class="fw-bold" style="font-family: 'Space Grotesk', sans-serif; font-size: 1.15rem;">DriveMaster</div>
        <div class="small text-white-50" style="font-size: 0.72rem; letter-spacing: 0.05em;">ADMINISTRATION</div>
      </div>
    </a>

    <ul class="sidebar-nav">
      <li class="sidebar-nav-item">
        <a href="index.php" class="sidebar-nav-link active">
          <i class="bi bi-grid-fill"></i> Dashboard
        </a>
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
        <a href="subscribers.php" class="sidebar-nav-link">
          <i class="bi bi-mailbox2"></i> Newsletter Subscribers
          <span class="badge bg-light text-dark ms-auto rounded-pill"><?= $newsletterCount ?></span>
        </a>
      </li>
      <li class="sidebar-nav-item">
        <a href="courses.php" class="sidebar-nav-link">
          <i class="bi bi-car-front-fill"></i> Courses &amp; Rates
        </a>
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
    <!-- Topbar -->
    <header class="admin-topbar">
      <div class="d-flex align-items-center gap-3">
        <h4 class="mb-0 fw-bold" style="font-family: 'Space Grotesk', sans-serif;">Dashboard Overview</h4>
        <?php if ($dbOnline): ?>
          <span class="badge bg-success-subtle text-success small px-2 py-1"><i class="bi bi-check-circle-fill me-1"></i> MySQL Connected</span>
        <?php endif; ?>
      </div>

      <div class="d-flex align-items-center gap-2">
        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 fw-bold me-2">
          <i class="bi bi-cash-stack me-1"></i> Revenue: $<?= number_format((float)($enrollmentStats['revenue'] ?? 0), 2) ?>
        </span>
        <a href="/backend/swagger/" target="_blank" class="btn btn-outline-secondary btn-sm fw-bold">
          <i class="bi bi-code-slash me-1"></i> Swagger UI
        </a>
        <a href="../courses#courses-catalog" target="_blank" class="btn btn-outline-success btn-sm fw-bold">
          <i class="bi bi-credit-card-2-front me-1"></i> Course Catalog
        </a>
      </div>
    </header>

    <div class="p-4 flex-grow-1">

      <!-- 4 KPI Cards -->
      <div class="row g-4 mb-4">
        <div class="col-xl-3 col-sm-6">
          <a href="enrollments.php" class="text-decoration-none">
            <div class="kpi-card">
              <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="text-muted small fw-bold text-uppercase">Total Bookings</span>
                <div class="kpi-icon-wrap bg-primary-subtle text-primary">
                  <i class="bi bi-calendar-check-fill"></i>
                </div>
              </div>
              <h2 class="display-6 fw-bold mb-1 text-dark"><?= $enrollmentStats['total_bookings'] ?? 0 ?></h2>
              <div class="small text-muted">All registered students</div>
            </div>
          </a>
        </div>

        <div class="col-xl-3 col-sm-6">
          <a href="enrollments.php?filter=square_pending" class="text-decoration-none">
            <div class="kpi-card <?= ($enrollmentStats['square_pending'] ?? 0) > 0 ? 'border-warning shadow-sm' : '' ?>">
              <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="text-danger small fw-bold text-uppercase">Pending Request</span>
                <div class="kpi-icon-wrap bg-warning-subtle text-warning">
                  <i class="bi bi-hourglass-split"></i>
                </div>
              </div>
              <h2 class="display-6 fw-bold mb-1 text-danger"><?= $enrollmentStats['square_pending'] ?? 0 ?></h2>
              <div class="small text-danger fw-semibold">Awaiting payment approval</div>
            </div>
          </a>
        </div>

        <div class="col-xl-3 col-sm-6">
          <a href="inquiries.php" class="text-decoration-none">
            <div class="kpi-card <?= ($inquiryStats['new'] ?? 0) > 0 ? 'border-danger shadow-sm' : '' ?>">
              <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="text-muted small fw-bold text-uppercase">Contact Inquiries</span>
                <div class="kpi-icon-wrap bg-danger-subtle text-danger">
                  <i class="bi bi-envelope-fill"></i>
                </div>
              </div>
              <div class="d-flex align-items-baseline gap-2 mb-1">
                <h2 class="display-6 fw-bold mb-0 text-dark"><?= $inquiryStats['total'] ?? count($recentInquiries) ?></h2>
                <?php if (($inquiryStats['new'] ?? 0) > 0): ?>
                  <span class="badge bg-danger rounded-pill px-2 py-1 small"><?= $inquiryStats['new'] ?> New</span>
                <?php endif; ?>
              </div>
              <div class="small text-muted">Frontend contact form submissions</div>
            </div>
          </a>
        </div>

        <div class="col-xl-3 col-sm-6">
          <a href="subscribers.php" class="text-decoration-none">
            <div class="kpi-card">
              <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="text-muted small fw-bold text-uppercase">Newsletter</span>
                <div class="kpi-icon-wrap bg-success-subtle text-success">
                  <i class="bi bi-mailbox2"></i>
                </div>
              </div>
              <h2 class="display-6 fw-bold mb-1 text-success"><?= $newsletterCount ?></h2>
              <div class="small text-muted">Active newsletter subscribers</div>
            </div>
          </a>
        </div>
      </div>

      <!-- Charts Row -->
      <div class="row g-4 mb-4">
        <div class="col-lg-8">
          <div class="chart-card">
            <h5 class="fw-bold mb-3"><i class="bi bi-graph-up-arrow text-success me-2"></i>Weekly Student Bookings Trend</h5>
            <canvas id="inquiriesChart" height="95"></canvas>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="chart-card">
            <h5 class="fw-bold mb-3"><i class="bi bi-pie-chart-fill text-primary me-2"></i>Course Demand Breakdown</h5>
            <canvas id="courseDemandChart" height="200"></canvas>
          </div>
        </div>
      </div>

      <!-- Recent Pending Requests Table -->
      <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
          <h5 class="mb-0 fw-bold"><i class="bi bi-clock-history text-primary me-2"></i>Recent Pending Requests</h5>
          <a href="enrollments.php" class="btn btn-outline-success btn-sm fw-bold">View All Pending Requests &rarr;</a>
        </div>
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-uppercase">
              <tr>
                <th class="ps-4">Booking Ref</th>
                <th>Student Details</th>
                <th>Selected Course</th>
                <th>Amount</th>
                <th>Payment Status</th>
                <th>Overall Status</th>
                <th class="text-end pe-4">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($recentEnrollments)): ?>
                <tr>
                  <td colspan="7" class="text-center py-4 text-muted">No student booking requests found.</td>
                </tr>
              <?php else: ?>
                <?php foreach (array_slice($recentEnrollments, 0, 5) as $e): 
                  $payStatus = $e['payment_status'] ?? 'unpaid';
                  $isPending = ($payStatus === 'payment_requested');
                  $isPaid = ($payStatus === 'paid_in_full');
                ?>
                  <tr>
                    <td class="ps-4">
                      <span class="badge bg-dark font-monospace"><?= htmlspecialchars($e['booking_reference']) ?></span>
                    </td>
                    <td>
                      <div class="fw-bold text-dark"><?= htmlspecialchars($e['student_name']) ?></div>
                      <div class="small text-muted"><?= htmlspecialchars($e['student_email']) ?> &bull; <?= htmlspecialchars($e['student_phone']) ?></div>
                    </td>
                    <td>
                      <div class="fw-bold text-dark small"><?= htmlspecialchars($e['course_name']) ?></div>
                      <div class="small text-muted"><i class="bi bi-gear-fill me-1"></i><?= ucfirst(htmlspecialchars($e['preferred_transmission'] ?? 'automatic')) ?></div>
                    </td>
                    <td>
                      <span class="fw-bold text-success">$<?= number_format((float)$e['course_price'], 2) ?></span>
                    </td>
                    <td>
                      <?php if ($isPending): ?>
                        <span class="badge bg-warning-subtle text-dark border border-warning-subtle rounded-pill py-1 px-2 fw-semibold">Payment Requested</span>
                      <?php elseif ($isPaid): ?>
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill py-1 px-2 fw-semibold">Paid Full</span>
                      <?php else: ?>
                        <span class="badge bg-secondary-subtle text-dark rounded-pill py-1 px-2 fw-semibold"><?= ucfirst(str_replace('_', ' ', $payStatus)) ?></span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <span class="badge bg-<?= ($e['status'] === 'confirmed' || $isPaid) ? 'success' : ($isPending ? 'warning text-dark' : 'secondary') ?> rounded-pill px-2 py-1">
                        <?= ucfirst(str_replace('_', ' ', $e['status'] ?? 'pending')) ?>
                      </span>
                    </td>
                    <td class="text-end pe-4">
                      <a href="enrollments.php" class="btn btn-sm btn-outline-success fw-bold rounded-pill px-3 py-1">
                        Manage <i class="bi bi-arrow-right-short"></i>
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Two Columns: Recent Contact Inquiries & Recent Newsletter Subscribers -->
      <div class="row g-4 mb-4">
        <!-- Recent Contact Inquiries -->
        <div class="col-lg-7">
          <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
            <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
              <div>
                <h5 class="mb-0 fw-bold"><i class="bi bi-envelope-paper-fill text-danger me-2"></i>Recent Contact Inquiries</h5>
                <small class="text-muted">Submissions from frontend contact form</small>
              </div>
              <a href="inquiries.php" class="btn btn-outline-danger btn-sm fw-bold">Manage Inquiries &rarr;</a>
            </div>
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-uppercase">
                  <tr>
                    <th class="ps-4">Sender</th>
                    <th>Subject / Message</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th class="text-end pe-4">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($recentInquiries)): ?>
                    <tr>
                      <td colspan="5" class="text-center py-4 text-muted">No contact messages received yet.</td>
                    </tr>
                  <?php else: ?>
                    <?php foreach (array_slice($recentInquiries, 0, 5) as $inq): ?>
                      <tr>
                        <td class="ps-4">
                          <div class="fw-bold text-dark"><?= htmlspecialchars($inq['full_name'] ?? $inq['name'] ?? 'Visitor') ?></div>
                          <div class="small text-muted"><?= htmlspecialchars($inq['email'] ?? '') ?></div>
                          <?php if (!empty($inq['phone'])): ?>
                            <div class="small text-muted font-monospace" style="font-size: 0.75rem;"><i class="bi bi-telephone me-1"></i><?= htmlspecialchars($inq['phone']) ?></div>
                          <?php endif; ?>
                        </td>
                        <td>
                          <div class="fw-semibold text-dark small"><?= htmlspecialchars($inq['subject'] ?? 'Website Inquiry') ?></div>
                          <div class="small text-muted text-truncate" style="max-width: 220px;" title="<?= htmlspecialchars($inq['message'] ?? '') ?>">
                            <?= htmlspecialchars($inq['message'] ?? '') ?>
                          </div>
                        </td>
                        <td>
                          <div class="small text-muted"><?= htmlspecialchars(date('M d, Y', strtotime($inq['created_at'] ?? 'now'))) ?></div>
                          <div class="small text-muted" style="font-size: 0.72rem;"><?= htmlspecialchars(date('h:i A', strtotime($inq['created_at'] ?? 'now'))) ?></div>
                        </td>
                        <td>
                          <span class="badge status-badge-<?= htmlspecialchars($inq['status'] ?? 'new') ?> rounded-pill px-2 py-1">
                            <?= ucfirst(str_replace('_', ' ', $inq['status'] ?? 'new')) ?>
                          </span>
                        </td>
                        <td class="text-end pe-4">
                          <a href="inquiries.php?search=<?= urlencode($inq['email'] ?? '') ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-2 py-1" title="View details in inquiries manager">
                            <i class="bi bi-eye"></i>
                          </a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Recent Newsletter Subscribers -->
        <div class="col-lg-5">
          <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
            <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
              <div>
                <h5 class="mb-0 fw-bold"><i class="bi bi-mailbox2 text-success me-2"></i>Newsletter Signups</h5>
                <small class="text-muted">Submissions from frontend newsletter</small>
              </div>
              <a href="subscribers.php" class="btn btn-outline-success btn-sm fw-bold">View All &rarr;</a>
            </div>
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-uppercase">
                  <tr>
                    <th class="ps-4">Email</th>
                    <th>Source</th>
                    <th>Date</th>
                    <th class="text-end pe-4">Status</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($recentSubscribers)): ?>
                    <tr>
                      <td colspan="4" class="text-center py-4 text-muted">No newsletter subscribers yet.</td>
                    </tr>
                  <?php else: ?>
                    <?php foreach (array_slice($recentSubscribers, 0, 5) as $sub): ?>
                      <tr>
                        <td class="ps-4">
                          <div class="fw-bold text-dark text-truncate" style="max-width: 180px;" title="<?= htmlspecialchars($sub['email'] ?? '') ?>">
                            <i class="bi bi-envelope me-1 text-primary"></i>
                            <?= htmlspecialchars($sub['email'] ?? '') ?>
                          </div>
                        </td>
                        <td>
                          <span class="badge bg-light text-dark border rounded-pill px-2 py-1 small">
                            <?= htmlspecialchars($sub['source'] ?? 'website') ?>
                          </span>
                        </td>
                        <td>
                          <div class="small text-muted"><?= htmlspecialchars(date('M d', strtotime($sub['created_at'] ?? 'now'))) ?></div>
                          <div class="small text-muted" style="font-size: 0.72rem;"><?= htmlspecialchars(date('h:i A', strtotime($sub['created_at'] ?? 'now'))) ?></div>
                        </td>
                        <td class="text-end pe-4">
                          <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1">
                            <?= ucfirst(htmlspecialchars($sub['status'] ?? 'active')) ?>
                          </span>
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

    </div>

    <!-- Admin Footer -->
    <footer class="bg-white border-top py-3 px-4 text-center small text-muted">
      &copy; 2026 DriveMaster Driving School &bull; Core PHP 8+ Backend &bull; MySQL &bull; OpenAPI 3.0
    </footer>
  </div>

  <script>
    // Chart 1: Bookings Timeline
    const ctx1 = document.getElementById('inquiriesChart').getContext('2d');
    new Chart(ctx1, {
      type: 'line',
      data: {
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        datasets: [{
          label: 'Student Bookings',
          data: [12, 19, 15, 25, 22, 30, 28],
          borderColor: '#00b060',
          backgroundColor: 'rgba(0, 176, 96, 0.1)',
          fill: true,
          tension: 0.35,
          borderWidth: 3
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
      }
    });

    // Chart 2: Course Demand
    const ctx2 = document.getElementById('courseDemandChart').getContext('2d');
    new Chart(ctx2, {
      type: 'doughnut',
      data: {
        labels: ['Standard Pass', 'Automatic Only', 'Intensive Fast-Track', 'Beginner Starter'],
        datasets: [{
          data: [42, 28, 18, 12],
          backgroundColor: ['#00b060', '#2A6B5C', '#f59e0b', '#3b82f6'],
          borderWidth: 0
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { position: 'bottom' } }
      }
    });
  </script>
</body>
</html>
