-- database/phase4_admin_updates.sql
USE bloodnet_db;

-- 1. Add is_verified to users table to support admin verification of donors
ALTER TABLE `users`
ADD COLUMN `is_verified` TINYINT(1) DEFAULT 0 AFTER `is_active`;
