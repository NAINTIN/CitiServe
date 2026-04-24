-- User verification columns
ALTER TABLE users
    ADD COLUMN IF NOT EXISTS is_verified tinyint(1) NOT NULL DEFAULT 1,
    ADD COLUMN IF NOT EXISTS proof_of_id varchar(255) DEFAULT NULL;

-- Keep existing users verified so current records are unaffected
UPDATE users
SET is_verified = 1
WHERE is_verified IS NULL OR is_verified <> 1;
