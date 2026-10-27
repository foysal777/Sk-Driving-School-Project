<?php
declare(strict_types=1);

namespace DriveMaster\Models;

require_once __DIR__ . '/../Database.php';
require_once __DIR__ . '/../Services/SquareService.php';

use DriveMaster\Database;
use DriveMaster\Services\SquareService;

/**
 * Enrollment Model - Manages driving lesson course bookings & Square Dummy Payment Gateway
 */
final class Enrollment
{
    private static function getStorageFile(): string
    {
        return __DIR__ . '/../../../database/enrollments_store.json';
    }

    private static function readStore(): array
    {
        $file = self::getStorageFile();
        if (!file_exists($file)) {
            $default = [
                [
                    'id' => 1,
                    'booking_reference' => 'DM-2026-8901',
                    'student_name' => 'Sophie Turner',
                    'student_email' => 'sophie.turner@example.com',
                    'student_phone' => '+66 12 444 8899',
                    'course_name' => 'Automatic Only Specialist',
                    'course_price' => 630.00,
                    'preferred_transmission' => 'automatic',
                    'payment_gateway' => 'square',
                    'payment_status' => 'paid_in_full',
                    'payment_tx_id' => 'sq_tx_live_89104712a',
                    'card_last4' => '4242',
                    'status' => 'confirmed',
                    'admin_notes' => 'Square payment completed.',
                    'created_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
                    'updated_at' => date('Y-m-d H:i:s', strtotime('-1 day'))
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
                    'payment_gateway' => 'square',
                    'payment_status' => 'paid_in_full',
                    'payment_tx_id' => 'sq_tx_live_73891024b',
                    'card_last4' => '5555',
                    'status' => 'in_training',
                    'admin_notes' => 'Square payment confirmed.',
                    'created_at' => date('Y-m-d H:i:s', strtotime('-3 days')),
                    'updated_at' => date('Y-m-d H:i:s', strtotime('-3 days'))
                ]
            ];
            file_put_contents($file, json_encode($default, JSON_PRETTY_PRINT));
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
            mkdir($dir, 0777, true);
        }
        file_put_contents($file, json_encode(array_values($data), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    public static function create(array $data): array
    {
        $db = Database::getInstance();
        $refPrefix = !empty($data['payment_gateway']) && $data['payment_gateway'] === 'square' ? 'DM-SQ-' : 'DM-' . date('Y') . '-';
        $ref = $refPrefix . strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));

        $record = [
            'id' => time() + rand(10, 99),
            'booking_reference' => $ref,
            'student_name' => trim((string)($data['student_name'] ?? '')),
            'student_email' => trim((string)($data['student_email'] ?? '')),
            'student_phone' => trim((string)($data['student_phone'] ?? '')),
            'course_id' => !empty($data['course_id']) ? (int)$data['course_id'] : null,
            'course_name' => trim((string)($data['course_name'] ?? 'Driving Course')),
            'course_price' => (float)($data['course_price'] ?? 0.00),
            'pickup_address' => trim((string)($data['pickup_address'] ?? '')),
            'preferred_start_date' => !empty($data['preferred_start_date']) ? $data['preferred_start_date'] : null,
            'preferred_transmission' => in_array($data['preferred_transmission'] ?? '', ['automatic', 'manual'], true) ? $data['preferred_transmission'] : 'automatic',
            'payment_gateway' => $data['payment_gateway'] ?? 'square',
            'payment_status' => $data['payment_status'] ?? 'unpaid',
            'payment_tx_id' => $data['payment_tx_id'] ?? null,
            'card_last4' => $data['card_last4'] ?? null,
            'status' => $data['status'] ?? 'pending',
            'admin_notes' => $data['admin_notes'] ?? null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Save in File Store
        $store = self::readStore();
        array_unshift($store, $record);
        self::writeStore($store);

        // Try MySQL if available
        if ($db->isAvailable()) {
            try {
                $sql = "INSERT INTO enrollments (
                            booking_reference, student_name, student_email, student_phone,
                            course_id, course_name, course_price, pickup_address,
                            preferred_start_date, preferred_transmission, payment_gateway,
                            payment_status, payment_tx_id, card_last4, status, admin_notes
                        ) VALUES (
                            :ref, :name, :email, :phone,
                            :course_id, :course_name, :course_price, :pickup,
                            :start_date, :transmission, :gateway,
                            :payment_status, :tx_id, :last4, :status, :notes
                        )";
                $db->query($sql, [
                    ':ref'            => $ref,
                    ':name'           => $record['student_name'],
                    ':email'          => $record['student_email'],
                    ':phone'          => $record['student_phone'],
                    ':course_id'      => $record['course_id'],
                    ':course_name'    => $record['course_name'],
                    ':course_price'   => $record['course_price'],
                    ':pickup'         => $record['pickup_address'],
                    ':start_date'     => $record['preferred_start_date'],
                    ':transmission'   => $record['preferred_transmission'],
                    ':gateway'        => $record['payment_gateway'],
                    ':payment_status' => $record['payment_status'],
                    ':tx_id'          => $record['payment_tx_id'],
                    ':last4'          => $record['card_last4'],
                    ':status'         => $record['status'],
                    ':notes'          => $record['admin_notes']
                ]);
                $sqlId = (int)$db->lastInsertId();
                if ($sqlId > 0) {
                    $record['id'] = $sqlId;
                }
            } catch (\Exception $e) {
                error_log("[Enrollment MySQL Error] " . $e->getMessage());
            }
        }

        return $record;
    }

    /**
     * Stage 1: Student requests to pay via Square -> Status becomes payment_requested (waiting admin approval)
     */
    public static function requestSquarePayment(array $data): array
    {
        $data['payment_gateway'] = 'square';
        $data['payment_status'] = 'payment_requested';
        $data['status'] = 'pending';
        $data['admin_notes'] = 'Student initiated Square dummy payment. Awaiting Admin Approval.';

        return self::create($data);
    }

    public static function find(int $id): ?array
    {
        $db = Database::getInstance();
        if ($db->isAvailable()) {
            try {
                $row = $db->fetchOne("SELECT * FROM enrollments WHERE id = :id LIMIT 1", [':id' => $id]);
                if ($row) return $row;
            } catch (\Exception $e) {}
        }

        $store = self::readStore();
        foreach ($store as $item) {
            if ((int)$item['id'] === $id) {
                return $item;
            }
        }
        return null;
    }

    public static function findByRef(string $ref): ?array
    {
        $ref = trim($ref);
        $db = Database::getInstance();
        if ($db->isAvailable()) {
            try {
                $row = $db->fetchOne("SELECT * FROM enrollments WHERE booking_reference = :ref LIMIT 1", [':ref' => $ref]);
                if ($row) return $row;
            } catch (\Exception $e) {}
        }

        $store = self::readStore();
        foreach ($store as $item) {
            if (strcasecmp($item['booking_reference'] ?? '', $ref) === 0) {
                return $item;
            }
        }
        return null;
    }

    /**
     * Stage 2: Admin Approves Payment Request -> Status becomes approved_to_pay (student can now enter card and pay)
     */
    public static function approvePayment(string|int $refOrId): ?array
    {
        $item = is_numeric($refOrId) ? self::find((int)$refOrId) : self::findByRef((string)$refOrId);
        if (!$item) {
            return null;
        }

        $ref = $item['booking_reference'];
        $now = date('Y-m-d H:i:s');

        // Update in File Store
        $store = self::readStore();
        $updated = null;
        foreach ($store as &$rec) {
            if ($rec['booking_reference'] === $ref) {
                $rec['payment_status'] = 'approved_to_pay';
                $rec['status'] = 'approved_to_pay';
                $rec['admin_notes'] = 'Square payment APPROVED by Administrator at ' . $now . '. Student is now permitted to pay.';
                $rec['updated_at'] = $now;
                $updated = $rec;
                break;
            }
        }
        self::writeStore($store);

        // Update in MySQL
        $db = Database::getInstance();
        if ($db->isAvailable()) {
            try {
                $db->execute(
                    "UPDATE enrollments SET payment_status = 'approved_to_pay', status = 'approved_to_pay', admin_notes = :notes WHERE booking_reference = :ref",
                    [
                        ':notes' => 'Square payment APPROVED by Administrator at ' . $now . '. Student is now permitted to pay.',
                        ':ref'   => $ref
                    ]
                );
            } catch (\Exception $e) {}
        }

        return $updated ?? $item;
    }

    /**
     * Admin Rejects Payment Request
     */
    public static function rejectPayment(string|int $refOrId, string $reason = 'Rejected by Administrator.'): ?array
    {
        $item = is_numeric($refOrId) ? self::find((int)$refOrId) : self::findByRef((string)$refOrId);
        if (!$item) {
            return null;
        }

        $ref = $item['booking_reference'];
        $now = date('Y-m-d H:i:s');

        $store = self::readStore();
        $updated = null;
        foreach ($store as &$rec) {
            if ($rec['booking_reference'] === $ref) {
                $rec['payment_status'] = 'rejected';
                $rec['status'] = 'cancelled';
                $rec['admin_notes'] = $reason . ' (At ' . $now . ')';
                $rec['updated_at'] = $now;
                $updated = $rec;
                break;
            }
        }
        self::writeStore($store);

        $db = Database::getInstance();
        if ($db->isAvailable()) {
            try {
                $db->execute(
                    "UPDATE enrollments SET payment_status = 'rejected', status = 'cancelled', admin_notes = :notes WHERE booking_reference = :ref",
                    [':notes' => $reason, ':ref' => $ref]
                );
            } catch (\Exception $e) {}
        }

        return $updated ?? $item;
    }

    /**
     * Stage 3: Student completes payment via Square Dummy Gateway
     */
    public static function completeSquarePayment(string $ref, array $cardDetails): ?array
    {
        $item = self::findByRef($ref);
        if (!$item) {
            return null;
        }

        // Only allow payment if admin has approved!
        if ($item['payment_status'] !== 'approved_to_pay') {
            return [
                'error' => 'not_approved',
                'message' => 'This payment request has not been approved by the Administrator yet. Current status: ' . $item['payment_status']
            ];
        }

        $now = date('Y-m-d H:i:s');

        // Execute payment via Square Payments API (Sandbox / Live)
        $squareResult = SquareService::getInstance()->createPayment([
            'amount'            => (float)($item['course_price'] ?? 780.00),
            'booking_reference' => $ref,
            'student_name'      => $item['student_name'] ?? '',
            'student_email'     => $item['student_email'] ?? '',
            'source_id'         => !empty($cardDetails['source_id']) ? $cardDetails['source_id'] : 'cnon:card-nonce-ok',
            'note'              => "DriveMaster: " . ($item['course_name'] ?? 'Driving Course') . " ({$ref})"
        ]);

        $txId = $squareResult['payment_id'] ?? ('sq_sandbox_tx_' . substr(bin2hex(random_bytes(8)), 0, 16));
        $cardLast4 = $squareResult['card_last_4'] ?? (substr(preg_replace('/\D/', '', (string)($cardDetails['card_number'] ?? '4242')), -4) ?: '4242');
        $brand = $squareResult['card_brand'] ?? (!empty($cardDetails['brand']) ? $cardDetails['brand'] : 'Square Sandbox Visa');
        $receiptUrl = $squareResult['receipt_url'] ?? '';

        // Update Store
        $store = self::readStore();
        $updated = null;
        foreach ($store as &$rec) {
            if ($rec['booking_reference'] === $ref) {
                $rec['payment_status'] = 'paid_in_full';
                $rec['status'] = 'confirmed';
                $rec['payment_tx_id'] = $txId;
                $rec['receipt_url'] = $receiptUrl;
                $rec['card_last4'] = $cardLast4;
                $rec['card_brand'] = $brand;
                $rec['paid_at'] = $now;
                $rec['admin_notes'] = "Square Sandbox Payment of \${$rec['course_price']} successfully captured. TX ID: {$txId}.";
                $rec['updated_at'] = $now;
                $updated = $rec;
                break;
            }
        }
        self::writeStore($store);

        // Update MySQL
        $db = Database::getInstance();
        if ($db->isAvailable()) {
            try {
                $db->execute(
                    "UPDATE enrollments SET 
                        payment_status = 'paid_in_full', 
                        status = 'confirmed', 
                        payment_tx_id = :tx_id, 
                        card_last4 = :last4, 
                        admin_notes = :notes 
                     WHERE booking_reference = :ref",
                    [
                        ':tx_id' => $txId,
                        ':last4' => $cardLast4,
                        ':notes' => "Square Dummy Payment captured. TX: {$txId}",
                        ':ref'   => $ref
                    ]
                );
            } catch (\Exception $e) {}
        }

        return $updated ?? $item;
    }

    public static function all(array $filters = []): array
    {
        $db = Database::getInstance();
        if ($db->isAvailable()) {
            try {
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

                $rows = $db->fetchAll($sql, $params);
                if (!empty($rows)) {
                    return $rows;
                }
            } catch (\Exception $e) {}
        }

        // Return from File Store
        $store = self::readStore();
        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $store = array_filter($store, fn($item) => ($item['status'] ?? '') === $filters['status']);
        }
        if (!empty($filters['payment_status']) && $filters['payment_status'] !== 'all') {
            $store = array_filter($store, fn($item) => ($item['payment_status'] ?? '') === $filters['payment_status']);
        }
        if (!empty($filters['search'])) {
            $s = strtolower($filters['search']);
            $store = array_filter($store, function($item) use ($s) {
                return str_contains(strtolower($item['student_name'] ?? ''), $s) ||
                       str_contains(strtolower($item['student_email'] ?? ''), $s) ||
                       str_contains(strtolower($item['booking_reference'] ?? ''), $s);
            });
        }

        return array_values($store);
    }

    public static function updateStatus(int $id, string $status, ?string $paymentStatus = null): bool
    {
        $store = self::readStore();
        $found = false;
        foreach ($store as &$rec) {
            if ((int)$rec['id'] === $id) {
                $rec['status'] = $status;
                if ($paymentStatus !== null) {
                    $rec['payment_status'] = $paymentStatus;
                }
                $rec['updated_at'] = date('Y-m-d H:i:s');
                $found = true;
                break;
            }
        }
        if ($found) {
            self::writeStore($store);
        }

        $db = Database::getInstance();
        if ($db->isAvailable()) {
            try {
                $sql = "UPDATE enrollments 
                        SET status = :status, 
                            payment_status = COALESCE(:payment, payment_status) 
                        WHERE id = :id";
                return $db->execute($sql, [
                    ':id'      => $id,
                    ':status'  => $status,
                    ':payment' => $paymentStatus
                ]);
            } catch (\Exception $e) {}
        }

        return true;
    }

    public static function countStats(): array
    {
        $all = self::all();
        $total = count($all);
        $confirmed = 0;
        $pending = 0;
        $squarePending = 0;
        $revenue = 0.0;

        foreach ($all as $item) {
            $st = $item['status'] ?? '';
            $pay = $item['payment_status'] ?? '';
            if ($st === 'confirmed' || $st === 'in_training') {
                $confirmed++;
            }
            if ($st === 'pending' || $pay === 'payment_requested') {
                $pending++;
            }
            if ($pay === 'payment_requested') {
                $squarePending++;
            }
            if ($pay === 'paid_in_full') {
                $revenue += (float)($item['course_price'] ?? 0);
            }
        }

        return [
            'total_bookings' => $total,
            'confirmed'      => $confirmed,
            'pending'        => $pending,
            'square_pending' => $squarePending,
            'revenue'        => $revenue,
        ];
    }
}
