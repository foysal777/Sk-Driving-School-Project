<?php
declare(strict_types=1);

/**
 * Sync script to import/sync JSON file stores into MySQL database.
 * Run via terminal: php backend/scripts/sync_json_to_mysql.php
 */

require_once __DIR__ . '/../src/Database.php';

use DriveMaster\Database;

$db = Database::getInstance();

if (!$db->isAvailable()) {
    echo "❌ Cannot connect to MySQL. Please check your DB credentials in .env or backend/config/database.php\n";
    echo "Tip: Run the database setup commands first.\n";
    exit(1);
}

echo "✅ Connected to MySQL successfully!\n";

// 1. Run Schema
$schemaFile = __DIR__ . '/../../database/schema.sql';
if (file_exists($schemaFile)) {
    echo "➡️ Applying schema...\n";
    $sql = (string)file_get_contents($schemaFile);
    $pdo = $db->getConnection();
    if ($pdo !== null) {
        $pdo->exec($sql);
        echo "✅ Schema applied.\n";
    }
}

// 2. Sync Inquiries
$inquiriesFile = __DIR__ . '/../../database/inquiries_store.json';
if (file_exists($inquiriesFile)) {
    $items = json_decode((string)file_get_contents($inquiriesFile), true) ?: [];
    echo "➡️ Syncing " . count($items) . " inquiries...\n";
    foreach ($items as $inq) {
        $db->execute(
            "INSERT INTO inquiries (id, full_name, email, phone, subject, pickup_location, timeline, preferred_schedule, message, status, admin_notes, ip_address, created_at)
             VALUES (:id, :name, :email, :phone, :subj, :pickup, :time, :sched, :msg, :status, :notes, :ip, :created)
             ON DUPLICATE KEY UPDATE status = VALUES(status), admin_notes = VALUES(admin_notes)",
            [
                ':id'      => $inq['id'] ?? null,
                ':name'    => $inq['full_name'] ?? $inq['name'] ?? 'Visitor',
                ':email'   => $inq['email'] ?? '',
                ':phone'   => $inq['phone'] ?? '',
                ':subj'    => $inq['subject'] ?? 'Website Inquiry',
                ':pickup'  => $inq['pickup_location'] ?? null,
                ':time'    => $inq['timeline'] ?? null,
                ':sched'   => $inq['preferred_schedule'] ?? 'Flexible',
                ':msg'     => $inq['message'] ?? '',
                ':status'  => $inq['status'] ?? 'new',
                ':notes'   => $inq['admin_notes'] ?? null,
                ':ip'      => $inq['ip_address'] ?? '127.0.0.1',
                ':created' => $inq['created_at'] ?? date('Y-m-d H:i:s'),
            ]
        );
    }
    echo "✅ Inquiries synced.\n";
}

// 3. Sync Newsletter Subscribers
$newsletterFile = __DIR__ . '/../../database/newsletter_subscribers.json';
if (file_exists($newsletterFile)) {
    $items = json_decode((string)file_get_contents($newsletterFile), true) ?: [];
    echo "➡️ Syncing " . count($items) . " newsletter subscribers...\n";
    foreach ($items as $sub) {
        $db->execute(
            "INSERT INTO newsletter_subscribers (id, email, source, status, ip_address, created_at)
             VALUES (:id, :email, :source, :status, :ip, :created)
             ON DUPLICATE KEY UPDATE status = VALUES(status)",
            [
                ':id'      => $sub['id'] ?? null,
                ':email'   => $sub['email'] ?? '',
                ':source'  => $sub['source'] ?? 'website',
                ':status'  => $sub['status'] ?? 'subscribed',
                ':ip'      => $sub['ip_address'] ?? '127.0.0.1',
                ':created' => $sub['created_at'] ?? date('Y-m-d H:i:s'),
            ]
        );
    }
    echo "✅ Newsletter subscribers synced.\n";
}

// 4. Sync Enrollments
$enrollmentsFile = __DIR__ . '/../../database/enrollments_store.json';
if (file_exists($enrollmentsFile)) {
    $items = json_decode((string)file_get_contents($enrollmentsFile), true) ?: [];
    echo "➡️ Syncing " . count($items) . " enrollments...\n";
    foreach ($items as $e) {
        $db->execute(
            "INSERT INTO enrollments (id, booking_reference, student_name, student_email, student_phone, course_name, course_price, preferred_transmission, payment_gateway, payment_status, status, admin_notes, created_at)
             VALUES (:id, :ref, :name, :email, :phone, :cname, :price, :trans, :gw, :pay_status, :status, :notes, :created)
             ON DUPLICATE KEY UPDATE payment_status = VALUES(payment_status), status = VALUES(status), admin_notes = VALUES(admin_notes)",
            [
                ':id'         => $e['id'] ?? null,
                ':ref'        => $e['booking_reference'] ?? 'DM-REF',
                ':name'       => $e['student_name'] ?? 'Student',
                ':email'      => $e['student_email'] ?? '',
                ':phone'      => $e['student_phone'] ?? '',
                ':cname'      => $e['course_name'] ?? 'Driving Course',
                ':price'      => $e['course_price'] ?? 0.00,
                ':trans'      => $e['preferred_transmission'] ?? 'automatic',
                ':gw'         => $e['payment_gateway'] ?? 'square',
                ':pay_status' => $e['payment_status'] ?? 'unpaid',
                ':status'     => $e['status'] ?? 'pending',
                ':notes'      => $e['admin_notes'] ?? null,
                ':created'    => $e['created_at'] ?? date('Y-m-d H:i:s'),
            ]
        );
    }
    echo "✅ Enrollments synced.\n";
}

echo "🎉 All data successfully synced into MySQL database!\n";
