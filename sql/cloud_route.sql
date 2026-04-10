-- Cloud Route - Intelligent Transportation Management System
-- Database Schema for MySQL

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------
-- 1. Table structure for table `users`
-- --------------------------------------------------------

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL UNIQUE,
  `email` varchar(100) NOT NULL UNIQUE,
  `phone` varchar(15) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  CONSTRAINT `chk_name_length` CHECK (char_length(`name`) >= 3),
  CONSTRAINT `chk_email_format` CHECK (`email` regexp '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\\.[A-Za-z]{2,}$'),
  CONSTRAINT `chk_password_length` CHECK (char_length(`password`) >= 8)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 2. Table structure for table `routes`
-- --------------------------------------------------------

CREATE TABLE `routes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `route_code` varchar(20) NOT NULL UNIQUE,
  `route_name` varchar(100) NOT NULL,
  `origin` varchar(100) NOT NULL,
  `destination` varchar(100) NOT NULL,
  `price` decimal(10,2) DEFAULT 0.00,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 3. Table structure for table `drivers`
-- --------------------------------------------------------

CREATE TABLE `drivers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `license_number` varchar(50) NOT NULL UNIQUE,
  `phone` varchar(15) NOT NULL,
  `assigned_bus` varchar(20) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 4. Table structure for table `buses`
-- --------------------------------------------------------

CREATE TABLE `buses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bus_number` varchar(20) NOT NULL UNIQUE,
  `capacity` int(11) NOT NULL,
  `route_id` int(11) DEFAULT NULL,
  `route_code` varchar(20) DEFAULT NULL,
  `driver_id` int(11) DEFAULT NULL,
  `status` enum('active','maintenance','inactive','deleted') DEFAULT 'active',
  `current_lat` decimal(10,8) DEFAULT NULL,
  `current_lng` decimal(11,8) DEFAULT NULL,
  `last_update` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`route_id`) REFERENCES `routes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `chk_capacity_range` CHECK (`capacity` BETWEEN 10 AND 100),
  CONSTRAINT `chk_latitude_range` CHECK (`current_lat` BETWEEN -90 AND 90),
  CONSTRAINT `chk_longitude_range` CHECK (`current_lng` BETWEEN -180 AND 180)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 5. Table structure for table `bookings`
-- --------------------------------------------------------

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_id` varchar(20) NOT NULL UNIQUE,
  `user_id` int(11) NOT NULL,
  `bus_number` varchar(20) NOT NULL,
  `route` varchar(100) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `pickup_point` varchar(100) NOT NULL,
  `seat_number` varchar(10) NOT NULL,
  `status` enum('confirmed','cancelled','completed') DEFAULT 'confirmed',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  UNIQUE KEY `unique_active_booking` (`bus_number`,`date`,`time`,`seat_number`,`status`),
  CONSTRAINT `chk_future_date` CHECK (`date` >= curdate())
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 6. Table structure for table `reports`
-- --------------------------------------------------------

CREATE TABLE `reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `report_id` varchar(20) NOT NULL UNIQUE,
  `user_id` int(11) NOT NULL,
  `issue_type` varchar(50) NOT NULL,
  `priority` enum('low','medium','high','urgent') DEFAULT 'medium',
  `route` varchar(100) NOT NULL,
  `bus_number` varchar(20) DEFAULT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `location` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `contact_method` varchar(20) DEFAULT 'email',
  `anonymous` tinyint(1) DEFAULT 0,
  `status` enum('pending','in_progress','resolved','closed') DEFAULT 'pending',
  `resolution` text DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 7. Table structure for table `pickup_points`
-- --------------------------------------------------------

CREATE TABLE `pickup_points` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `point_name` varchar(100) NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `order_index` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 8. Table structure for table `schedules`
-- --------------------------------------------------------

CREATE TABLE `schedules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `route_id` int(11) NOT NULL,
  `departure_time` time NOT NULL,
  `days_active` varchar(50) DEFAULT 'Monday-Friday',
  PRIMARY KEY (`id`),
  FOREIGN KEY (`route_id`) REFERENCES `routes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 9. Table structure for table `notifications`
-- --------------------------------------------------------

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 10. Table structure for table `analytics`
-- --------------------------------------------------------

CREATE TABLE `analytics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `page` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Triggers
-- --------------------------------------------------------

DELIMITER //

-- 1. Prevent double booking trigger
CREATE TRIGGER `tr_prevent_double_booking` BEFORE INSERT ON `bookings`
FOR EACH ROW
BEGIN
    DECLARE booking_count INT;
    SELECT COUNT(*) INTO booking_count FROM `bookings`
    WHERE `bus_number` = NEW.`bus_number`
    AND `date` = NEW.`date`
    AND `time` = NEW.`time`
    AND `seat_number` = NEW.`seat_number`
    AND `status` = 'confirmed';
    
    IF booking_count > 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'This seat is already booked for this schedule';
    END IF;
END //

-- 2. Validate booking capacity trigger
CREATE TRIGGER `tr_validate_booking_capacity` BEFORE INSERT ON `bookings`
FOR EACH ROW
BEGIN
    DECLARE total_capacity INT;
    DECLARE current_bookings INT;
    
    SELECT `capacity` INTO total_capacity FROM `buses` WHERE `bus_number` = NEW.`bus_number`;
    SELECT COUNT(*) INTO current_bookings FROM `bookings`
    WHERE `bus_number` = NEW.`bus_number` AND `date` = NEW.`date` AND `time` = NEW.`time` AND `status` = 'confirmed';
    
    IF current_bookings >= total_capacity THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Bus capacity has been reached for this schedule';
    END IF;
END //

-- 3. Update bus location timestamp trigger
CREATE TRIGGER `tr_bus_location_update` BEFORE UPDATE ON `buses`
FOR EACH ROW
BEGIN
    IF NEW.`current_lat` != OLD.`current_lat` OR NEW.`current_lng` != OLD.`current_lng` THEN
        SET NEW.`last_update` = NOW();
    END IF;
END //

DELIMITER ;

-- --------------------------------------------------------
-- Sample Data
-- --------------------------------------------------------

-- Admin user (password: admin123)
INSERT INTO `users` (`name`, `username`, `email`, `phone`, `password`, `user_type`) VALUES
('Administrator', 'admin', 'admin@example.com', '+256700000001', '$2y$10$8WkZ9.j/hG6K0q1p2j.zOuS6qQvQ.5/8v7qW3K8R1E4C.pD2K8Y6.', 'admin');

-- Sample users (password: password)
INSERT INTO `users` (`name`, `username`, `email`, `phone`, `password`, `user_type`) VALUES
('John Doe', 'user101', 'john@example.com', '+256700000002', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user'),
('Jane Smith', 'user102', 'jane@example.com', '+256700000003', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user'),
('Robert Musiime', 'user103', 'robert@example.com', '+256700000004', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user');

-- Routes
INSERT INTO `routes` (`route_code`, `route_name`, `origin`, `destination`, `price`) VALUES
('RT001', 'Main Station to City Center', 'Main Station', 'City Center', 1000.00),
('RT002', 'City Center to Main Station', 'City Center', 'Main Station', 1000.00),
('RT003', 'Main Station to Suburbs', 'Main Station', 'Suburbs', 500.00);

-- Drivers
INSERT INTO `drivers` (`name`, `license_number`, `phone`, `status`) VALUES
('Samuel Mukasa', 'DL123456', '+256701123456', 'active'),
('Grace Nakato', 'DL654321', '+256701654321', 'active'),
('Peter Okello', 'DL987654', '+256701987654', 'active');

-- Buses
INSERT INTO `buses` (`bus_number`, `capacity`, `route_id`, `route_code`, `driver_id`, `status`, `current_lat`, `current_lng`) VALUES
('BUS001', 60, 1, 'RT001', 1, 'active', -0.60190000, 30.65740000),
('BUS002', 60, 2, 'RT002', 2, 'active', -0.61000000, 30.66000000),
('BUS003', 30, 3, 'RT003', 3, 'active', -0.60100000, 30.65900000);

COMMIT;
