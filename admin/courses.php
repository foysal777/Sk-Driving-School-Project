<?php
declare(strict_types=1);

require_once __DIR__ . '/../backend/src/Database.php';
require_once __DIR__ . '/../backend/src/Auth.php';
require_once __DIR__ . '/../backend/src/Models/Course.php';

use DriveMaster\Auth;
use DriveMaster\Models\Course;

$admin = Auth::requireAuth();

$alert = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        Course::create($_POST);
        $alert = ['type' => 'success', 'msg' => 'New driving package added successfully.'];
    } elseif ($action === 'update') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            Course::update($id, $_POST);
            $alert = ['type' => 'success', 'msg' => "Course #{$id} updated successfully."];
        }
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            Course::delete($id);
            $alert = ['type' => 'success', 'msg' => "Course #{$id} has been deleted."];
        }
    }
}

$courses = Course::all('all');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Courses &amp; Rates | DriveMaster Admin</title>
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
      <li><a href="/admin/enrollments.php" class="sidebar-nav-link"><i class="bi bi-calendar-check-fill"></i> Course Bookings</a></li>
      <li><a href="/admin/courses.php" class="sidebar-nav-link active"><i class="bi bi-car-front-fill"></i> Courses &amp; Rates</a></li>
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
      <h4 class="mb-0 fw-bold" style="font-family: 'Space Grotesk', sans-serif;">Courses &amp; Pricing Packages</h4>
      <button type="button" class="btn btn-success btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#newCourseModal">
        <i class="bi bi-plus-lg me-1"></i> Add New Package
      </button>
    </header>

    <div class="p-4 flex-grow-1">
      <?php if ($alert): ?>
        <div class="alert alert-<?= $alert['type'] ?> alert-dismissible fade show mb-4" role="alert">
          <i class="bi bi-check-circle-fill me-2"></i> <?= htmlspecialchars($alert['msg']) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>

      <!-- Courses Grid -->
      <div class="row g-4">
        <?php foreach ($courses as $c): ?>
          <div class="col-md-6 col-xl-4">
            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden position-relative">
              <?php if (!empty($c['is_featured'])): ?>
                <span class="badge bg-success position-absolute top-0 end-0 m-3 rounded-pill">Featured Package</span>
              <?php endif; ?>
              <div class="card-body p-4 d-flex flex-direction-column flex-column">
                <span class="badge bg-light text-dark border align-self-start mb-2 text-capitalize">
                  <?= htmlspecialchars($c['category']) ?>
                </span>
                <h4 class="fw-bold mb-2"><?= htmlspecialchars($c['title']) ?></h4>
                <p class="text-muted small mb-3"><?= htmlspecialchars($c['short_description']) ?></p>

                <div class="bg-light p-3 rounded-3 mb-3 d-flex justify-content-between align-items-center">
                  <div>
                    <span class="display-6 fw-bold text-success">$<?= number_format((float)$c['total_price'], 0) ?></span>
                    <span class="small text-muted d-block"><?= $c['duration_hours'] ?> Hours Instruction</span>
                  </div>
                  <span class="badge bg-white text-dark border px-2 py-1">$<?= number_format((float)$c['hourly_rate'], 0) ?>/hr</span>
                </div>

                <div class="mt-auto d-flex justify-content-between pt-2 border-top">
                  <span class="small text-muted">Status: <strong class="text-success"><?= ucfirst($c['status']) ?></strong></span>
                  <div class="d-flex gap-1">
                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal<?= $c['id'] ?>">
                      <i class="bi bi-pencil"></i> Edit
                    </button>
                    <form method="POST" class="d-inline" onsubmit="return confirm('Delete this course?');">
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="id" value="<?= $c['id'] ?>">
                      <button type="submit" class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-trash"></i>
                      </button>
                    </form>
                  </div>
                </div>
              </div>
            </div>

            <!-- Edit Modal -->
            <div class="modal fade" id="editModal<?= $c['id'] ?>" tabindex="-1">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                  <form method="POST">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?= $c['id'] ?>">
                    <div class="modal-header bg-light">
                      <h5 class="modal-title fw-bold">Edit Course: <?= htmlspecialchars($c['title']) ?></h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                      <div class="mb-3">
                        <label class="form-label small fw-bold">Course Title</label>
                        <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($c['title']) ?>" required>
                      </div>
                      <div class="row g-2 mb-3">
                        <div class="col-6">
                          <label class="form-label small fw-bold">Category</label>
                          <select name="category" class="form-select">
                            <option value="beginner" <?= $c['category'] === 'beginner' ? 'selected' : '' ?>>Beginner</option>
                            <option value="popular" <?= str_contains($c['category'], 'popular') ? 'selected' : '' ?>>Popular</option>
                            <option value="intensive" <?= $c['category'] === 'intensive' ? 'selected' : '' ?>>Intensive</option>
                            <option value="automatic" <?= $c['category'] === 'automatic' ? 'selected' : '' ?>>Automatic</option>
                            <option value="advanced" <?= $c['category'] === 'advanced' ? 'selected' : '' ?>>Advanced</option>
                          </select>
                        </div>
                        <div class="col-6">
                          <label class="form-label small fw-bold">Duration (Hours)</label>
                          <input type="number" name="duration_hours" class="form-control" value="<?= $c['duration_hours'] ?>" required>
                        </div>
                      </div>
                      <div class="row g-2 mb-3">
                        <div class="col-6">
                          <label class="form-label small fw-bold">Total Price ($)</label>
                          <input type="number" step="0.01" name="total_price" class="form-control" value="<?= $c['total_price'] ?>" required>
                        </div>
                        <div class="col-6">
                          <label class="form-label small fw-bold">Hourly Rate ($)</label>
                          <input type="number" step="0.01" name="hourly_rate" class="form-control" value="<?= $c['hourly_rate'] ?>" required>
                        </div>
                      </div>
                      <div class="mb-3">
                        <label class="form-label small fw-bold">Short Description</label>
                        <textarea name="short_description" class="form-control" rows="2"><?= htmlspecialchars($c['short_description']) ?></textarea>
                      </div>
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="feat<?= $c['id'] ?>" <?= !empty($c['is_featured']) ? 'checked' : '' ?>>
                        <label class="form-check-label small" for="feat<?= $c['id'] ?>">
                          Mark as Featured / Most Popular Package
                        </label>
                      </div>
                    </div>
                    <div class="modal-footer bg-light">
                      <button type="submit" class="btn btn-success btn-sm fw-bold">Save Changes</button>
                      <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <!-- New Course Modal -->
  <div class="modal fade" id="newCourseModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow">
        <form method="POST">
          <input type="hidden" name="action" value="create">
          <div class="modal-header bg-light">
            <h5 class="modal-title fw-bold">Add New Driving Course</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-4">
            <div class="mb-3">
              <label class="form-label small fw-bold">Course Title</label>
              <input type="text" name="title" class="form-control" placeholder="e.g. Pass Plus Motorway Master" required>
            </div>
            <div class="row g-2 mb-3">
              <div class="col-6">
                <label class="form-label small fw-bold">Category</label>
                <select name="category" class="form-select">
                  <option value="beginner">Beginner</option>
                  <option value="popular">Popular</option>
                  <option value="intensive">Intensive</option>
                  <option value="automatic">Automatic</option>
                  <option value="advanced">Advanced</option>
                </select>
              </div>
              <div class="col-6">
                <label class="form-label small fw-bold">Duration (Hours)</label>
                <input type="number" name="duration_hours" class="form-control" value="10" required>
              </div>
            </div>
            <div class="row g-2 mb-3">
              <div class="col-6">
                <label class="form-label small fw-bold">Total Price ($)</label>
                <input type="number" step="0.01" name="total_price" class="form-control" value="420" required>
              </div>
              <div class="col-6">
                <label class="form-label small fw-bold">Hourly Rate ($)</label>
                <input type="number" step="0.01" name="hourly_rate" class="form-control" value="42" required>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label small fw-bold">Short Description</label>
              <textarea name="short_description" class="form-control" rows="2" placeholder="Brief outline of syllabus goals..."></textarea>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="newFeat">
              <label class="form-check-label small" for="newFeat">
                Mark as Featured / Most Popular Package
              </label>
            </div>
          </div>
          <div class="modal-footer bg-light">
            <button type="submit" class="btn btn-success btn-sm fw-bold">Create Package</button>
            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
