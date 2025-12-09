-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 09, 2025 at 02:33 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pembayaran_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `anggota`
--

CREATE TABLE `anggota` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `nama` varchar(100) NOT NULL,
  `no_hp` varchar(30) NOT NULL,
  `angkatan` varchar(20) NOT NULL,
  `jabatan` varchar(50) NOT NULL,
  `divisi` varchar(50) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `anggota`
--

INSERT INTO `anggota` (`id`, `user_id`, `nama`, `no_hp`, `angkatan`, `jabatan`, `divisi`, `foto`, `created_at`) VALUES
(1, 4, 'nur aisyah', '081242913864', '2023', 'Anggota Humas', 'humas', 'uploads/anggota_4_1765216707.png', '2025-12-09 01:58:27');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `payment_date` date NOT NULL,
  `payment_type` varchar(100) NOT NULL,
  `bukti_tf` varchar(255) DEFAULT NULL,
  `status` enum('pending','paid','rejected') NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `user_id`, `amount`, `payment_date`, `payment_type`, `bukti_tf`, `status`, `created_at`) VALUES
(1, 4, 5000, '2025-12-08', 'uang iuran', NULL, 'paid', '2025-12-09 01:04:05'),
(2, 4, 50000, '2025-12-08', 'Uang Iuran', 'uploads/buktitf_4_1765217126.png', 'rejected', '2025-12-09 02:05:26');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `angkatan` varchar(20) DEFAULT NULL,
  `jabatan` enum('Ketua Himpunan','Wakil Ketua Himpunan','Bendahara','Sekertaris','Anggota Kaderisasi','Angota Kominfo','Anggota Humas','Anggota Keilmuan') DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','peserta') NOT NULL DEFAULT 'peserta',
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `angkatan`, `jabatan`, `username`, `email`, `password`, `role`, `is_verified`, `created_at`) VALUES
(1, 'Admin', NULL, NULL, 'admin', 'admin@example.com', 'admin123', 'admin', 1, '2025-12-08 23:37:04'),
(2, 'adlan khalid', NULL, NULL, 'user2', 'adlan@gmail.com', '$2y$10$jByR7zRzV8UetGSwQJYUt.jUnyXI5dJf9e.EDXacRoE7B03gsNqDG', 'peserta', 1, '2025-12-08 23:48:09'),
(3, 'Admin Yayaw', NULL, NULL, 'yayaw', 'yayaw@gmail.com', 'yayaw', 'admin', 1, '2025-12-09 00:52:24'),
(4, 'nur aisyah', '2023', 'Anggota Humas', 'aisyah', '', '$2y$10$ChTZze/iGclZlTVw2t53zurlUEnW4tAJiOmqPrM6xQ2fx0V4tV5IK', 'peserta', 1, '2025-12-09 01:02:57'),
(7, 'fauzi', '2023', 'Ketua Himpunan', 'uci', '', '$2y$10$vD2aeTC.OuKkfXH4toD4OOxcZFvdVmc4SwCJxWoKFGQ0Z764MnhEu', 'peserta', 1, '2025-12-09 02:08:06');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `anggota`
--
ALTER TABLE `anggota`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_anggota_user` (`user_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_payments_user` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `anggota`
--
ALTER TABLE `anggota`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `anggota`
--
ALTER TABLE `anggota`
  ADD CONSTRAINT `fk_anggota_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_payments_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
