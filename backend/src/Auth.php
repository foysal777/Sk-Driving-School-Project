<?php
declare(strict_types=1);

namespace DriveMaster;

/**
 * Authentication and Session Guard for DriveMaster Admin
 * Core PHP 8+ Implementation
 */
final class Auth
{
    private static bool $sessionStarted = false;

    public static function startSession(): void
    {
        if (self::$sessionStarted || session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        $appConfig = require __DIR__ . '/../config/app.php';
        ini_set('session.name', $appConfig['session_name']);
        ini_set('session.cookie_httponly', '1');
        ini_set('session.cookie_samesite', 'Lax');
        ini_set('session.gc_maxlifetime', (string)$appConfig['session_lifetime']);

        session_start();
        self::$sessionStarted = true;
    }

    public static function login(string $usernameOrEmail, string $password): ?array
    {
        self::startSession();
        $db = Database::getInstance();

        if (!$db->isAvailable()) {
            // Fallback development bypass if MySQL is not yet configured
            if (($usernameOrEmail === 'admin' || $usernameOrEmail === 'admin@drivemaster.com') && $password === 'Admin@123456') {
                $fakeAdmin = [
                    'id'        => 1,
                    'username'  => 'admin',
                    'email'     => 'admin@drivemaster.com',
                    'full_name' => 'Master Instructor Director (Dev Mode)',
                    'role'      => 'super_admin',
                    'token'     => bin2hex(random_bytes(24)),
                ];
                $_SESSION['admin_user'] = $fakeAdmin;
                return $fakeAdmin;
            }
            return null;
        }

        $sql = "SELECT id, username, email, password_hash, full_name, role, status 
                FROM admins 
                WHERE (username = :u OR email = :e) AND status = 'active' 
                LIMIT 1";

        $admin = $db->fetchOne($sql, [':u' => $usernameOrEmail, ':e' => $usernameOrEmail]);

        if (!$admin || !password_verify($password, $admin['password_hash'])) {
            return null;
        }

        // Update last login
        $db->query("UPDATE admins SET last_login_at = NOW() WHERE id = :id", [':id' => $admin['id']]);

        // Secure token for API / Header authorization
        $token = bin2hex(random_bytes(24));
        unset($admin['password_hash']);
        $admin['token'] = $token;

        $_SESSION['admin_user'] = $admin;
        return $admin;
    }

    public static function user(): ?array
    {
        self::startSession();

        // 1. Check session
        if (isset($_SESSION['admin_user'])) {
            return $_SESSION['admin_user'];
        }

        // 2. Check Bearer Token header
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? null;

        if ($authHeader && preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            $token = trim($matches[1]);
            if (isset($_SESSION['admin_user']) && ($_SESSION['admin_user']['token'] ?? '') === $token) {
                return $_SESSION['admin_user'];
            }
        }

        return null;
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function requireAuth(): array
    {
        $user = self::user();
        if ($user === null) {
            if (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json')) {
                Response::unauthorized('Authentication required. Please login.');
            }
            header('Location: /admin/login.php?error=session_expired');
            exit();
        }
        return $user;
    }

    public static function logout(): void
    {
        self::startSession();
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }
}
