-- Reset (optional for re-seeding during development)
DELETE FROM document_services;

INSERT INTO document_services (id, name, description, price, processing_time_days, is_active, created_at, updated_at) VALUES
(1, 'Barangay Business Clearance', 'Required for business permit processing', 150.00, 3, 1, NOW(), NOW()),
(2, 'Barangay Clearance', 'General barangay clearance', 50.00, 1, 1, NOW(), NOW()),
(3, 'Barangay ID', 'Official barangay resident ID', 75.00, 1, 1, NOW(), NOW()),
(4, 'Barangay Permit (Construction)', 'Permit for construction and related activities', 200.00, 3, 1, NOW(), NOW()),
(5, 'Certificate of Indigency', 'Certification for assistance and support programs', 0.00, 1, 1, NOW(), NOW()),
(6, 'Certificate of Residency', 'Proof of barangay residency', 30.00, 1, 1, NOW(), NOW()),
(7, 'Solo Parent Certificate', 'Certification for solo parent benefits', 50.00, 2, 1, NOW(), NOW());
