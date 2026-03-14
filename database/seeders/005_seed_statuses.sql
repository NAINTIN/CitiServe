-- If you have a statuses table, use this.
-- If you DO NOT have a statuses table (because you use ENUM), skip this file.

-- Example schema expected:
-- statuses(id, module, code, label, sort_order, is_active)

-- Optional reset
DELETE FROM statuses;

-- Document Request statuses
INSERT INTO statuses (module, code, label, sort_order, is_active) VALUES
('document_request', 'received',  'Received',  1, 1),
('document_request', 'pending',   'Pending',   2, 1),
('document_request', 'claimable', 'Claimable', 3, 1),
('document_request', 'rejected',  'Rejected',  4, 1),
('document_request', 'released',  'Released',  5, 1);

-- Complaint statuses
INSERT INTO statuses (module, code, label, sort_order, is_active) VALUES
('complaint', 'received',   'Received',   1, 1),
('complaint', 'processing', 'Processing', 2, 1),
('complaint', 'rejected',   'Rejected',   3, 1),
('complaint', 'resolved',   'Resolved',   4, 1);