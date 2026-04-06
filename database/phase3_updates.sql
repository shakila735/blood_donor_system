-- database/phase3_updates.sql
USE bloodnet_db;

-- 1. Add availability_status to users table
ALTER TABLE `users`
ADD COLUMN `availability_status` ENUM('Available', 'Unavailable') DEFAULT 'Available' AFTER `is_active`;

-- 2. Create blood_requests table
CREATE TABLE IF NOT EXISTS `blood_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `blood_group` varchar(5) NOT NULL,
  `location` varchar(255) NOT NULL,
  `needed_date` date NOT NULL,
  `contact_info` varchar(100) NOT NULL,
  `note` text NULL,
  `status` enum('Pending','Accepted','Completed','Cancelled') DEFAULT 'Pending',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
