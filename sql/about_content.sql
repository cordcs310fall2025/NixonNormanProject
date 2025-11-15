-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 10, 2025 at 03:28 AM
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
-- Database: `nixon_norman_media`
--

-- --------------------------------------------------------

--
-- Table structure for table `about_content`
--

CREATE TABLE `about_content` (
  `id` int(11) NOT NULL,
  `headline` varchar(255) NOT NULL DEFAULT 'About Nixon Norman Media',
  `intro` text DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `mission` text DEFAULT NULL,
  `experience_years` varchar(10) DEFAULT '5+',
  `projects_completed` varchar(10) DEFAULT '150+',
  `happy_clients` varchar(10) DEFAULT '50+',
  `services` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`services`)),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `about_content`
--

INSERT INTO `about_content` (`id`, `headline`, `intro`, `bio`, `mission`, `experience_years`, `projects_completed`, `happy_clients`, `services`, `updated_at`) VALUES
(1, 'About Nixon Norman Media', 'Capturing moments that matter through the lens of creativity and passion.', 'Nixon Norman Media specializes in professional photography and videography services. With years of experience in automotive, commercial, and event photography, we bring your vision to life with stunning visual storytelling.', 'Our mission is to deliver exceptional visual content that exceeds expectations and creates lasting impressions.', '5+', '50+', '50+', '[\"Commercial Photography\", \"Event Photography\", \"Automotive Photography\", \"Video Production\", \"Social Media Content\"]', '2025-11-08 17:54:12');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `about_content`
--
ALTER TABLE `about_content`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `about_content`
--
ALTER TABLE `about_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
