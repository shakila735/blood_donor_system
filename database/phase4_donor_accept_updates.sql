-- Phase 4: Donor Request Acceptance Updates
-- This script adds the necessary columns to the blood_requests table to track which donor accepts a request.

USE bloodnet_db;

-- Add donor_id and accepted_at columns to blood_requests
ALTER TABLE `blood_requests`
ADD COLUMN `donor_id` INT(11) NULL AFTER `user_id`,
ADD COLUMN `accepted_at` TIMESTAMP NULL DEFAULT NULL AFTER `status`;

-- Add foreign key constraint for donor_id
ALTER TABLE `blood_requests`
ADD CONSTRAINT `fk_blood_requests_donor`
FOREIGN KEY (`donor_id`) REFERENCES `users`(`id`)
ON DELETE SET NULL;
