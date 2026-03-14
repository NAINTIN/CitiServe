ALTER TABLE complaints
MODIFY COLUMN status ENUM('received','processing','rejected','resolved')
NOT NULL DEFAULT 'received';