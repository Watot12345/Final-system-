-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 09, 2025 at 01:32 PM
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
-- Database: `siglatrolap_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `qr`
--

CREATE TABLE `qr` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `qr_secret` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','employee') NOT NULL DEFAULT 'employee',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `username` varchar(100) DEFAULT NULL,
  `user_id` int(100) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `check_in_time` time DEFAULT NULL,
  `check_out_time` time DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'On Time',
  `leave_type` varchar(50) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `stat` enum('pending','approved','rejected') DEFAULT 'pending',
  `qr_secret` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `role`, `created_at`, `username`, `user_id`, `date`, `check_in_time`, `check_out_time`, `status`, `leave_type`, `start_date`, `end_date`, `reason`, `stat`, `qr_secret`) VALUES
(39, 'admin', 'dmins@gmail.com', '$2y$10$DuZhv03BTmNiT5Hnukjko.bgkalBsjF8ems9qR5kBC57KdOjyk1Mu', 'admin', '2025-03-25 17:00:00', 'adminer', 0, NULL, NULL, NULL, 'On Time', '', '0000-00-00', '0000-00-00', '', 'rejected', NULL),
(40, 'admin2', 'admirers@gmail.com', '$2y$10$qfSB9nAIGsYmeTSaBKzuxOAw1JBLklTbCaI3LWZGzxx4ubbyp1jgu', 'admin', '2025-03-25 17:01:41', 'adminer2', 0, NULL, NULL, NULL, 'On Time', '', '0000-00-00', '0000-00-00', '', 'rejected', NULL),
(44, 'joshua sierra', 'joshuasierra725@gmail.com', '$2y$10$/u1U5g6G31qFya2votoYOuyaSuVXMIiXQyzhRtM31/gWIG5RmKrdu', 'employee', '2025-04-25 02:48:47', 'Josh', NULL, NULL, NULL, NULL, 'On Time', NULL, NULL, NULL, NULL, NULL, NULL),
(45, 'joshua sierra', NULL, '', 'employee', '2025-04-25 02:49:13', '', 44, '2025-04-25', '04:49:13', NULL, 'Under Time', NULL, NULL, NULL, NULL, 'pending', NULL),
(51, 'Joshua arncel2sdf', 'safaff@gam.com', '$2y$10$2YQlaJJdBHVfleuDCmnWd.GpMvIzp6wL2XfEjLxoTb8iqeh7fId9W', 'employee', '2025-05-06 17:37:47', 'joshpodikoooo', NULL, NULL, NULL, NULL, 'On Time', NULL, NULL, NULL, NULL, NULL, 'a54f2eb482117b65406a167087b92aba'),
(53, 'joshua qr', 'asierra389@gmail.com', '$2y$10$Gf7wuZaMg6oq4pVUJ14yS.JiopIcx6i.MD4bd6JiGYC5bvgniQuaC', 'employee', '2025-05-06 17:42:23', 'joshpodikooooo', NULL, NULL, NULL, NULL, 'On Time', NULL, NULL, NULL, NULL, NULL, '8015032ca6edfe3928707b1a948523fd'),
(54, 'joshua 1243f', 'joshua@gmail.com', '$2y$10$znP20Sqb6/D8u1s5aLAufO0h7b.1cpqU0ZqryLgZjooXGKoX1JiFq', 'employee', '2025-05-06 17:44:08', 'joshpodikooooory', NULL, NULL, NULL, NULL, 'On Time', NULL, NULL, NULL, NULL, NULL, 'cf16a683c0ad35e59fdd283464c139af'),
(55, 'Joshua arncel sierra', 'qwer@gmail.com', '$2y$10$YRCSeOJ5My/Wl4PM7GIJ3uv/qtRBnxYka9BoEO.20yrji1.jbIoke', 'employee', '2025-05-07 00:33:36', 'jush', NULL, NULL, NULL, NULL, 'On Time', NULL, NULL, NULL, NULL, NULL, '699d5cefcb393211544b96879527b9d2'),
(58, 'joshua 1243f', NULL, '', 'employee', '2025-05-07 07:05:46', NULL, 54, '2025-05-07', '09:05:46', '09:06:32', 'Late', NULL, NULL, NULL, NULL, 'pending', NULL),
(59, 'joshua 1243f', NULL, '', 'employee', '2025-05-08 14:42:20', NULL, 54, '2025-05-08', '16:42:20', '16:43:04', 'Late', NULL, NULL, NULL, NULL, 'pending', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `qr`
--
ALTER TABLE `qr`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `qr`
--
ALTER TABLE `qr`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
