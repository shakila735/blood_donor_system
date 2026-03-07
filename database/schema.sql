CREATE DATABASE IF NOT EXISTS blood_donor_system;
USE blood_donor_system;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL UNIQUE,
    email VARCHAR(100) DEFAULT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('donor','requester','hospital','admin') NOT NULL,
    blood_group ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-') DEFAULT NULL,
    location VARCHAR(100) NOT NULL,
    availability_status ENUM('Available','Unavailable') DEFAULT 'Available',
    is_verified TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE blood_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    posted_by_user_id INT NOT NULL,
    blood_group ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-') NOT NULL,
    location VARCHAR(100) NOT NULL,
    needed_date DATE NOT NULL,
    units_needed INT DEFAULT 1,
    contact_phone VARCHAR(20) NOT NULL,
    details TEXT,
    status ENUM('Pending','Accepted','Completed','Cancelled') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (posted_by_user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

INSERT INTO users (name, phone, email, password, role, blood_group, location, availability_status, is_verified, is_active)
VALUES
('System Admin', '01700000000', 'admin@blood.com', '$2y$10$Q0r4Y9w8aV0oYVvD3R4gxOtSQfVqWwzYF5gFNmAqgD5w7G3M3yD3K', 'admin', NULL, 'Dhaka', 'Available', 1, 1);