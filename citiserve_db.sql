-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 14, 2026 at 11:09 AM
-- Server version: 12.2.2-MariaDB
-- PHP Version: 8.5.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `citiserve_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `complaints`
--

CREATE TABLE `complaints` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `category_id` int(10) UNSIGNED NOT NULL,
  `is_anonymous` tinyint(1) NOT NULL DEFAULT 0,
  `title` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `status` enum('submitted','under_review','in_progress','resolved','rejected') NOT NULL DEFAULT 'submitted',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `complaints`
--

INSERT INTO `complaints` (`id`, `user_id`, `category_id`, `is_anonymous`, `title`, `description`, `location`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 9, 0, 'asdsad', 'adsadasd', NULL, 'submitted', '2026-03-14 09:31:03', '2026-03-14 09:31:03'),
(2, 1, 3, 0, 'Manong', 'Ang ingay ni manong kainis', 'block 1 new zealand canada.usa mars jupiter', 'submitted', '2026-03-14 09:31:45', '2026-03-14 09:31:45'),
(3, 1, 7, 0, 'madumi', 'ang dumi', NULL, 'submitted', '2026-03-14 09:58:54', '2026-03-14 09:58:54'),
(4, 1, 7, 0, 'adsads', 'asdadsadas', NULL, 'submitted', '2026-03-14 10:56:32', '2026-03-14 10:56:32'),
(5, 1, 9, 0, 'asdsad', 'asdsadsa', NULL, 'submitted', '2026-03-14 10:59:31', '2026-03-14 10:59:31'),
(6, 1, 9, 0, 'sadsad', 'adsadsa', NULL, 'submitted', '2026-03-14 10:59:50', '2026-03-14 10:59:50'),
(7, 1, 9, 0, 'sads', 'adsadsad', NULL, 'submitted', '2026-03-14 10:59:58', '2026-03-14 10:59:58');

-- --------------------------------------------------------

--
-- Table structure for table `complaint_evidence`
--

CREATE TABLE `complaint_evidence` (
  `id` int(10) UNSIGNED NOT NULL,
  `complaint_id` int(10) UNSIGNED NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `uploaded_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `complaint_categories`
--

CREATE TABLE `complaint_categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `complaint_categories`
--

INSERT INTO `complaint_categories` (`id`, `name`, `description`, `is_active`) VALUES
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

-- --------------------------------------------------------

--
-- Table structure for table `document_requests`
--

CREATE TABLE `document_requests` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `document_service_id` int(10) UNSIGNED NOT NULL,
  `purpose` text DEFAULT NULL,
  `status` enum('received','pending','claimable','rejected','released') NOT NULL DEFAULT 'received',
  `payment_reference` varchar(100) DEFAULT NULL,
  `payment_proof_path` varchar(255) DEFAULT NULL,
  `claimed_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `document_requests`
--

INSERT INTO `document_requests` (`id`, `user_id`, `document_service_id`, `purpose`, `status`, `payment_reference`, `payment_proof_path`, `claimed_at`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'maingay kapitbahay', 'claimable', NULL, NULL, NULL, '2026-03-14 08:54:24', '2026-03-14 10:25:43'),
(2, 3, 3, 'asdsadsad', 'received', NULL, 'uploads/payment_proofs/69d887c73e6ae864e25822be32fc04e2.jpg', NULL, '2026-03-14 10:34:03', '2026-03-14 10:34:03'),
(3, 1, 3, 'safsaf', 'received', NULL, 'uploads/payment_proofs/badc990a15872a38d89229edb3a7fac4.jpg', NULL, '2026-03-14 10:58:13', '2026-03-14 10:58:13');

-- --------------------------------------------------------

--
-- Table structure for table `document_services`
--

CREATE TABLE `document_services` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `processing_time_days` int(11) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `document_services`
--

INSERT INTO `document_services` (`id`, `name`, `description`, `price`, `processing_time_days`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Barangay Clearance', 'General clearance', 50.00, 1, 1, '2026-03-14 08:53:18', '2026-03-14 08:53:18'),
(2, 'Certificate of Residency', 'Proof of residency', 30.00, 1, 1, '2026-03-14 08:53:18', '2026-03-14 08:53:18'),
(3, 'Business Permit Endorsement', 'For permit processing', 100.00, 3, 1, '2026-03-14 08:53:18', '2026-03-14 08:53:18');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `link`, `is_read`, `created_at`) VALUES
(1, 1, 'Document Request Update', 'Your request #1 status is now \'claimable\'.', '/CitiServe/public/my_requests.php', 1, '2026-03-14 10:25:43');

-- --------------------------------------------------------

--
-- Table structure for table `status_history`
--

CREATE TABLE `status_history` (
  `id` int(10) UNSIGNED NOT NULL,
  `entity_type` enum('document_request','complaint') NOT NULL,
  `entity_id` int(10) UNSIGNED NOT NULL,
  `old_status` varchar(50) NOT NULL,
  `new_status` varchar(50) NOT NULL,
  `changed_by` int(10) UNSIGNED NOT NULL,
  `notes` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `status_history`
--

INSERT INTO `status_history` (`id`, `entity_type`, `entity_id`, `old_status`, `new_status`, `changed_by`, `notes`, `created_at`) VALUES
(1, 'document_request', 1, 'received', 'claimable', 3, NULL, '2026-03-14 10:25:43');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('resident','staff','admin') NOT NULL DEFAULT 'resident',
  `address` text DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password_hash`, `role`, `address`, `contact_number`, `created_at`, `updated_at`) VALUES
(1, 'Dan Deniel V. Belaro', 'danndenielbelaro@gmail.com', '$2y$12$UegDuWyBvBpUG/hVL4LsPeFOCQY9FbtKdlqsvg6KP/dwXWxQ/zpUK', 'resident', 'blok 1 lot 4', '945855940404044', '2026-03-14 07:58:33', '2026-03-14 09:57:10'),
(2, 'testing the database', 'thechosenone@gmail.com', '$2y$12$VXWioTizKE5Vz7PTG378Yuxm/TcwsEPnXZfbOJEUR2lI.2JWDTG2G', 'resident', NULL, NULL, '2026-03-14 08:18:38', '2026-03-14 08:18:38'),
(3, 'admin_citi', 'admin@citiserve.local', '$2y$12$HLaUZerW/3VWIB9GqJzGwuNYf2Gdyq6sbXt7KmhhZu/tpgDh0MOiy', 'admin', NULL, NULL, '2026-03-14 08:56:45', '2026-03-14 08:57:44');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `complaints`
--
ALTER TABLE `complaints`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_complaints_user` (`user_id`),
  ADD KEY `fk_complaints_category` (`category_id`);

--
-- Indexes for table `complaint_categories`
--
ALTER TABLE `complaint_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `complaint_evidence`
--
ALTER TABLE `complaint_evidence`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_complaint_evidence_complaint` (`complaint_id`);

--
-- Indexes for table `document_requests`
--
ALTER TABLE `document_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_document_requests_user` (`user_id`),
  ADD KEY `fk_document_requests_service` (`document_service_id`);

--
-- Indexes for table `document_services`
--
ALTER TABLE `document_services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_notifications_user_id` (`user_id`);

--
-- Indexes for table `status_history`
--
ALTER TABLE `status_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status_history_entity` (`entity_type`,`entity_id`),
  ADD KEY `idx_status_history_changed_by` (`changed_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `complaints`
--
ALTER TABLE `complaints`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `complaint_categories`
--
ALTER TABLE `complaint_categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `complaint_evidence`
--
ALTER TABLE `complaint_evidence`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `document_requests`
--
ALTER TABLE `document_requests`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `document_services`
--
ALTER TABLE `document_services`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `status_history`
--
ALTER TABLE `status_history`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `complaints`
--
ALTER TABLE `complaints`
  ADD CONSTRAINT `fk_complaints_category` FOREIGN KEY (`category_id`) REFERENCES `complaint_categories` (`id`),
  ADD CONSTRAINT `fk_complaints_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `complaint_evidence`
--
ALTER TABLE `complaint_evidence`
  ADD CONSTRAINT `fk_complaint_evidence_complaint` FOREIGN KEY (`complaint_id`) REFERENCES `complaints` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `document_requests`
--
ALTER TABLE `document_requests`
  ADD CONSTRAINT `fk_document_requests_service` FOREIGN KEY (`document_service_id`) REFERENCES `document_services` (`id`),
  ADD CONSTRAINT `fk_document_requests_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `status_history`
--
ALTER TABLE `status_history`
  ADD CONSTRAINT `fk_status_history_changed_by` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
