-- Assumes table: document_requirements(document_service_id, name, description, is_required, created_at, updated_at)

-- Reset (optional)
DELETE FROM document_requirements;

-- 1 Barangay Clearance
INSERT INTO document_requirements (document_service_id, name, description, is_required, created_at, updated_at) VALUES
(1, 'Valid ID', 'Any government-issued valid ID', 1, NOW(), NOW());

-- 2 Certificate of Residency
INSERT INTO document_requirements (document_service_id, name, description, is_required, created_at, updated_at) VALUES
(2, 'Valid ID', 'Any government-issued valid ID', 1, NOW(), NOW()),
(2, 'Proof of Address', 'Utility bill or Barangay ID showing local address', 1, NOW(), NOW());

-- 3 Certificate of Indigency
INSERT INTO document_requirements (document_service_id, name, description, is_required, created_at, updated_at) VALUES
(3, 'Valid ID', 'Any government-issued valid ID', 1, NOW(), NOW());

-- 4 Barangay Business Clearance
INSERT INTO document_requirements (document_service_id, name, description, is_required, created_at, updated_at) VALUES
(4, 'DTI/Business Registration', 'Business registration document', 1, NOW(), NOW()),
(4, 'Valid ID', 'Owner valid ID', 1, NOW(), NOW()),
(4, 'Business Location Info', 'Complete business address/location', 1, NOW(), NOW());

-- 5 Barangay ID
INSERT INTO document_requirements (document_service_id, name, description, is_required, created_at, updated_at) VALUES
(5, 'Valid ID', 'Any government-issued valid ID', 1, NOW(), NOW()),
(5, '1x1 or 2x2 Photo', 'Recent ID picture', 1, NOW(), NOW());

-- 6 Solo Parent Certificate
INSERT INTO document_requirements (document_service_id, name, description, is_required, created_at, updated_at) VALUES
(6, 'Valid ID', 'Any government-issued valid ID', 1, NOW(), NOW()),
(6, 'Birth Certificate of Child', 'Birth certificate as supporting document', 1, NOW(), NOW()),
(6, 'Proof of Solo Parent Status', 'Legal or supporting proof of solo parent status', 1, NOW(), NOW());

-- 7 Barangay Permit (Construction / Renovation)
INSERT INTO document_requirements (document_service_id, name, description, is_required, created_at, updated_at) VALUES
(7, 'Construction Plan', 'Basic construction/renovation plan', 1, NOW(), NOW()),
(7, 'Valid ID', 'Applicant valid ID', 1, NOW(), NOW());