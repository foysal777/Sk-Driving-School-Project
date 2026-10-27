<?php
declare(strict_types=1);

namespace DriveMaster\Models;

require_once __DIR__ . '/../Database.php';

use DriveMaster\Database;
use PDO;

/**
 * Inquiry Model - Manages contact form submissions & leads
 * Uses JSON file store for 100% persistent storage, mirrored to MySQL when available
 */
final class Inquiry
{
    private static function getStorageFile(): string
    {
        return __DIR__ . '/../../../database/inquiries_store.json';
    }

    private static function readStore(): array
    {
        $file = self::getStorageFile();
        if (!file_exists($file)) {
            $default = [
                [
                    'id' => 1,
                    'full_name' => 'Sophie Turner',
                    'email' => 'sophie.turner@example.com',
                    'phone' => '+66 12 444 8899',
                    'subject' => 'Automatic Lessons',
                    'pickup_location' => 'Central City, 10110',
                    'timeline' => 'As Soon As Possible',
                    'preferred_schedule' => 'Weekday Mornings',
                    'message' => 'Hello! I am a complete beginner nervous about driving. I would like to book a 15-hour automatic course with an experienced instructor.',
                    'status' => 'new',
                    'admin_notes' => null,
                    'created_at' => date('Y-m-d H:i:s', strtotime('-2 hours'))
                ],
                [
                    'id' => 2,
                    'full_name' => 'Daniel Craig',
                    'email' => 'daniel.c@example.com',
                    'phone' => '+66 12 777 2233',
                    'subject' => 'Intensive Fast-Track',
                    'pickup_location' => 'Riverside District, 10200',
                    'timeline' => 'Within 2 Weeks',
                    'preferred_schedule' => 'Weekends',
                    'message' => 'Looking for fast-track intensive lessons over the next two weekends. Need test car hire as well.',
                    'status' => 'contacted',
                    'admin_notes' => 'Called back, arranged consultation.',
                    'created_at' => date('Y-m-d H:i:s', strtotime('-1 day'))
                ],
                [
                    'id' => 3,
                    'full_name' => 'Emma Watson',
                    'email' => 'emma.w@example.com',
                    'phone' => '+66 12 555 1122',
                    'subject' => 'Standard Pass Course',
                    'pickup_location' => 'Westside Highway, 10300',
                    'timeline' => 'Next Month',
                    'preferred_schedule' => 'Weekday Evenings',
                    'message' => 'Hi, do you provide pickup from University campus for evening classes? Thanks!',
                    'status' => 'enrolled',
                    'admin_notes' => 'Enrolled in beginner package.',
                    'created_at' => date('Y-m-d H:i:s', strtotime('-3 days'))
                ]
            ];
            self::writeStore($default);
            return $default;
        }

        $raw = file_get_contents($file);
        $data = json_decode($raw, true);
        return is_array($data) ? $data : [];
    }

    private static function writeStore(array $data): void
    {
        $file = self::getStorageFile();
        $dir = dirname($file);
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        file_put_contents($file, json_encode(array_values($data), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), LOCK_EX);
    }

    public static function create(array $data): array
    {
        $id = time() + rand(100, 999);
        $record = [
            'id'                 => $id,
            'full_name'          => trim((string)($data['full_name'] ?? '')),
            'email'              => trim((string)($data['email'] ?? '')),
            'phone'              => trim((string)($data['phone'] ?? '')),
            'subject'            => trim((string)($data['subject'] ?? 'General Inquiry')),
            'pickup_location'    => trim((string)($data['pickup_location'] ?? '')),
            'timeline'           => trim((string)($data['timeline'] ?? '')),
            'preferred_schedule' => trim((string)($data['preferred_schedule'] ?? 'Flexible')),
            'message'            => trim((string)($data['message'] ?? '')),
            'status'             => 'new',
            'admin_notes'        => null,
            'ip_address'         => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'created_at'         => date('Y-m-d H:i:s')
        ];

        // 1. Save in JSON Store (ensures 100% data preservation)
        $store = self::readStore();
        array_unshift($store, $record);
        self::writeStore($store);

        // 2. Mirror into MySQL when available
        $db = Database::getInstance();
        if ($db->isAvailable()) {
            try {
                $sql = "INSERT INTO inquiries (
                            full_name, email, phone, subject, pickup_location, 
                            timeline, preferred_schedule, message, status, ip_address
                        ) VALUES (
                            :full_name, :email, :phone, :subject, :pickup_location,
                            :timeline, :preferred_schedule, :message, 'new', :ip_address
                        )";
                $db->query($sql, [
                    ':full_name'          => $record['full_name'],
                    ':email'              => $record['email'],
                    ':phone'              => $record['phone'],
                    ':subject'            => $record['subject'],
                    ':pickup_location'    => $record['pickup_location'],
                    ':timeline'           => $record['timeline'],
                    ':preferred_schedule' => $record['preferred_schedule'],
                    ':message'            => $record['message'],
                    ':ip_address'         => $record['ip_address'],
                ]);
            } catch (\Throwable $e) {}
        }

        return $record;
    }

    public static function find(int $id): ?array
    {
        $store = self::readStore();
        foreach ($store as $item) {
            if ((int)($item['id'] ?? 0) === $id) {
                return $item;
            }
        }
        return null;
    }

    public static function all(array $filters = []): array
    {
        $items = self::readStore();

        $status = $filters['status'] ?? 'all';
        $search = strtolower(trim((string)($filters['search'] ?? '')));

        if ($status !== 'all' && !empty($status)) {
            $items = array_filter($items, fn($i) => ($i['status'] ?? '') === $status);
        }

        if (!empty($search)) {
            $items = array_filter($items, function($i) use ($search) {
                $haystack = strtolower(($i['full_name'] ?? '') . ' ' . ($i['email'] ?? '') . ' ' . ($i['phone'] ?? '') . ' ' . ($i['subject'] ?? '') . ' ' . ($i['message'] ?? ''));
                return str_contains($haystack, $search);
            });
        }

        return array_values($items);
    }

    public static function updateStatus(int $id, string $status, ?string $adminNotes = null): bool
    {
        $store = self::readStore();
        $updated = false;
        foreach ($store as &$item) {
            if ((int)($item['id'] ?? 0) === $id) {
                $item['status'] = $status;
                if ($adminNotes !== null) {
                    $item['admin_notes'] = $adminNotes;
                }
                $updated = true;
                break;
            }
        }
        if ($updated) {
            self::writeStore($store);
        }

        $db = Database::getInstance();
        if ($db->isAvailable()) {
            try {
                $db->execute("UPDATE inquiries SET status = :status, admin_notes = COALESCE(:notes, admin_notes) WHERE id = :id", [
                    ':id'     => $id,
                    ':status' => $status,
                    ':notes'  => $adminNotes
                ]);
            } catch (\Throwable $e) {}
        }

        return $updated;
    }

    public static function delete(int $id): bool
    {
        $store = self::readStore();
        $originalCount = count($store);
        $store = array_filter($store, fn($i) => (int)($i['id'] ?? 0) !== $id);

        if (count($store) !== $originalCount) {
            self::writeStore($store);
        }

        $db = Database::getInstance();
        if ($db->isAvailable()) {
            try {
                $db->execute("DELETE FROM inquiries WHERE id = :id", [':id' => $id]);
            } catch (\Throwable $e) {}
        }

        return true;
    }

    public static function countByStatus(): array
    {
        $items = self::readStore();
        $counts = ['new' => 0, 'contacted' => 0, 'in_progress' => 0, 'enrolled' => 0, 'closed' => 0, 'total' => 0];

        foreach ($items as $item) {
            $st = $item['status'] ?? 'new';
            if (isset($counts[$st])) {
                $counts[$st]++;
            }
            $counts['total']++;
        }

        return $counts;
    }
}
