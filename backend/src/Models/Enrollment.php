<?php
declare(strict_types=1);

namespace DriveMaster\Models;

use DriveMaster\Database;

/**
 * Enrollment Model - Manages driving lesson course bookings
 */
final class Enrollment
{
    public static function create(array $data): array
    {
        $db = Database::getInstance();
        $ref = 'DM-' . date('Y') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));

        if (!$db->isAvailable()) {
            return [
                'id' => rand(100, 999),
                'booking_reference' => $ref,
                'student_name' => $data['student_name'] ?? '',
                'student_email' => $data['student_email'] ?? '',
                'course_name' => $data['course_name'] ?? 'Custom Package',
                'course_price' => (float)($data['course_price'] ?? 420.00),
                'status' => 'pending',
                'created_at' => date('Y-m-d H:i:s')
            ];
        }

        $sql = "INSERT INTO enrollments (
                    booking_reference, student_name, student_email, student_phone,
                    course_id, course_name, course_price, pickup_address,
                    preferred_start_date, preferred_transmission, payment_status, status
                ) VALUES (
                    :ref, :name, :email, :phone,
                    :course_id, :course_name, :course_price, :pickup,
                    :start_date, :transmission, 'unpaid', 'pending'
                )";

        $params = [
            ':ref'          => $ref,
            ':name'         => trim((string)($data['student_name'] ?? '')),
            ':email'        => trim((string)($data['student_email'] ?? '')),
            ':phone'        => trim((string)($data['student_phone'] ?? '')),
            ':course_id'    => !empty($data['course_id']) ? (int)$data['course_id'] : null,
            ':course_name'  => trim((string)($data['course_name'] ?? 'Driving Course')),
            ':course_price' => (float)($data['course_price'] ?? 0.00),
            ':pickup'       => trim((string)($data['pickup_address'] ?? '')),
            ':start_date'   => !empty($data['preferred_start_date']) ? $data['preferred_start_date'] : null,
            ':transmission' => in_array($data['preferred_transmission'] ?? '', ['automatic', 'manual'], true) ? $data['preferred_transmission'] : 'automatic',
        ];

        $db->query($sql, $params);
        $id = (int)$db->lastInsertId();

        return self::find($id) ?? array_merge(['id' => $id, 'booking_reference' => $ref], $data);
    }

    public static function find(int $id): ?array
    {
        $db = Database::getInstance();
        if (!$db->isAvailable()) {
            return null;
        }

        return $db->fetchOne("SELECT * FROM enrollments WHERE id = :id LIMIT 1", [':id' => $id]);
    }

    public static function all(array $filters = []): array
    {
        $db = Database::getInstance();
        if (!$db->isAvailable()) {
            return [
                [
                    'id' => 1,
                    'booking_reference' => 'DM-2026-8901',
                    'student_name' => 'Sophie Turner',
                    'student_email' => 'sophie.turner@example.com',
                    'student_phone' => '+66 12 444 8899',
                    'course_name' => 'Automatic Only Specialist',
                    'course_price' => 630.00,
                    'preferred_transmission' => 'automatic',
                    'payment_status' => 'deposit_paid',
                    'status' => 'confirmed',
                    'created_at' => date('Y-m-d H:i:s', strtotime('-1 day'))
                ],
                [
                    'id' => 2,
                    'booking_reference' => 'DM-2026-8902',
                    'student_name' => 'Liam Neeson',
                    'student_email' => 'liam.n@example.com',
                    'student_phone' => '+66 12 666 4321',
                    'course_name' => 'Standard Pass Course',
                    'course_price' => 780.00,
                    'preferred_transmission' => 'manual',
                    'payment_status' => 'paid_in_full',
                    'status' => 'in_training',
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

        if (!empty($filters['payment_status']) && $filters['payment_status'] !== 'all') {
            $conditions[] = "payment_status = :payment";
            $params[':payment'] = $filters['payment_status'];
        }

        if (!empty($filters['search'])) {
            $conditions[] = "(student_name LIKE :search OR student_email LIKE :search OR booking_reference LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';
        $sql = "SELECT * FROM enrollments {$whereClause} ORDER BY created_at DESC LIMIT 100";

        return $db->fetchAll($sql, $params);
    }

    public static function updateStatus(int $id, string $status, ?string $paymentStatus = null): bool
    {
        $db = Database::getInstance();
        if (!$db->isAvailable()) {
            return true;
        }

        $sql = "UPDATE enrollments 
                SET status = :status, 
                    payment_status = COALESCE(:payment, payment_status) 
                WHERE id = :id";

        return $db->execute($sql, [
            ':id'      => $id,
            ':status'  => $status,
            ':payment' => $paymentStatus
        ]);
    }

    public static function countStats(): array
    {
        $db = Database::getInstance();
        if (!$db->isAvailable()) {
            return [
                'total_bookings' => 2,
                'confirmed'      => 1,
                'pending'        => 1,
                'revenue'        => 1410.00
            ];
        }

        $stats = $db->fetchOne("SELECT 
                                    COUNT(*) as total_bookings,
                                    SUM(CASE WHEN status = 'confirmed' OR status = 'in_training' THEN 1 ELSE 0 END) as confirmed,
                                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                                    COALESCE(SUM(course_price), 0) as revenue
                                FROM enrollments");

        return [
            'total_bookings' => (int)($stats['total_bookings'] ?? 0),
            'confirmed'      => (int)($stats['confirmed'] ?? 0),
            'pending'        => (int)($stats['pending'] ?? 0),
            'revenue'        => (float)($stats['revenue'] ?? 0.0),
        ];
    }
}
