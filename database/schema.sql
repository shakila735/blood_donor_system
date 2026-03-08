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

CREATE TABLE IF NOT EXISTS blood_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    requester_role ENUM('requester', 'hospital') NOT NULL,
    patient_name VARCHAR(100) NOT NULL,
    blood_group VARCHAR(5) NOT NULL,
    units_needed INT NOT NULL,
    needed_date DATE NOT NULL,
    location VARCHAR(150) NOT NULL,
    hospital_name VARCHAR(150) DEFAULT NULL,
    contact_phone VARCHAR(20) NOT NULL,
    details TEXT,
    status ENUM('open', 'fulfilled', 'cancelled') DEFAULT 'open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);