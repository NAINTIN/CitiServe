-- Reset (optional for re-seeding during development)
DELETE FROM document_services;

INSERT INTO document_services (id, name, description, price, processing_time_days, is_active, created_at, updated_at) VALUES
(1, 'Barangay Clearance', 'Required for employment, travel, or transactions', 50.00, 1, 1, NOW(), NOW()),
(2, 'Certificate of Residency', 'Proof that resident lives in the barangay', 30.00, 1, 1, NOW(), NOW()),
(3, 'Certificate of Indigency', 'Certification for financial assistance, scholarships, or medical aid', 0.00, 0, 1, NOW(), NOW()),
(4, 'Barangay Business Clearance', 'Required for business permit application', 150.00, 3, 1, NOW(), NOW()),
(5, 'Barangay ID', 'Official identification for a resident', 75.00, 1, 1, NOW(), NOW()),
(6, 'Solo Parent Certificate', 'Certification for solo parent benefits', 50.00, 2, 1, NOW(), NOW()),
(7, 'Barangay Permit (Construction / Renovation)', 'Required before building/repair projects', 200.00, 3, 1, NOW(), NOW());