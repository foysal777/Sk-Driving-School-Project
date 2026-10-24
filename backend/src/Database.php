<?php
declare(strict_types=1);

namespace DriveMaster;

use PDO;
use PDOException;
use PDOStatement;

/**
 * Core PHP 8+ Database Service
 * Singleton PDO Wrapper for MySQL with Prepared Statements
 */
final class Database
{
    private static ?self $instance = null;
    private ?PDO $pdo = null;
    private array $config;
    private ?string $lastError = null;

    private function __construct()
    {
        $this->config = require __DIR__ . '/../config/database.php';
        $this->connect();
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function connect(): void
    {
        $dsn = sprintf(
            '%s:host=%s;port=%d;dbname=%s;charset=%s',
            $this->config['driver'],
            $this->config['host'],
            $this->config['port'],
            $this->config['database'],
            $this->config['charset']
        );

        try {
            $this->pdo = new PDO(
                $dsn,
                $this->config['username'],
                $this->config['password'],
                $this->config['options']
            );
            $this->lastError = null;
        } catch (PDOException $e) {
            $this->pdo = null;
            $this->lastError = $e->getMessage();
            error_log("[DriveMaster DB Error] Failed connecting to MySQL: " . $e->getMessage());
        }
    }

    public function getConnection(): ?PDO
    {
        if ($this->pdo === null) {
            $this->connect();
        }
        return $this->pdo;
    }

    public function isAvailable(): bool
    {
        return $this->getConnection() !== null;
    }

    public function getLastError(): ?string
    {
        return $this->lastError;
    }

    public function query(string $sql, array $params = []): PDOStatement
    {
        $pdo = $this->getConnection();
        if ($pdo === null) {
            throw new PDOException("Database is currently unreachable. " . ($this->lastError ?? ''));
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }

    public function fetchOne(string $sql, array $params = []): ?array
    {
        $result = $this->query($sql, $params)->fetch();
        return $result === false ? null : $result;
    }

    public function execute(string $sql, array $params = []): bool
    {
        return $this->query($sql, $params)->rowCount() > 0;
    }

    public function lastInsertId(): string
    {
        $pdo = $this->getConnection();
        return $pdo ? $pdo->lastInsertId() : '0';
    }

    public function beginTransaction(): bool
    {
        return $this->getConnection()?->beginTransaction() ?? false;
    }

    public function commit(): bool
    {
        return $this->getConnection()?->commit() ?? false;
    }

    public function rollBack(): bool
    {
        return $this->getConnection()?->rollBack() ?? false;
    }
}
