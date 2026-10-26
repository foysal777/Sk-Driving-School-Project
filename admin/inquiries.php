<?php
declare(strict_types=1);

require_once __DIR__ . '/../backend/src/Database.php';
require_once __DIR__ . '/../backend/src/Auth.php';
require_once __DIR__ . '/../backend/src/Models/Inquiry.php';

use DriveMaster\Auth;
use DriveMaster\Models\Inquiry;

$admin = Auth::requireAuth();

// Handle Status Update or Delete
$alert = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);

    if ($action === 'update_status' && $id > 0) {
        $status = $_POST['status'] ?? 'new';
        $notes = $_POST['admin_notes'] ?? null;
        Inquiry::updateStatus($id, $status, $notes);
        $alert = ['type' => 'success', 'msg' => "Inquiry #{$id} status updated to '{$status}'."];
    } elseif ($action === 'delete' && $id > 0) {
        Inquiry::delete($id);
        $alert = ['type' => 'success', 'msg' => "Inquiry #{$id} has been removed."];
    }
}

$currentStatus = $_GET['status'] ?? 'all';
$searchQuery = $_GET['search'] ?? '';

$inquiries = Inquiry::all(['status' => $currentStatus, 'search' => $searchQuery]);
$counts = Inquiry::countByStatus();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Inquiries | DriveMaster Admin</title>
  <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>✉️</text></svg>">
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
    .status-badge-new { background-color: #fee2e2; color: #dc2626; font-weight: 700; }
    .status-badge-contacted { background-color: #fef3c7; color: #d97706; font-weight: 700; }
    .status-badge-in_progress { background-color: #e0e7ff; color: #4338ca; font-weight: 700; }
    .status-badge-enrolled { background-color: #dcfce7; color: #16a34a; font-weight: 700; }
    .status-badge-closed { background-color: #f1f5f9; color: #64748b; font-weight: 700; }
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
      <li><a href="/admin/inquiries.php" class="sidebar-nav-link active"><i class="bi bi-envelope-fill"></i> Inquiries &amp; Leads</a></li>
      <li><a href="/admin/enrollments.php" class="sidebar-nav-link"><i class="bi bi-calendar-check-fill"></i> Course Bookings</a></li>
      <li><a href="/admin/courses.php" class="sidebar-nav-link"><i class="bi bi-car-front-fill"></i> Courses &amp; Rates</a></li>
      <li class="mt-3 pt-3 border-top border-white-10">
        <a href="/backend/swagger/" target="_blank" class="sidebar-nav-link"><i class="bi bi-code-square text-info"></i> Swagger API Docs</a>
      </li>
      <li><a href="../home" target="_blank" class="sidebar-nav-link"><i class="bi bi-box-arrow-up-right text-success"></i> View Live Site</a></li>
    </ul>
    <div class="p-3 border-top border-white-10">
      <a href="/admin/logout.php" class="btn btn-outline-light btn-sm w-100 fw-semibold"><i class="bi bi-box-arrow-right me-1"></i> Sign Out</a>
    </div>
  </aside>

  <!-- Main Content -->
  <div class="admin-main">
    <header class="admin-topbar">
      <h4 class="mb-0 fw-bold" style="font-family: 'Space Grotesk', sans-serif;">Student Inquiries &amp; Leads</h4>
      <div class="d-flex gap-2">
        <a href="../contact" target="_blank" class="btn btn-outline-success btn-sm fw-bold">
          <i class="bi bi-envelope-paper-heart me-1"></i> Test Contact Form
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

      <!-- Filter Bar -->
      <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
          <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <!-- Filter Tabs -->
            <ul class="nav nav-pills gap-1">
              <li class="nav-item">
                <a class="nav-link <?= $currentStatus === 'all' ? 'active' : '' ?>" href="?status=all">
                  All (<?= $counts['total'] ?>)
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?= $currentStatus === 'new' ? 'active' : '' ?> text-danger" href="?status=new">
                  New (<?= $counts['new'] ?>)
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?= $currentStatus === 'contacted' ? 'active' : '' ?> text-warning" href="?status=contacted">
                  Contacted (<?= $counts['contacted'] ?>)
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?= $currentStatus === 'enrolled' ? 'active' : '' ?> text-success" href="?status=enrolled">
                  Enrolled (<?= $counts['enrolled'] ?>)
                </a>
              </li>
            </ul>

            <!-- Search -->
            <form method="GET" class="d-flex gap-2">
              <input type="hidden" name="status" value="<?= htmlspecialchars($currentStatus) ?>">
              <input type="text" name="search" class="form-control form-control-sm" placeholder="Search name, email, phone..." value="<?= htmlspecialchars($searchQuery) ?>" style="width: 240px;">
              <button type="submit" class="btn btn-secondary btn-sm"><i class="bi bi-search"></i></button>
            </form>
          </div>
        </div>
      </div>

      <!-- Inquiries Table -->
      <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-uppercase">
              <tr>
                <th class="ps-4">ID</th>
                <th>Student</th>
                <th>Subject / Location</th>
                <th>Timeline &amp; Schedule</th>
                <th>Status</th>
                <th>Date</th>
                <th class="text-end pe-4">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($inquiries)): ?>
                <tr>
                  <td colspan="7" class="text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                    No student inquiries found matching current filter.
                  </td>
                </tr>
              <?php else: ?>
                <?php foreach ($inquiries as $inq): ?>
                  <tr>
                    <td class="ps-4 fw-bold text-muted">#<?= $inq['id'] ?></td>
                    <td>
                      <div class="fw-bold text-dark"><?= htmlspecialchars($inq['full_name']) ?></div>
                      <div class="small"><a href="mailto:<?= htmlspecialchars($inq['email']) ?>" class="text-decoration-none"><?= htmlspecialchars($inq['email']) ?></a></div>
                      <div class="small text-muted"><a href="tel:<?= htmlspecialchars($inq['phone']) ?>" class="text-muted text-decoration-none"><?= htmlspecialchars($inq['phone']) ?></a></div>
                    </td>
                    <td>
                      <div class="badge bg-light text-dark border mb-1"><?= htmlspecialchars($inq['subject']) ?></div>
                      <?php if (!empty($inq['pickup_location'])): ?>
                        <div class="small text-muted"><i class="bi bi-geo-alt me-1"></i> <?= htmlspecialchars($inq['pickup_location']) ?></div>
                      <?php endif; ?>
                    </td>
                    <td class="small">
                      <div class="fw-semibold text-dark"><?= htmlspecialchars($inq['preferred_schedule'] ?? 'Flexible') ?></div>
                      <div class="text-muted"><?= htmlspecialchars($inq['timeline'] ?? 'Not specified') ?></div>
                    </td>
                    <td>
                      <span class="badge status-badge-<?= $inq['status'] ?> px-2 py-1 rounded-pill">
                        <?= ucfirst(str_replace('_', ' ', $inq['status'])) ?>
                      </span>
                    </td>
                    <td class="small text-muted">
                      <?= date('M d, Y', strtotime($inq['created_at'])) ?><br>
                      <?= date('H:i', strtotime($inq['created_at'])) ?>
                    </td>
                    <td class="text-end pe-4">
                      <div class="d-flex justify-content-end gap-1">
                        <!-- View Modal Trigger -->
                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#inquiryModal<?= $inq['id'] ?>" title="View Message">
                          <i class="bi bi-eye"></i>
                        </button>

                        <!-- Quick Status Form -->
                        <form method="POST" class="d-inline">
                          <input type="hidden" name="action" value="update_status">
                          <input type="hidden" name="id" value="<?= $inq['id'] ?>">
                          <select name="status" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                            <option value="new" <?= $inq['status'] === 'new' ? 'selected' : '' ?>>New</option>
                            <option value="contacted" <?= $inq['status'] === 'contacted' ? 'selected' : '' ?>>Contacted</option>
                            <option value="in_progress" <?= $inq['status'] === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                            <option value="enrolled" <?= $inq['status'] === 'enrolled' ? 'selected' : '' ?>>Enrolled</option>
                            <option value="closed" <?= $inq['status'] === 'closed' ? 'selected' : '' ?>>Closed</option>
                          </select>
                        </form>

                        <!-- Delete Button -->
                        <form method="POST" class="d-inline" onsubmit="return confirm('Delete this inquiry record?');">
                          <input type="hidden" name="action" value="delete">
                          <input type="hidden" name="id" value="<?= $inq['id'] ?>">
                          <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                            <i class="bi bi-trash"></i>
                          </button>
                        </form>
                      </div>

                      <!-- Detail Modal -->
                      <div class="modal fade text-start" id="inquiryModal<?= $inq['id'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                          <div class="modal-content border-0 shadow">
                            <div class="modal-header bg-light">
                              <h5 class="modal-title fw-bold">Inquiry from <?= htmlspecialchars($inq['full_name']) ?></h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body p-4">
                              <div class="mb-3">
                                <label class="small text-muted fw-bold text-uppercase">Course / Subject</label>
                                <div class="fw-bold"><?= htmlspecialchars($inq['subject']) ?></div>
                              </div>
                              <div class="row g-2 mb-3">
                                <div class="col-6">
                                  <label class="small text-muted fw-bold text-uppercase">Email</label>
                                  <div><a href="mailto:<?= htmlspecialchars($inq['email']) ?>"><?= htmlspecialchars($inq['email']) ?></a></div>
                                </div>
                                <div class="col-6">
                                  <label class="small text-muted fw-bold text-uppercase">Phone</label>
                                  <div><a href="tel:<?= htmlspecialchars($inq['phone']) ?>"><?= htmlspecialchars($inq['phone']) ?></a></div>
                                </div>
                              </div>
                              <div class="mb-3">
                                <label class="small text-muted fw-bold text-uppercase">Message Content</label>
                                <div class="p-3 bg-light rounded-3 text-secondary" style="white-space: pre-wrap;"><?= htmlspecialchars($inq['message']) ?></div>
                              </div>
                              <div class="row g-2 mb-3 small text-muted">
                                <div class="col-6">
                                  <strong>Pickup:</strong> <?= htmlspecialchars($inq['pickup_location'] ?? 'None provided') ?>
                                </div>
                                <div class="col-6">
                                  <strong>Schedule:</strong> <?= htmlspecialchars($inq['preferred_schedule'] ?? 'Flexible') ?>
                                </div>
                              </div>
                            </div>
                            <div class="modal-footer bg-light">
                              <a href="mailto:<?= htmlspecialchars($inq['email']) ?>?subject=Re:%20Driving%20Lessons%20Inquiry%20-%20DriveMaster" class="btn btn-success btn-sm fw-bold">
                                <i class="bi bi-reply-fill me-1"></i> Reply via Email
                              </a>
                              <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
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
</body>
</html>
