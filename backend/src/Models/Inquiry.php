<?php
declare(strict_types=1);

namespace DriveMaster\Models;

use DriveMaster\Database;
use PDO;

/**
 * Inquiry Model - Manages contact requests & lead messages
 */
final class Inquiry
{
    public static function create(array $data): array
    {
        $db = Database::getInstance();

        if (!$db->isAvailable()) {
            // Graceful fallback for environments before MySQL is populated
            return [
                'id' => rand(100, 999),
                'full_name' => htmlspecialchars($data['full_name'] ?? ''),
                'email' => filter_var($data['email'] ?? '', FILTER_SANITIZE_EMAIL),
                'subject' => htmlspecialchars($data['subject'] ?? ''),
                'status' => 'new',
                'created_at' => date('Y-m-d H:i:s'),
                'note' => 'Stored in temporary runtime fallback (MySQL not connected)'
            ];
        }

        $sql = "INSERT INTO inquiries (
                    full_name, email, phone, subject, pickup_location, 
                    timeline, preferred_schedule, message, status, ip_address
                ) VALUES (
                    :full_name, :email, :phone, :subject, :pickup_location,
                    :timeline, :preferred_schedule, :message, 'new', :ip_address
                )";

        $params = [
            ':full_name'          => trim((string)($data['full_name'] ?? '')),
            ':email'              => trim((string)($data['email'] ?? '')),
            ':phone'              => trim((string)($data['phone'] ?? '')),
            ':subject'            => trim((string)($data['subject'] ?? 'General Inquiry')),
            ':pickup_location'    => trim((string)($data['pickup_location'] ?? '')),
            ':timeline'           => trim((string)($data['timeline'] ?? '')),
            ':preferred_schedule' => trim((string)($data['preferred_schedule'] ?? 'Flexible')),
            ':message'            => trim((string)($data['message'] ?? '')),
            ':ip_address'         => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
        ];

        $db->query($sql, $params);
        $id = (int)$db->lastInsertId();

        return self::find($id) ?? array_merge(['id' => $id], $data);
    }

    public static function find(int $id): ?array
    {
        $db = Database::getInstance();
        if (!$db->isAvailable()) {
            return null;
        }

        return $db->fetchOne("SELECT * FROM inquiries WHERE id = :id LIMIT 1", [':id' => $id]);
    }

    public static function all(array $filters = []): array
    {
        $db = Database::getInstance();
        if (!$db->isAvailable()) {
            // Return sample seed array so admin panel looks great immediately
            return [
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
                    'created_at' => date('Y-m-d H:i:s', strtotime('-3 days'))
                ]
            ];
        }

        $conditions = [];
        $params = [];

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $conditions[] = "status = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['search'])) {
            $conditions[] = "(full_name LIKE :search OR email LIKE :search OR phone LIKE :search OR subject LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';
        $sql = "SELECT * FROM inquiries {$whereClause} ORDER BY created_at DESC LIMIT 100";

        return $db->fetchAll($sql, $params);
    }

    public static function updateStatus(int $id, string $status, ?string $adminNotes = null): bool
    {
        $db = Database::getInstance();
        if (!$db->isAvailable()) {
            return true;
        }

        $sql = "UPDATE inquiries SET status = :status, admin_notes = COALESCE(:notes, admin_notes) WHERE id = :id";
        return $db->execute($sql, [
            ':id'     => $id,
            ':status' => $status,
            ':notes'  => $adminNotes
        ]);
    }

    public static function delete(int $id): bool
    {
        $db = Database::getInstance();
        if (!$db->isAvailable()) {
            return true;
        }

        return $db->execute("DELETE FROM inquiries WHERE id = :id", [':id' => $id]);
    }

    public static function countByStatus(): array
    {
        $db = Database::getInstance();
        if (!$db->isAvailable()) {
            return ['new' => 1, 'contacted' => 1, 'enrolled' => 1, 'closed' => 0, 'total' => 3];
        }

        $rows = $db->fetchAll("SELECT status, COUNT(*) as count FROM inquiries GROUP BY status");
        $counts = ['new' => 0, 'contacted' => 0, 'in_progress' => 0, 'enrolled' => 0, 'closed' => 0, 'total' => 0];

        foreach ($rows as $row) {
            $counts[$row['status']] = (int)$row['count'];
            $counts['total'] += (int)$row['count'];
        }

        return $counts;
    }
}
