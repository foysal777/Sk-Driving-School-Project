<?php
declare(strict_types=1);

require_once __DIR__ . '/../backend/src/Database.php';
require_once __DIR__ . '/../backend/src/Auth.php';
require_once __DIR__ . '/../backend/src/Models/Inquiry.php';
require_once __DIR__ . '/../backend/src/Models/Enrollment.php';
require_once __DIR__ . '/../backend/src/Models/Course.php';

use DriveMaster\Auth;
use DriveMaster\Database;
use DriveMaster\Models\Inquiry;
use DriveMaster\Models\Enrollment;
use DriveMaster\Models\Course;

$admin = Auth::requireAuth();
$db = Database::getInstance();
$dbOnline = $db->isAvailable();

$inquiryStats = Inquiry::countByStatus();
$enrollmentStats = Enrollment::countStats();
$recentInquiries = Inquiry::all(['status' => 'all']);
$recentEnrollments = Enrollment::all(['status' => 'all']);
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
        <a href="inquiries.php" class="sidebar-nav-link">
          <i class="bi bi-envelope-fill"></i> Inquiries &amp; Leads
          <?php if ($inquiryStats['new'] > 0): ?>
            <span class="badge bg-danger ms-auto rounded-pill"><?= $inquiryStats['new'] ?></span>
          <?php endif; ?>
        </a>
      </li>
      <li class="sidebar-nav-item">
        <a href="enrollments.php" class="sidebar-nav-link">
          <i class="bi bi-calendar-check-fill"></i> Course Bookings
          <?php if (($enrollmentStats['square_pending'] ?? 0) > 0): ?>
            <span class="badge bg-warning text-dark ms-auto rounded-pill"><?= $enrollmentStats['square_pending'] ?></span>
          <?php endif; ?>
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
        <a href="../backend/install.php" target="_blank" class="sidebar-nav-link">
          <i class="bi bi-database-gear text-warning"></i> DB Installer
        </a>
      </li>
      <li class="sidebar-nav-item">
        <a href="../index.html" target="_blank" class="sidebar-nav-link">
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
          <div class="small fw-bold text-truncate text-white"><?= htmlspecialchars($admin['full_name']) ?></div>
          <div class="small text-white-50" style="font-size: 0.72rem;"><?= htmlspecialchars($admin['role']) ?></div>
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
        <?php else: ?>
          <a href="/backend/install.php" class="badge bg-warning-subtle text-dark text-decoration-none small px-2 py-1" title="Click to run installer">
            <i class="bi bi-exclamation-triangle-fill text-warning me-1"></i> Dev Mode (Click to install DB)
          </a>
        <?php endif; ?>
      </div>

      <div class="d-flex align-items-center gap-2">
        <a href="/backend/swagger/" target="_blank" class="btn btn-outline-secondary btn-sm fw-bold">
          <i class="bi bi-code-slash me-1"></i> Swagger UI
        </a>
        <a href="/contact.html" target="_blank" class="btn btn-outline-success btn-sm fw-bold">
          <i class="bi bi-send me-1"></i> Test Contact Form
        </a>
      </div>
    </header>

    <div class="p-4 flex-grow-1">
      <?php if (($enrollmentStats['square_pending'] ?? 0) > 0): ?>
        <div class="alert alert-warning border border-warning d-flex align-items-center justify-content-between p-3 rounded-4 shadow-sm mb-4">
          <div class="d-flex align-items-center gap-3">
            <div class="bg-warning text-dark p-2 rounded-3 fs-4"><i class="bi bi-credit-card-2-front-fill"></i></div>
            <div>
              <h6 class="fw-bold mb-0 text-dark"><?= $enrollmentStats['square_pending'] ?> Square Payment Request(s) Awaiting Approval</h6>
              <div class="small text-muted">Students have requested to pay via Square dummy gateway and need your authorization to unlock checkout.</div>
            </div>
          </div>
          <a href="enrollments.php?filter=square_pending" class="btn btn-warning fw-bold px-3 py-2 text-dark shadow-sm">
            <i class="bi bi-check-circle-fill me-1"></i> Review &amp; Approve
          </a>
        </div>
      <?php endif; ?>

      <!-- 4 KPI Cards -->
      <div class="row g-4 mb-4">
        <div class="col-xl-3 col-sm-6">
          <div class="kpi-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <span class="text-muted small fw-bold text-uppercase">Total Inquiries</span>
              <div class="kpi-icon-wrap bg-primary-subtle text-primary">
                <i class="bi bi-envelope-paper-fill"></i>
              </div>
            </div>
            <h2 class="display-6 fw-bold mb-1"><?= $inquiryStats['total'] ?></h2>
            <div class="small text-muted">Messages from contact email box</div>
          </div>
        </div>

        <div class="col-xl-3 col-sm-6">
          <div class="kpi-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <span class="text-muted small fw-bold text-uppercase">New / Unread</span>
              <div class="kpi-icon-wrap bg-danger-subtle text-danger">
                <i class="bi bi-bell-fill"></i>
              </div>
            </div>
            <h2 class="display-6 fw-bold mb-1 text-danger"><?= $inquiryStats['new'] ?></h2>
            <div class="small text-muted">Awaiting instructor response</div>
          </div>
        </div>

        <div class="col-xl-3 col-sm-6">
          <div class="kpi-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <span class="text-muted small fw-bold text-uppercase">Course Bookings</span>
              <div class="kpi-icon-wrap bg-success-subtle text-success">
                <i class="bi bi-journal-check"></i>
              </div>
            </div>
            <h2 class="display-6 fw-bold mb-1"><?= $enrollmentStats['total_bookings'] ?></h2>
            <div class="small text-muted">Active &amp; pending students</div>
          </div>
        </div>

        <div class="col-xl-3 col-sm-6">
          <div class="kpi-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <span class="text-muted small fw-bold text-uppercase">Est. Revenue</span>
              <div class="kpi-icon-wrap bg-info-subtle text-info">
                <i class="bi bi-currency-dollar"></i>
              </div>
            </div>
            <h2 class="display-6 fw-bold mb-1 text-success">$<?= number_format($enrollmentStats['revenue'], 2) ?></h2>
            <div class="small text-muted">From confirmed courses</div>
          </div>
        </div>
      </div>

      <!-- Charts Row -->
      <div class="row g-4 mb-4">
        <div class="col-lg-8">
          <div class="chart-card">
            <h5 class="fw-bold mb-3"><i class="bi bi-graph-up-arrow text-success me-2"></i>Weekly Student Inquiries Trend</h5>
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

      <!-- Recent Inquiries Table -->
      <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
          <h5 class="mb-0 fw-bold"><i class="bi bi-chat-left-dots-fill text-primary me-2"></i>Recent Contact Inquiries</h5>
          <a href="/admin/inquiries.php" class="btn btn-outline-primary btn-sm fw-bold">View All Leads &rarr;</a>
        </div>
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-uppercase">
              <tr>
                <th class="ps-4">Student Name</th>
                <th>Contact Info</th>
                <th>Subject / Course</th>
                <th>Schedule</th>
                <th>Status</th>
                <th>Submitted</th>
                <th class="text-end pe-4">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach (array_slice($recentInquiries, 0, 5) as $inq): ?>
                <tr>
                  <td class="ps-4 fw-bold text-dark">
                    <?= htmlspecialchars($inq['full_name']) ?>
                  </td>
                  <td>
                    <div><a href="mailto:<?= htmlspecialchars($inq['email']) ?>" class="text-decoration-none"><?= htmlspecialchars($inq['email']) ?></a></div>
                    <div class="small text-muted"><?= htmlspecialchars($inq['phone']) ?></div>
                  </td>
                  <td>
                    <span class="badge bg-light text-dark border"><?= htmlspecialchars($inq['subject']) ?></span>
                  </td>
                  <td class="small text-muted">
                    <?= htmlspecialchars($inq['preferred_schedule'] ?? 'Flexible') ?>
                  </td>
                  <td>
                    <span class="badge status-badge-<?= $inq['status'] ?> px-2 py-1 rounded-pill">
                      <?= ucfirst($inq['status']) ?>
                    </span>
                  </td>
                  <td class="small text-muted">
                    <?= date('M d, H:i', strtotime($inq['created_at'])) ?>
                  </td>
                  <td class="text-end pe-4">
                    <a href="/admin/inquiries.php" class="btn btn-sm btn-light border">
                      <i class="bi bi-pencil-square"></i> Manage
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>

    <!-- Admin Footer -->
    <footer class="bg-white border-top py-3 px-4 text-center small text-muted">
      &copy; 2026 DriveMaster Driving School &bull; Core PHP 8+ Backend &bull; MySQL &bull; OpenAPI 3.0
    </footer>
  </div>

  <script>
    // Chart 1: Inquiries Timeline
    const ctx1 = document.getElementById('inquiriesChart').getContext('2d');
    new Chart(ctx1, {
      type: 'line',
      data: {
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        datasets: [{
          label: 'Contact Inquiries',
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
