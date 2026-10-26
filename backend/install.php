<?php
declare(strict_types=1);

/**
 * DriveMaster Driving School - One-Click MySQL Database Installer
 * Pure PHP 8+ Core Implementation
 */

$configFile = __DIR__ . '/config/database.php';
$config = require $configFile;

$message = null;
$messageType = null;
$executedStatements = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $host = trim($_POST['host'] ?? $config['host']);
    $port = (int)($_POST['port'] ?? $config['port']);
    $dbname = trim($_POST['database'] ?? $config['database']);
    $user = trim($_POST['username'] ?? $config['username']);
    $pass = $_POST['password'] ?? $config['password'];

    try {
        // Step 1: Connect to MySQL server (without selecting DB first)
        $pdo = new PDO(
            "mysql:host={$host};port={$port};charset=utf8mb4",
            $user,
            $pass,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ]
        );

        // Step 2: Create database if not exists
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbname}` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `{$dbname}`");

        // Step 3: Run Schema SQL
        $schemaSql = file_get_contents(__DIR__ . '/../database/schema.sql');
        $statements = array_filter(array_map('trim', explode(';', $schemaSql)));

        foreach ($statements as $stmt) {
            if (!empty($stmt) && !str_starts_with($stmt, '--') && !str_starts_with($stmt, '/*')) {
                $pdo->exec($stmt);
                $executedStatements++;
            }
        }

        // Step 4: Run Seed SQL
        $seedSql = file_get_contents(__DIR__ . '/../database/seed.sql');
        $seedStatements = array_filter(array_map('trim', explode(';', $seedSql)));

        foreach ($seedStatements as $stmt) {
            if (!empty($stmt) && !str_starts_with($stmt, '--') && !str_starts_with($stmt, '/*')) {
                $pdo->exec($stmt);
                $executedStatements++;
            }
        }

        // Step 5: Save updated credentials into config/database.php
        $newConfigContent = "<?php\ndeclare(strict_types=1);\n\nreturn [\n"
            . "    'driver'    => 'mysql',\n"
            . "    'host'      => '{$host}',\n"
            . "    'port'      => {$port},\n"
            . "    'database'  => '{$dbname}',\n"
            . "    'username'  => '{$user}',\n"
            . "    'password'  => '{$pass}',\n"
            . "    'charset'   => 'utf8mb4',\n"
            . "    'collation' => 'utf8mb4_unicode_ci',\n"
            . "    'options'   => [\n"
            . "        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,\n"
            . "        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,\n"
            . "        PDO::ATTR_EMULATE_PREPARES   => false,\n"
            . "        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci',\n"
            . "    ],\n];\n";

        file_put_contents($configFile, $newConfigContent);

        $message = "Database '{$dbname}' successfully initialized with {$executedStatements} queries executed! Default admin and courses seeded.";
        $messageType = "success";
    } catch (PDOException $e) {
        $message = "Database Setup Error: " . $e->getMessage();
        $messageType = "danger";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Database Setup &amp; Migration | DriveMaster</title>
  <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🛠️</text></svg>">
  <link rel="stylesheet" href="../assets/vendor/fonts/fonts.css">
  <link rel="stylesheet" href="../assets/vendor/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/vendor/bootstrap-icons/bootstrap-icons.min.css">
  <style>
    body {
      background: #f1f5f9;
      font-family: 'Plus Jakarta Sans', sans-serif;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem 1rem;
    }
    .installer-card {
      background: #ffffff;
      border-radius: 20px;
      box-shadow: 0 20px 40px rgba(15, 23, 42, 0.08);
      border: 1px solid #e2e8f0;
      max-width: 650px;
      width: 100%;
      overflow: hidden;
    }
    .installer-header {
      background: linear-gradient(135deg, #133a30 0%, #1e4d40 100%);
      color: #ffffff;
      padding: 2rem;
      text-align: center;
    }
    .installer-body {
      padding: 2.25rem;
    }
  </style>
</head>
<body>

  <div class="installer-card">
    <div class="installer-header">
      <div class="display-6 mb-2">🚗</div>
      <h2 class="fw-bold mb-1" style="font-family: 'Space Grotesk', sans-serif;">DriveMaster Database Setup</h2>
      <p class="mb-0 text-white-50 small">Automated MySQL Schema &amp; Seed Installer (Core PHP 8+)</p>
    </div>

    <div class="installer-body">
      <?php if ($message): ?>
        <div class="alert alert-<?= $messageType ?> d-flex align-items-center mb-4" role="alert">
          <i class="bi bi-<?= $messageType === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill' ?> fs-4 me-2"></i>
          <div><?= htmlspecialchars($message) ?></div>
        </div>

        <?php if ($messageType === 'success'): ?>
          <div class="card bg-light border-0 mb-4 p-3">
            <h6 class="fw-bold text-success mb-2"><i class="bi bi-shield-check me-1"></i> Admin Panel Credentials:</h6>
            <div class="small text-muted mb-1"><strong>Email:</strong> admin@drivemaster.com</div>
            <div class="small text-muted mb-3"><strong>Password:</strong> Admin@123456</div>
            <div class="d-flex gap-2">
              <a href="/admin/login.php" class="btn btn-success btn-sm fw-bold">
                <i class="bi bi-box-arrow-in-right me-1"></i> Go to Admin Login
              </a>
              <a href="/backend/swagger/" class="btn btn-outline-dark btn-sm fw-bold">
                <i class="bi bi-code-slash me-1"></i> Open Swagger UI
              </a>
            </div>
          </div>
        <?php endif; ?>
      <?php endif; ?>

      <form method="POST">
        <h5 class="fw-bold mb-3 text-dark">MySQL Server Connection</h5>
        <div class="row g-3 mb-3">
          <div class="col-md-8">
            <label class="form-label small fw-bold">Database Host</label>
            <input type="text" name="host" class="form-control" value="<?= htmlspecialchars($config['host']) ?>" required>
          </div>
          <div class="col-md-4">
            <label class="form-label small fw-bold">Port</label>
            <input type="number" name="port" class="form-control" value="<?= htmlspecialchars((string)$config['port']) ?>" required>
          </div>
          <div class="col-12">
            <label class="form-label small fw-bold">Database Name</label>
            <input type="text" name="database" class="form-control" value="<?= htmlspecialchars($config['database']) ?>" required>
            <div class="form-text">Will be created automatically if it does not exist.</div>
          </div>
          <div class="col-md-6">
            <label class="form-label small fw-bold">MySQL Username</label>
            <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($config['username']) ?>" required>
          </div>
          <div class="col-md-6">
            <label class="form-label small fw-bold">MySQL Password</label>
            <input type="password" name="password" class="form-control" placeholder="Leave blank if none" value="<?= htmlspecialchars($config['password']) ?>">
          </div>
        </div>

        <div class="d-grid gap-2">
          <button type="submit" class="btn btn-success fw-bold py-2">
            <i class="bi bi-database-check me-1"></i> Run Schema &amp; Seed Database
          </button>
          <a href="/admin/" class="btn btn-light border py-2 small fw-semibold text-muted text-center">
            Already installed? Go to Admin Dashboard &rarr;
          </a>
        </div>
      </form>
    </div>
  </div>

</body>
</html>
