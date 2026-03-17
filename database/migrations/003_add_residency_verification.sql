-- Add resident verification workflow fields to users table
ALTER TABLE `users`
  ADD COLUMN `is_verified` tinyint(1) NOT NULL DEFAULT 0 AFTER `contact_number`,
  ADD COLUMN `residency_verification_status` enum('not_submitted','pending','approved','rejected') NOT NULL DEFAULT 'not_submitted' AFTER `is_verified`,
  ADD COLUMN `residency_proof_path` varchar(255) DEFAULT NULL AFTER `residency_verification_status`;

-- Keep staff/admin usable after migration
UPDATE `users`
SET `is_verified` = 1, `residency_verification_status` = 'approved'
WHERE `role` IN ('staff', 'admin');
