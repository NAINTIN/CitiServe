-- Optional helper seeder if you're using ENUM role in users table.
-- Creates one admin and one staff account.
-- IMPORTANT: Replace password hashes with hashes generated on your machine.

-- Example quick hash command:
-- php -r "echo password_hash('admin123', PASSWORD_DEFAULT) . PHP_EOL;"

INSERT INTO users (full_name, email, password_hash, role, created_at, updated_at)
VALUES
('System Admin', 'admin@citiserve.local', '$2y$10$REPLACE_WITH_REAL_HASH', 'admin', NOW(), NOW()),
('Barangay Staff', 'staff@citiserve.local', '$2y$10$REPLACE_WITH_REAL_HASH', 'staff', NOW(), NOW())
ON DUPLICATE KEY UPDATE role = VALUES(role), updated_at = NOW();