-- ==============================================================================
-- SK DRIVING SCHOOL (DriveMaster) - DATABASE SCHEMA
-- Target Engine: MySQL 8.0+ / MariaDB 10.4+
-- Charset: utf8mb4 / Collation: utf8mb4_unicode_ci
-- ==============================================================================

CREATE DATABASE IF NOT EXISTS `drivemaster_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `drivemaster_db`;

-- ------------------------------------------------------------------------------
-- Table 1: admins (System administrators for Admin Panel access)
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `admins` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(60) NOT NULL UNIQUE,
  `email` VARCHAR(120) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(100) NOT NULL,
  `role` ENUM('super_admin', 'instructor_admin', 'support') DEFAULT 'super_admin',
  `status` ENUM('active', 'inactive') DEFAULT 'active',
  `avatar_url` VARCHAR(255) DEFAULT NULL,
  `last_login_at` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- Table 2: courses (Driving lesson syllabi and pricing packages)
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `courses` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `slug` VARCHAR(80) NOT NULL UNIQUE,
  `title` VARCHAR(120) NOT NULL,
  `category` VARCHAR(50) NOT NULL DEFAULT 'beginner', -- beginner, popular, intensive, automatic, advanced
  `short_description` VARCHAR(255) NOT NULL,
  `full_description` TEXT DEFAULT NULL,
  `duration_hours` INT UNSIGNED NOT NULL DEFAULT 10,
  `total_price` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
  `hourly_rate` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
  `badge_label` VARCHAR(60) DEFAULT NULL,
  `features_json` JSON DEFAULT NULL,
  `is_featured` TINYINT(1) DEFAULT 0,
  `status` ENUM('active', 'draft', 'archived') DEFAULT 'active',
  `sort_order` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- Table 3: inquiries (Contact form submissions from contact.html email form box)
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `inquiries` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(120) NOT NULL,
  `phone` VARCHAR(30) NOT NULL,
  `subject` VARCHAR(150) NOT NULL,
  `pickup_location` VARCHAR(150) DEFAULT NULL,
  `timeline` VARCHAR(80) DEFAULT NULL,
  `preferred_schedule` VARCHAR(80) DEFAULT NULL,
  `message` TEXT NOT NULL,
  `status` ENUM('new', 'contacted', 'in_progress', 'enrolled', 'closed') DEFAULT 'new',
  `admin_notes` TEXT DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_inquiries_status` (`status`),
  INDEX `idx_inquiries_email` (`email`),
  INDEX `idx_inquiries_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- Table 4: enrollments (Course bookings from courses.html / modal)
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `enrollments` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `booking_reference` VARCHAR(20) NOT NULL UNIQUE,
  `student_name` VARCHAR(100) NOT NULL,
  `student_email` VARCHAR(120) NOT NULL,
  `student_phone` VARCHAR(30) NOT NULL,
  `course_id` INT UNSIGNED DEFAULT NULL,
  `course_name` VARCHAR(120) NOT NULL,
  `course_price` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
  `pickup_address` VARCHAR(200) DEFAULT NULL,
  `preferred_start_date` DATE DEFAULT NULL,
  `preferred_transmission` ENUM('automatic', 'manual', 'either') DEFAULT 'automatic',
  `payment_gateway` VARCHAR(50) DEFAULT 'square',
  `payment_status` VARCHAR(50) DEFAULT 'unpaid',
  `payment_tx_id` VARCHAR(100) DEFAULT NULL,
  `card_last4` VARCHAR(10) DEFAULT NULL,
  `status` ENUM('pending', 'approved_to_pay', 'confirmed', 'in_training', 'completed', 'cancelled') DEFAULT 'pending',
  `admin_notes` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_enrollments_course` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE SET NULL,
  INDEX `idx_enrollments_status` (`status`),
  INDEX `idx_enrollments_email` (`student_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- Table 5: system_settings (Key-value store for academy configuration)
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `system_settings` (
  `setting_key` VARCHAR(60) PRIMARY KEY,
  `setting_value` TEXT NOT NULL,
  `description` VARCHAR(255) DEFAULT NULL,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- Table 6: newsletter_subscribers (Email subscribers from frontend forms)
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `newsletter_subscribers` (
  `id` BIGINT UNSIGNED PRIMARY KEY,
  `email` VARCHAR(254) NOT NULL UNIQUE,
  `source` VARCHAR(60) DEFAULT 'website',
  `status` VARCHAR(20) DEFAULT 'subscribed',
  `ip_address` VARCHAR(45) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

