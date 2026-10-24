<?php
declare(strict_types=1);

namespace DriveMaster\Models;

use DriveMaster\Database;

/**
 * Course Model - Manages driving syllabi and packages
 */
final class Course
{
    public static function all(string $status = 'active'): array
    {
        $db = Database::getInstance();
        if (!$db->isAvailable()) {
            return self::defaultSeedCourses();
        }

        $sql = "SELECT * FROM courses WHERE status = :status ORDER BY sort_order ASC, id ASC";
        $courses = $db->fetchAll($sql, [':status' => $status]);

        if (empty($courses)) {
            return self::defaultSeedCourses();
        }

        foreach ($courses as &$c) {
            if (isset($c['features_json']) && is_string($c['features_json'])) {
                $c['features'] = json_decode($c['features_json'], true);
            }
        }

        return $courses;
    }

    public static function find(int|string $idOrSlug): ?array
    {
        $db = Database::getInstance();
        if (!$db->isAvailable()) {
            foreach (self::defaultSeedCourses() as $c) {
                if ($c['id'] == $idOrSlug || $c['slug'] == $idOrSlug) {
                    return $c;
                }
            }
            return null;
        }

        $sql = is_numeric($idOrSlug) 
            ? "SELECT * FROM courses WHERE id = :val LIMIT 1"
            : "SELECT * FROM courses WHERE slug = :val LIMIT 1";

        $course = $db->fetchOne($sql, [':val' => $idOrSlug]);
        if ($course && isset($course['features_json']) && is_string($course['features_json'])) {
            $course['features'] = json_decode($course['features_json'], true);
        }

        return $course;
    }

    public static function create(array $data): ?array
    {
        $db = Database::getInstance();
        if (!$db->isAvailable()) {
            return null;
        }

        $slug = preg_replace('/[^a-z0-9]+/i', '-', strtolower(trim((string)$data['title'])));
        $features = is_array($data['features'] ?? null) 
            ? json_encode($data['features']) 
            : json_encode(array_filter(array_map('trim', explode("\n", (string)($data['features_raw'] ?? '')))));

        $sql = "INSERT INTO courses (
                    slug, title, category, short_description, full_description,
                    duration_hours, total_price, hourly_rate, badge_label,
                    features_json, is_featured, status, sort_order
                ) VALUES (
                    :slug, :title, :category, :short_desc, :full_desc,
                    :duration, :price, :hourly, :badge,
                    :features, :featured, :status, :sort_order
                )";

        $params = [
            ':slug'        => $slug,
            ':title'       => trim((string)$data['title']),
            ':category'    => trim((string)($data['category'] ?? 'beginner')),
            ':short_desc'  => trim((string)($data['short_description'] ?? '')),
            ':full_desc'   => trim((string)($data['full_description'] ?? '')),
            ':duration'    => (int)($data['duration_hours'] ?? 10),
            ':price'       => (float)($data['total_price'] ?? 0.0),
            ':hourly'      => (float)($data['hourly_rate'] ?? 0.0),
            ':badge'       => trim((string)($data['badge_label'] ?? '')),
            ':features'    => $features,
            ':featured'    => !empty($data['is_featured']) ? 1 : 0,
            ':status'      => $data['status'] ?? 'active',
            ':sort_order'  => (int)($data['sort_order'] ?? 0),
        ];

        $db->query($sql, $params);
        return self::find((int)$db->lastInsertId());
    }

    public static function update(int $id, array $data): bool
    {
        $db = Database::getInstance();
        if (!$db->isAvailable()) {
            return true;
        }

        $features = is_array($data['features'] ?? null) 
            ? json_encode($data['features']) 
            : json_encode(array_filter(array_map('trim', explode("\n", (string)($data['features_raw'] ?? '')))));

        $sql = "UPDATE courses SET 
                    title = :title,
                    category = :category,
                    short_description = :short_desc,
                    duration_hours = :duration,
                    total_price = :price,
                    hourly_rate = :hourly,
                    badge_label = :badge,
                    features_json = :features,
                    is_featured = :featured,
                    status = :status
                WHERE id = :id";

        return $db->execute($sql, [
            ':id'         => $id,
            ':title'      => trim((string)$data['title']),
            ':category'   => trim((string)($data['category'] ?? 'beginner')),
            ':short_desc' => trim((string)($data['short_description'] ?? '')),
            ':duration'   => (int)($data['duration_hours'] ?? 10),
            ':price'      => (float)($data['total_price'] ?? 0.0),
            ':hourly'     => (float)($data['hourly_rate'] ?? 0.0),
            ':badge'      => trim((string)($data['badge_label'] ?? '')),
            ':features'   => $features,
            ':featured'   => !empty($data['is_featured']) ? 1 : 0,
            ':status'     => $data['status'] ?? 'active',
        ]);
    }

    public static function delete(int $id): bool
    {
        $db = Database::getInstance();
        if (!$db->isAvailable()) {
            return true;
        }

        return $db->execute("DELETE FROM courses WHERE id = :id", [':id' => $id]);
    }

    public static function defaultSeedCourses(): array
    {
        return [
            [
                'id' => 1,
                'slug' => 'beginner-starter-pack',
                'title' => 'Beginner Starter Pack',
                'category' => 'beginner',
                'short_description' => 'Ideal for beginners who have never touched a steering wheel.',
                'duration_hours' => 10,
                'total_price' => 420.00,
                'hourly_rate' => 42.00,
                'badge_label' => 'Beginner Essential',
                'is_featured' => 0,
                'status' => 'active',
                'features' => [
                    '10 Hours 1-on-1 Behind-The-Wheel',
                    'Modern Dual-Control 2024 Vehicle',
                    'Free Doorstep Pickup & Return',
                    'Official DVSA Theory App Access'
                ]
            ],
            [
                'id' => 2,
                'slug' => 'standard-pass-course',
                'title' => 'Standard Pass Course',
                'category' => 'popular beginner',
                'short_description' => 'Our #1 chosen course to take you from initial driving to passing your test.',
                'duration_hours' => 20,
                'total_price' => 780.00,
                'hourly_rate' => 39.00,
                'badge_label' => '98.4% Pass Rate',
                'is_featured' => 1,
                'status' => 'active',
                'features' => [
                    '20 Hours Comprehensive In-Car Lessons',
                    '2 Full Mock Driving Tests on Test Routes',
                    'Official Test Day Car Rental Included',
                    'Free Test Re-Sit Assurance Guarantee'
                ]
            ],
            [
                'id' => 3,
                'slug' => 'fast-track-intensive-pack',
                'title' => 'Fast-Track Intensive Pack',
                'category' => 'intensive',
                'short_description' => 'Compressed 30-hour intensive program with daily immersive sessions.',
                'duration_hours' => 30,
                'total_price' => 1150.00,
                'hourly_rate' => 38.00,
                'badge_label' => 'Pass In 1-2 Weeks',
                'is_featured' => 0,
                'status' => 'active',
                'features' => [
                    '30 Hours Daily Immersive Behind-The-Wheel',
                    'Expedited Practical Test Booking Support',
                    'Test Car Rental & Pre-Test Warmup Included'
                ]
            ],
            [
                'id' => 4,
                'slug' => 'automatic-only-specialist',
                'title' => 'Automatic Only Specialist',
                'category' => 'automatic',
                'short_description' => 'Zero clutch pedals, zero gear juggling. Focus completely on observation.',
                'duration_hours' => 15,
                'total_price' => 630.00,
                'hourly_rate' => 42.00,
                'badge_label' => 'No Stalling',
                'is_featured' => 0,
                'status' => 'active',
                'features' => [
                    '15 Hours Focused In-Car Tuition',
                    'Latest 2024 Toyota Hybrid Dual-Control Car',
                    'Free Door-to-Door Pickup Included'
                ]
            ],
            [
                'id' => 5,
                'slug' => 'defensive-motorway-master',
                'title' => 'Defensive & Motorway Master',
                'category' => 'advanced',
                'short_description' => 'For newly passed drivers wanting high-speed motorway confidence.',
                'duration_hours' => 8,
                'total_price' => 350.00,
                'hourly_rate' => 43.00,
                'badge_label' => 'Pass Plus Certified',
                'is_featured' => 0,
                'status' => 'active',
                'features' => [
                    '8 Hours Specialized Motorway & Highway',
                    'Slip Roads, High-Speed Merging & Overtaking',
                    'Pass Plus Certificate (Up to 25% Off Insurance)'
                ]
            ],
            [
                'id' => 6,
                'slug' => 'manual-transmission-mastery',
                'title' => 'Manual Transmission Mastery',
                'category' => 'beginner',
                'short_description' => 'Gain a full unrestricted license allowing you to drive manual & auto worldwide.',
                'duration_hours' => 15,
                'total_price' => 600.00,
                'hourly_rate' => 40.00,
                'badge_label' => 'Full Category B',
                'is_featured' => 0,
                'status' => 'active',
                'features' => [
                    '15 Hours In-Car Manual Tuition',
                    'Friction Point Control & Zero Stall Technique',
                    'Progressive Gear Selection & Engine Braking'
                ]
            ]
        ];
    }
}
