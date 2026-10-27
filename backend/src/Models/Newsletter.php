<?php
declare(strict_types=1);

namespace DriveMaster\Models;

require_once __DIR__ . '/../Database.php';

use DriveMaster\Database;

/**
 * Newsletter Model - Manages email newsletter subscribers.
 * Primary storage is a JSON file store (works without MySQL); MySQL is mirrored when available.
 */
final class Newsletter
{
    private static function getStorageFile(): string
    {
        return __DIR__ . '/../../../database/newsletter_subscribers.json';
    }

    private static function readStore(): array
    {
        $file = self::getStorageFile();
        if (!file_exists($file)) {
            return [];
        }
        $data = json_decode((string)file_get_contents($file), true);
        return is_array($data) ? $data : [];
    }

    private static function writeStore(array $data): void
    {
        $file = self::getStorageFile();
        $dir = dirname($file);
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        file_put_contents(
            $file,
            json_encode(array_values($data), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            LOCK_EX
        );
    }

    public static function findByEmail(string $email): ?array
    {
        foreach (self::readStore() as $item) {
            if (strcasecmp($item['email'] ?? '', $email) === 0) {
                return $item;
            }
        }
        return null;
    }

    /**
     * Subscribe an email. Returns ['subscriber' => array, 'already' => bool].
     */
    public static function subscribe(string $email, string $source = 'website'): array
    {
        $email = strtolower(trim($email));

        $existing = self::findByEmail($email);
        if ($existing !== null) {
            return ['subscriber' => $existing, 'already' => true];
        }

        $record = [
            'id'         => time() . rand(10, 99),
            'email'      => $email,
            'source'     => substr(trim($source), 0, 60) ?: 'website',
            'status'     => 'subscribed',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $store = self::readStore();
        array_unshift($store, $record);
        self::writeStore($store);

        // Mirror to MySQL when available (silently skipped otherwise)
        $db = Database::getInstance();
        if ($db->isAvailable()) {
            try {
                $db->execute(
                    "CREATE TABLE IF NOT EXISTS newsletter_subscribers (
                        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                        email VARCHAR(254) NOT NULL UNIQUE,
                        source VARCHAR(60) DEFAULT 'website',
                        status VARCHAR(20) DEFAULT 'subscribed',
                        ip_address VARCHAR(45) NULL,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
                );
                $db->execute(
                    "INSERT IGNORE INTO newsletter_subscribers (email, source, ip_address) VALUES (:email, :source, :ip)",
                    [':email' => $email, ':source' => $record['source'], ':ip' => $record['ip_address']]
                );
            } catch (\Throwable $e) {
                // JSON store already holds the record
            }
        }

        return ['subscriber' => $record, 'already' => false];
    }

    public static function all(): array
    {
        return self::readStore();
    }

    public static function delete(string|int $id): bool
    {
        $idStr = (string)$id;
        $store = self::readStore();
        $initialCount = count($store);
        $filtered = array_filter($store, fn($item) => (string)($item['id'] ?? '') !== $idStr);
        if (count($filtered) !== $initialCount) {
            self::writeStore(array_values($filtered));
            return true;
        }
        return false;
    }

    public static function count(): int
    {
        return count(self::readStore());
    }
}
