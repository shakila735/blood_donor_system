CREATE DATABASE IF NOT EXISTS bloodnet_db;
USE bloodnet_db;

CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL UNIQUE,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `roles` (`name`) VALUES 
('admin'), 
('donor'), 
('requester'), 
('hospital') 
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL UNIQUE,
  `email` varchar(100) NULL UNIQUE,
  `password_hash` varchar(255) NOT NULL,
  `blood_group` varchar(5) NULL,
  `location` varchar(255) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Password for admin is 'adminpassword'
-- Hash created using: password_hash('adminpassword', PASSWORD_DEFAULT)
INSERT INTO `users` (`role_id`, `name`, `phone`, `email`, `password_hash`, `blood_group`, `location`, `is_active`) 
VALUES (
    (SELECT `id` FROM `roles` WHERE `name`='admin'),
    'Super Admin',
    '01700000000',
    'admin@bloodnet.local',
    '$2y$10$jydS3zANkWkhs2gp4wZ9me.sRQzInj6gxiPSQ/v0es.rik2EYRl2y',
    NULL,
    'Dhaka, Bangladesh',
    1
) ON DUPLICATE KEY UPDATE `name`=`name`;
