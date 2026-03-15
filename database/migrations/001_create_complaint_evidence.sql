-- Migration: Add complaint_evidence table
-- This table was missing from the original schema and is required
-- for complaint evidence file uploads and for the admin complaints page.

CREATE TABLE IF NOT EXISTS `complaint_evidence` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `complaint_id` int(10) UNSIGNED NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `uploaded_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_complaint_evidence_complaint` (`complaint_id`),
  CONSTRAINT `fk_complaint_evidence_complaint` FOREIGN KEY (`complaint_id`) REFERENCES `complaints` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
