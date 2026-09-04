-- ============================================
-- Driving School Database Schema
-- ============================================

-- Create database if not exists
CREATE DATABASE IF NOT EXISTS `driving_school`;
USE `driving_school`;

-- ============================================
-- USERS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) UNIQUE NOT NULL,
    `phone` VARCHAR(20) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('user', 'admin', 'instructor') DEFAULT 'user',
    `status` ENUM('active', 'inactive', 'blocked') DEFAULT 'active',
    `profile_image` VARCHAR(255) NULL,
    `date_of_birth` DATE NULL,
    `address` TEXT NULL,
    `city` VARCHAR(50) NULL,
    `state` VARCHAR(50) NULL,
    `postal_code` VARCHAR(10) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- COURSES TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS `courses` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `title` VARCHAR(150) NOT NULL,
    `description` TEXT NOT NULL,
    `level` ENUM('Beginner', 'Intermediate', 'Advanced') NOT NULL,
    `price` DECIMAL(10, 2) NOT NULL,
    `duration_hours` INT NOT NULL,
    `duration_weeks` INT NOT NULL,
    `lessons_count` INT NOT NULL DEFAULT 0,
    `features` TEXT NOT NULL COMMENT 'JSON or comma-separated features',
    `vehicle_types` VARCHAR(255) NOT NULL COMMENT 'e.g., Car, Bike',
    `image` VARCHAR(255) NULL,
    `status` ENUM('active', 'inactive') DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_level (level),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- INSTRUCTORS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS `instructors` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `experience_years` INT NOT NULL,
    `specialty` VARCHAR(150) COMMENT 'e.g., Car, Bike, Heavy Vehicle',
    `phone` VARCHAR(20) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `bio` TEXT NULL,
    `image` VARCHAR(255) NULL,
    `rating` DECIMAL(3, 2) DEFAULT 5.00,
    `status` ENUM('active', 'inactive', 'on_leave') DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_status (status),
    INDEX idx_specialty (specialty)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- ENROLLMENTS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS `enrollments` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `course_id` INT NOT NULL,
    `enrollment_date` DATE NOT NULL,
    `status` ENUM('active', 'completed', 'cancelled') DEFAULT 'active',
    `progress_percentage` INT DEFAULT 0,
    `certificate_issued` BOOLEAN DEFAULT FALSE,
    `certificate_date` DATE NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    UNIQUE KEY unique_enrollment (user_id, course_id),
    INDEX idx_status (status),
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- BOOKINGS/LESSONS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS `bookings` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `course_id` INT NOT NULL,
    `instructor_id` INT NOT NULL,
    `booking_date` DATE NOT NULL,
    `start_time` TIME NOT NULL,
    `end_time` TIME NOT NULL,
    `duration_hours` INT DEFAULT 1,
    `vehicle_type` VARCHAR(50) NOT NULL,
    `location` VARCHAR(255) NULL,
    `notes` TEXT NULL,
    `status` ENUM('pending', 'approved', 'rejected', 'completed', 'cancelled') DEFAULT 'pending',
    `payment_status` ENUM('pending', 'paid', 'failed') DEFAULT 'pending',
    `price` DECIMAL(10, 2) NOT NULL,
    `admin_notes` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (instructor_id) REFERENCES instructors(id) ON DELETE RESTRICT,
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    INDEX idx_booking_date (booking_date),
    INDEX idx_instructor_id (instructor_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- REVIEWS/RATINGS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS `reviews` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `instructor_id` INT NULL,
    `course_id` INT NULL,
    `booking_id` INT NULL,
    `rating` INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    `title` VARCHAR(150) NOT NULL,
    `comment` TEXT NOT NULL,
    `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    `admin_response` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (instructor_id) REFERENCES instructors(id) ON DELETE SET NULL,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE SET NULL,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE SET NULL,
    INDEX idx_rating (rating),
    INDEX idx_status (status),
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- ADMIN USERS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS `admin` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `username` VARCHAR(50) UNIQUE NOT NULL,
    `email` VARCHAR(100) UNIQUE NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(100) NOT NULL,
    `role` ENUM('super_admin', 'admin', 'moderator') DEFAULT 'admin',
    `status` ENUM('active', 'inactive') DEFAULT 'active',
    `last_login` DATETIME NULL,
    `ip_address` VARCHAR(45) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TESTIMONIALS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS `testimonials` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `image` VARCHAR(255) NULL,
    `title` VARCHAR(150) NOT NULL,
    `message` TEXT NOT NULL,
    `rating` INT DEFAULT 5 CHECK (rating >= 1 AND rating <= 5),
    `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- CONTACT MESSAGES TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS `contact_messages` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `phone` VARCHAR(20) NULL,
    `subject` VARCHAR(150) NOT NULL,
    `message` TEXT NOT NULL,
    `status` ENUM('new', 'read', 'replied') DEFAULT 'new',
    `reply` TEXT NULL,
    `reply_date` DATETIME NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- PAYMENTS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS `payments` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `booking_id` INT NULL,
    `enrollment_id` INT NULL,
    `amount` DECIMAL(10, 2) NOT NULL,
    `payment_method` ENUM('card', 'bank_transfer', 'upi', 'cash') DEFAULT 'card',
    `gateway` VARCHAR(50) NULL COMMENT 'e.g., Razorpay, Stripe',
    `transaction_id` VARCHAR(100) UNIQUE NULL,
    `status` ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    `receipt_url` VARCHAR(255) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE SET NULL,
    FOREIGN KEY (enrollment_id) REFERENCES enrollments(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    INDEX idx_transaction_id (transaction_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- SITE SETTINGS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS `site_settings` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `setting_key` VARCHAR(100) UNIQUE NOT NULL,
    `setting_value` LONGTEXT NOT NULL,
    `description` VARCHAR(255) NULL,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_key (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Insert default data
-- ============================================

-- Insert admin user
INSERT INTO `admin` (`username`, `email`, `password`, `full_name`, `role`, `status`) 
VALUES ('Admin', 'admin@driveeasy.com', '$2y$10$R/nJ.Go0hIFzyuyrET3JQuvd1t958jT0aSp8aV/8HIFqUCPZUb2hi', 'Admin User', 'super_admin', 'active')
ON DUPLICATE KEY UPDATE `id`=`id`;

-- Insert sample courses
INSERT INTO `courses` (`title`, `description`, `level`, `price`, `duration_hours`, `duration_weeks`, `lessons_count`, `features`, `vehicle_types`, `status`) 
VALUES 
('Beginner Car Driving', 'Complete beginner course for car driving with basic traffic rules', 'Beginner', 5000, 10, 4, 5, 'Basic controls, Traffic rules, Simple driving', 'Car', 'active'),
('Intermediate Car Driving', 'Intermediate car driving with advanced maneuvers', 'Intermediate', 8000, 15, 6, 7, 'Advanced controls, Highway driving, parking', 'Car', 'active'),
('Advanced Driving Techniques', 'Advanced driving course with defensive driving', 'Advanced', 12000, 20, 8, 10, 'Defensive driving, Emergency handling, Night driving', 'Car', 'active'),
('Bike Training Basic', 'Basic motorcycle training course', 'Beginner', 4000, 8, 3, 4, 'Balance, Basic controls, Safety gear', 'Bike', 'active'),
('License Assistance Package', 'Complete license assistance with practice tests', 'Beginner', 3000, 5, 2, 2, 'RTO rules, Document guidance, Test preparation', 'All', 'active')
ON DUPLICATE KEY UPDATE `id`=`id`;

-- Insert sample site settings
INSERT INTO `site_settings` (`setting_key`, `setting_value`, `description`) 
VALUES 
('site_phone', '+91-9876543210', 'School contact phone number'),
('site_email', 'info@driveeasy.com', 'School contact email'),
('site_address', '123 Driving Avenue, City, State 12345', 'School physical address'),
('working_hours', 'Mon-Sun: 06:00 AM - 08:00 PM', 'School working hours'),
('school_description', 'Best driving school in the city', 'Short description'),
('map_latitude', '40.7128', 'Google Maps latitude'),
('map_longitude', '-74.0060', 'Google Maps longitude')
ON DUPLICATE KEY UPDATE `id`=`id`;
