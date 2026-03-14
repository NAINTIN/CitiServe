-- Reset (optional)
DELETE FROM complaint_categories;

INSERT INTO complaint_categories (id, name, description, is_active) VALUES
(1, 'Road/Infrastructure', 'Potholes, broken streetlights, damaged sidewalks', 1),
(2, 'Garbage/Sanitation', 'Uncollected trash, illegal dumping, drainage issues', 1),
(3, 'Noise Disturbance', 'Loud parties, construction noise, barking dogs', 1),
(4, 'Health/Sanitation', 'Unsanitary establishments, public health hazards', 1),
(5, 'Traffic/Parking', 'Illegal parking, traffic violations, blocked roads', 1),
(6, 'Public Safety/Security', 'Theft, vandalism, suspicious activities', 1),
(7, 'Environmental/Tree/Animal Concerns', 'Fallen trees, stray animals, pollution', 1),
(8, 'Water/Electricity/Utilities', 'Water leaks, power outages, utility issues', 1),
(9, 'Community/Social Issues', 'Disputes between neighbors, harassment', 1),
(10, 'Other', 'Any complaint not covered by the above', 1);