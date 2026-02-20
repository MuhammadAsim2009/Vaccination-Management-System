-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 20, 2026 at 12:43 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `vaccination_management_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `hospital_id` int(11) NOT NULL,
  `child_id` int(11) NOT NULL,
  `vaccine_id` int(11) NOT NULL,
  `dose_number` int(11) DEFAULT NULL,
  `appointment_date` datetime NOT NULL,
  `status` enum('requested','approved','rejected','completed') NOT NULL DEFAULT 'requested',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `parent_id`, `hospital_id`, `child_id`, `vaccine_id`, `dose_number`, `appointment_date`, `status`, `created_at`) VALUES
(3, 2, 1, 2, 1, 1, '2026-02-16 11:00:00', 'completed', '2026-02-14 20:21:48'),
(4, 2, 1, 2, 1, 2, '2026-02-17 14:00:00', 'completed', '2026-02-14 22:32:29'),
(5, 2, 1, 2, 1, 3, '2026-02-17 11:00:00', 'completed', '2026-02-17 05:33:18');

-- --------------------------------------------------------

--
-- Table structure for table `children`
--

CREATE TABLE `children` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `date_of_birth` date NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `blood_group` varchar(5) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `children`
--

INSERT INTO `children` (`id`, `parent_id`, `name`, `date_of_birth`, `gender`, `blood_group`, `created_at`) VALUES
(1, 6, 'Bilawal Ali', '2001-03-15', 'Male', 'O+', '2026-02-12 19:37:29'),
(2, 4, 'Sahib', '2007-11-27', 'Male', 'O+', '2026-02-12 19:41:20');

-- --------------------------------------------------------

--
-- Table structure for table `hospitals`
--

CREATE TABLE `hospitals` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `hospital_name` varchar(150) NOT NULL,
  `registration_no` varchar(50) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `status` enum('approved','pending','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hospitals`
--

INSERT INTO `hospitals` (`id`, `user_id`, `hospital_name`, `registration_no`, `phone`, `address`, `status`, `created_at`) VALUES
(1, 3, 'City Hospital', 'REG-1001', '03073469181', 'Sachal Colony, Larkana', 'approved', '2026-02-03 04:06:32'),
(2, 5, 'Ali Hospital', 'REG-1002', '03337559726', 'Wagan Road, Larkana', 'approved', '2026-02-03 06:45:45'),
(4, 8, 'National Hospital', 'REG-1003', '03115873912', 'Near Degree College, Nazar Muhalla, Larkana', 'approved', '2026-02-17 13:39:48');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_type` enum('admin','hospital','parent') NOT NULL,
  `recipient_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `type` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `status` enum('unread','read') NOT NULL DEFAULT 'unread',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `parents`
--

CREATE TABLE `parents` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parents`
--

INSERT INTO `parents` (`id`, `user_id`, `phone`, `address`, `created_at`) VALUES
(1, 2, '03183433480', 'Near Degree College, Nazar Muhalla, Larkana', '2026-02-03 04:06:32'),
(2, 4, '03228300637', 'Near Szabait, Sachal Colony, Larkana', '2026-02-03 05:53:46'),
(3, 6, '03052820479', 'Near National Bank, Bakirani Road, Larkana', '2026-02-04 18:30:22');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','parent','hospital') NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `status`, `created_at`) VALUES
(1, 'Muhammad Asim', 'sasim4589@gmail.com', '$2y$10$RAvdxKGHjYDZZZ2HR0GSOe0g0jSg6d.rF7w4/KFHpSU6JLyRMh9zW', 'admin', 'active', '2026-02-03 04:06:32'),
(2, 'Faseeh', 'faseeh@gmail.com', '$2y$10$bfm4vMDA9GS2uvw08.3bJeu/L6rcCOlhzo2iTS5kZF9qkQNN9Z2QW', 'parent', 'active', '2026-02-03 04:06:32'),
(3, 'City Hospital', 'cityhospital@gmail.com', '$2y$10$.GGC.mT8iWRYkYpuWJ7cmekUQihYUEjnS4jJcT2iU7.CA9nSnY8n2', 'hospital', 'active', '2026-02-03 04:06:32'),
(4, 'Furqan', 'furqan@gmail.com', '$2y$10$gbQLxBtNdPD1B.EcYT3dE.dcJ0k9LSQgt.Mt/bOw1hHSpZ0Wy/0p.', 'parent', 'active', '2026-02-03 05:53:46'),
(5, 'Ali Hospital', 'alihospital@gmail.com', '$2y$10$HrlNURnxhLbec75l3TKwKOFsSMl140whJwOG/mEzGdina2sYbi.Ne', 'hospital', 'active', '2026-02-03 06:45:45'),
(6, 'Azam', 'azam@gmail.com', '$2y$10$VEjywJWwwZc7NxaG071z4OVQlnh7F9slW1qgeTSfDCgx2qUGuBWPS', 'parent', 'active', '2026-02-04 18:30:22'),
(8, 'National Hospital', 'nationalhospital@gmail.com', '$2y$10$/e5fAGrEKq2HsEvRUzInEu8ogn4bbGGOlS2DwqhumtGznTWjEh6DG', 'hospital', 'active', '2026-02-17 13:39:48');

-- --------------------------------------------------------

--
-- Table structure for table `vaccination_records`
--

CREATE TABLE `vaccination_records` (
  `id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `vaccinated_date` date NOT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vaccination_records`
--

INSERT INTO `vaccination_records` (`id`, `appointment_id`, `vaccinated_date`, `remarks`, `created_at`) VALUES
(1, 3, '2026-02-16', 'The vaccine was given successfully on time.', '2026-02-17 05:14:53'),
(2, 4, '2026-02-17', 'The vaccine was given successfully on time.', '2026-02-17 05:18:51'),
(3, 5, '2026-02-17', 'The vaccine was given successfully on proper time.', '2026-02-17 05:41:46');

-- --------------------------------------------------------

--
-- Table structure for table `vaccination_schedule`
--

CREATE TABLE `vaccination_schedule` (
  `id` int(11) NOT NULL,
  `child_id` int(11) NOT NULL,
  `vaccine_id` int(11) NOT NULL,
  `hospital_id` int(11) DEFAULT NULL,
  `scheduled_date` datetime NOT NULL,
  `dose_number` int(11) NOT NULL,
  `status` enum('pending','vaccinated','missed') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vaccination_schedule`
--

INSERT INTO `vaccination_schedule` (`id`, `child_id`, `vaccine_id`, `hospital_id`, `scheduled_date`, `dose_number`, `status`, `created_at`) VALUES
(3, 2, 1, 1, '2026-02-17 14:00:00', 2, 'vaccinated', '2026-02-15 16:46:55'),
(4, 2, 1, 1, '2026-02-16 11:00:00', 1, 'vaccinated', '2026-02-16 11:26:06'),
(5, 2, 1, 1, '2026-02-17 11:00:00', 3, 'vaccinated', '2026-02-17 05:33:40');

-- --------------------------------------------------------

--
-- Table structure for table `vaccines`
--

CREATE TABLE `vaccines` (
  `id` int(11) NOT NULL,
  `vaccine_name` varchar(100) NOT NULL,
  `total_dose` int(11) NOT NULL,
  `target_age_group` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `availability_status` enum('available','unavailable') NOT NULL DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vaccines`
--

INSERT INTO `vaccines` (`id`, `vaccine_name`, `total_dose`, `target_age_group`, `description`, `availability_status`, `created_at`) VALUES
(1, 'Polio', 4, '0–5 years', 'Protects against poliovirus.', 'available', '2026-02-03 04:06:32'),
(2, 'Hepatitis B', 3, '0–12 months', 'Protects against Hepatitis B virus. ', 'available', '2026-02-03 04:06:32'),
(5, 'Measles', 2, '9 months–5 years', 'Protects against measles infection.', 'available', '2026-02-03 04:06:32'),
(6, 'Diphtheria', 3, '0–6 years', 'Protects against diphtheria bacterial infection.', 'available', '2026-02-13 21:39:41');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `hospital_id` (`hospital_id`),
  ADD KEY `child_id` (`child_id`),
  ADD KEY `vaccine_id` (`vaccine_id`);

--
-- Indexes for table `children`
--
ALTER TABLE `children`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `hospitals`
--
ALTER TABLE `hospitals`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `registration_no` (`registration_no`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user` (`user_id`),
  ADD KEY `recipient_idx` (`user_type`,`recipient_id`);

--
-- Indexes for table `parents`
--
ALTER TABLE `parents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `vaccination_records`
--
ALTER TABLE `vaccination_records`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `appointment_id` (`appointment_id`);

--
-- Indexes for table `vaccination_schedule`
--
ALTER TABLE `vaccination_schedule`
  ADD PRIMARY KEY (`id`),
  ADD KEY `child_id` (`child_id`),
  ADD KEY `vaccine_id` (`vaccine_id`),
  ADD KEY `fk_schedule_hospital` (`hospital_id`);

--
-- Indexes for table `vaccines`
--
ALTER TABLE `vaccines`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `children`
--
ALTER TABLE `children`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `hospitals`
--
ALTER TABLE `hospitals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `parents`
--
ALTER TABLE `parents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `vaccination_records`
--
ALTER TABLE `vaccination_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `vaccination_schedule`
--
ALTER TABLE `vaccination_schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `vaccines`
--
ALTER TABLE `vaccines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `parents` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`hospital_id`) REFERENCES `hospitals` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_3` FOREIGN KEY (`child_id`) REFERENCES `children` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_4` FOREIGN KEY (`vaccine_id`) REFERENCES `vaccines` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `children`
--
ALTER TABLE `children`
  ADD CONSTRAINT `children_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hospitals`
--
ALTER TABLE `hospitals`
  ADD CONSTRAINT `hospitals_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `parents`
--
ALTER TABLE `parents`
  ADD CONSTRAINT `parents_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vaccination_records`
--
ALTER TABLE `vaccination_records`
  ADD CONSTRAINT `vaccination_records_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vaccination_schedule`
--
ALTER TABLE `vaccination_schedule`
  ADD CONSTRAINT `fk_schedule_hospital` FOREIGN KEY (`hospital_id`) REFERENCES `hospitals` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `vaccination_schedule_ibfk_1` FOREIGN KEY (`child_id`) REFERENCES `children` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vaccination_schedule_ibfk_2` FOREIGN KEY (`vaccine_id`) REFERENCES `vaccines` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
