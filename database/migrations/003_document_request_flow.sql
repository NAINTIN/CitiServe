-- Multi-step document request flow schema updates
ALTER TABLE document_requests
    ADD COLUMN payment_method varchar(100) DEFAULT NULL AFTER status,
    ADD COLUMN form_data_json longtext DEFAULT NULL AFTER payment_proof_path;

CREATE TABLE IF NOT EXISTS document_request_files (
    id int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    document_request_id int(10) UNSIGNED NOT NULL,
    file_type varchar(100) NOT NULL,
    file_path varchar(255) NOT NULL,
    original_name varchar(255) DEFAULT NULL,
    uploaded_at timestamp NULL DEFAULT current_timestamp(),
    PRIMARY KEY (id),
    KEY idx_document_request_files_request_id (document_request_id),
    CONSTRAINT fk_document_request_files_request
        FOREIGN KEY (document_request_id) REFERENCES document_requests (id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Align document services with supported multi-step forms
DELETE FROM document_services;
INSERT INTO document_services (id, name, description, price, processing_time_days, is_active, created_at, updated_at) VALUES
(1, 'Barangay Business Clearance', 'Required for business permit processing', 150.00, 3, 1, NOW(), NOW()),
(2, 'Barangay Clearance', 'General barangay clearance', 50.00, 1, 1, NOW(), NOW()),
(3, 'Barangay ID', 'Official barangay resident ID', 75.00, 1, 1, NOW(), NOW()),
(4, 'Barangay Permit (Construction)', 'Permit for construction and related activities', 200.00, 3, 1, NOW(), NOW()),
(5, 'Certificate of Indigency', 'Certification for assistance and support programs', 0.00, 1, 1, NOW(), NOW()),
(6, 'Certificate of Residency', 'Proof of barangay residency', 30.00, 1, 1, NOW(), NOW()),
(7, 'Solo Parent Certificate', 'Certification for solo parent benefits', 50.00, 2, 1, NOW(), NOW());
