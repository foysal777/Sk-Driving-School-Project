-- ==============================================================================
-- SK DRIVING SCHOOL (DriveMaster) - SEED DATA
-- Default Admin: admin@drivemaster.com / Admin@123456
-- ==============================================================================

USE `drivemaster_db`;

-- Seed Admin User
INSERT INTO `admins` (`username`, `email`, `password_hash`, `full_name`, `role`, `status`)
VALUES (
  'admin',
  'admin@drivemaster.com',
  '$2y$12$zv2/CAqw5G6DQNjqiHz14Oot32nDEbmPoLrgYBjjx3b8VI4pc5urq',
  'Master Instructor Director',
  'super_admin',
  'active'
) ON DUPLICATE KEY UPDATE `full_name` = VALUES(`full_name`);

-- Seed Courses matching courses.html
INSERT INTO `courses` (`slug`, `title`, `category`, `short_description`, `duration_hours`, `total_price`, `hourly_rate`, `badge_label`, `features_json`, `is_featured`, `status`, `sort_order`)
VALUES
(
  'beginner-starter-pack',
  'Beginner Starter Pack',
  'beginner',
  'Ideal for beginners who have never touched a steering wheel. Covers cockpit drill, clutch bite point, and smooth steering.',
  10,
  420.00,
  42.00,
  'Beginner Essential',
  JSON_ARRAY(
    '10 Hours 1-on-1 Behind-The-Wheel',
    'Modern Dual-Control 2024 Vehicle',
    'Free Doorstep Pickup & Return',
    'Official DVSA Theory App Access',
    'Driver Progress Tracker & Logbook'
  ),
  0,
  'active',
  1
),
(
  'standard-pass-course',
  'Standard Pass Course',
  'popular beginner',
  'Our #1 chosen course to take you from initial driving to passing your official practical driving test with flying colors.',
  20,
  780.00,
  39.00,
  '98.4% Pass Rate',
  JSON_ARRAY(
    '20 Hours Comprehensive In-Car Lessons',
    '2 Full Mock Driving Tests on Test Routes',
    'Official Test Day Car Rental Included',
    'Parallel, Bay Parking & Emergency Stop',
    'Free Test Re-Sit Assurance Guarantee'
  ),
  1,
  'active',
  2
),
(
  'fast-track-intensive-pack',
  'Fast-Track Intensive Pack',
  'intensive',
  'Need your license fast for a new job or relocation? Compressed 30-hour intensive program with daily 3-hour immersive sessions.',
  30,
  1150.00,
  38.00,
  'Pass In 1-2 Weeks',
  JSON_ARRAY(
    '30 Hours Daily Immersive Behind-The-Wheel',
    'Expedited Practical Test Booking Support',
    'Fast Muscle-Memory & Reaction Training',
    '3 Examiner Mock Route Evaluations',
    'Test Car Rental & Pre-Test Warmup Included'
  ),
  0,
  'active',
  3
),
(
  'automatic-only-specialist',
  'Automatic Only Specialist',
  'automatic',
  'Zero clutch pedals, zero gear juggling. Focus completely on observation, steering control, and road dynamics in hybrid cars.',
  15,
  630.00,
  42.00,
  'No Stalling',
  JSON_ARRAY(
    '15 Hours Focused In-Car Tuition',
    'Latest 2024 Toyota Hybrid Dual-Control Car',
    'Effortless Hill Starts & Stop-and-Go Traffic',
    'Fast Mastery for Nervous or Older Learners',
    'Free Door-to-Door Pickup Included'
  ),
  0,
  'active',
  4
),
(
  'defensive-motorway-master',
  'Defensive & Motorway Master',
  'advanced',
  'For newly passed drivers or test-ready students wanting high-speed motorway confidence, inclement weather handling, and hazard avoidance.',
  8,
  350.00,
  43.00,
  'Pass Plus Certified',
  JSON_ARRAY(
    '8 Hours Specialized Motorway & Highway',
    'Slip Roads, High-Speed Merging & Overtaking',
    'Complex Multi-Lane Turbo Roundabouts',
    'Adverse Weather & Night Hazard Avoidance',
    'Pass Plus Certificate (Up to 25% Off Insurance)'
  ),
  0,
  'active',
  5
),
(
  'manual-transmission-mastery',
  'Manual Transmission Mastery',
  'beginner',
  'Gain a full unrestricted license allowing you to drive both manual and automatic vehicles worldwide.',
  15,
  600.00,
  40.00,
  'Full Category B',
  JSON_ARRAY(
    '15 Hours In-Car Manual Tuition',
    'Friction Point Control & Zero Stall Technique',
    'Steep Hill Starts With Handbrake & Footbrake',
    'Progressive Gear Selection & Engine Braking',
    'Complete Maneuvers Preparation Included'
  ),
  0,
  'active',
  6
)
ON DUPLICATE KEY UPDATE `title` = VALUES(`title`), `total_price` = VALUES(`total_price`);

-- Seed Sample Inquiries (to populate the Admin Panel right away)
INSERT INTO `inquiries` (`full_name`, `email`, `phone`, `subject`, `pickup_location`, `timeline`, `preferred_schedule`, `message`, `status`)
VALUES
(
  'Sophie Turner',
  'sophie.turner@example.com',
  '+66 12 444 8899',
  'Automatic Lessons',
  'Central City, 10110',
  'As Soon As Possible',
  'Weekday Mornings',
  'Hello! I am a complete beginner nervous about driving. I would like to book a 15-hour automatic course with an experienced instructor.',
  'new'
),
(
  'Daniel Craig',
  'daniel.c@example.com',
  '+66 12 777 2233',
  'Intensive Fast-Track',
  'Riverside District, 10200',
  'Within 2 Weeks',
  'Weekends',
  'Looking for fast-track intensive lessons over the next two weekends. Need test car hire as well.',
  'contacted'
),
(
  'Emma Watson',
  'emma.w@example.com',
  '+66 12 555 1122',
  'Standard Pass Course',
  'Westside Highway, 10300',
  'Next Month',
  'Weekday Evenings',
  'Hi, do you provide pickup from University campus for evening classes? Thanks!',
  'enrolled'
);

-- Seed Sample Enrollments
INSERT INTO `enrollments` (`booking_reference`, `student_name`, `student_email`, `student_phone`, `course_id`, `course_name`, `course_price`, `pickup_address`, `preferred_start_date`, `preferred_transmission`, `payment_status`, `status`)
VALUES
(
  'DM-2026-8901',
  'Sophie Turner',
  'sophie.turner@example.com',
  '+66 12 444 8899',
  4,
  'Automatic Only Specialist',
  630.00,
  '124 Sukhumvit Soi 11, Bangkok',
  DATE_ADD(CURRENT_DATE, INTERVAL 3 DAY),
  'automatic',
  'deposit_paid',
  'confirmed'
),
(
  'DM-2026-8902',
  'Liam Neeson',
  'liam.n@example.com',
  '+66 12 666 4321',
  2,
  'Standard Pass Course',
  780.00,
  '88 Sathorn Road, Bangkok',
  DATE_ADD(CURRENT_DATE, INTERVAL 7 DAY),
  'manual',
  'paid_in_full',
  'in_training'
);

-- Seed System Settings
INSERT INTO `system_settings` (`setting_key`, `setting_value`, `description`)
VALUES
('school_name', 'DriveMaster Driving School', 'Official school branding name'),
('support_email', 'info@drivemaster-school.com', 'Primary customer inquiry email'),
('hotline_phone', '+66 12 000 3456', 'Student booking telephone line'),
('operating_hours', 'Mon - Sun: 7:00 AM - 9:00 PM', 'Academy lesson schedule hours'),
('currency_symbol', '$', 'Currency symbol used across system')
ON DUPLICATE KEY UPDATE `setting_value` = VALUES(`setting_value`);
