-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 22, 2024 at 09:01 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rambert_membres`
--

-- --------------------------------------------------------

--
-- Table structure for table `home`
--

CREATE TABLE `home` (
  `id` int UNSIGNED NOT NULL,
  `address_title` varchar(100) DEFAULT NULL,
  `address_name` varchar(100) DEFAULT NULL,
  `address_line_1` varchar(100) DEFAULT NULL,
  `address_line_2` varchar(100) DEFAULT NULL,
  `postal_code` varchar(50) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `nb_bulletins` int DEFAULT NULL,
  `comments` text,
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modification` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `date_delete` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `home`
--

INSERT INTO `home` (`id`, `address_title`, `address_name`, `address_line_1`, `address_line_2`, `postal_code`, `city`, `nb_bulletins`, `comments`, `date_creation`, `date_modification`, `date_delete`) VALUES
(1, 'Monsieur', 'Prénom Nom', 'Route des tests 1', NULL, '9999', 'Maville', 2, 'C\'est juste un test, pas de quoi s\'inquiéter', '2024-10-22 20:07:04', '2024-10-22 20:07:44', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `person`
--

CREATE TABLE `person` (
  `id` int UNSIGNED NOT NULL,
  `idx_home` int UNSIGNED NOT NULL,
  `idx_category` int UNSIGNED NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone_1` varchar(50) DEFAULT NULL,
  `phone_2` varchar(50) DEFAULT NULL,
  `birth` varchar(10) DEFAULT NULL,
  `profession` varchar(100) DEFAULT NULL,
  `godfathers` varchar(255) DEFAULT NULL,
  `membership_start` varchar(10) DEFAULT NULL,
  `membership_end` varchar(10) DEFAULT NULL,
  `membership_end_reason` varchar(255) DEFAULT NULL,
  `comments` text,
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modification` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `date_delete` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `person`
--

INSERT INTO `person` (`id`, `idx_home`, `idx_category`, `title`, `first_name`, `last_name`, `email`, `phone_1`, `phone_2`, `birth`, `profession`, `godfathers`, `membership_start`, `membership_end`, `membership_end_reason`, `comments`, `date_creation`, `date_modification`, `date_delete`) VALUES
(234, 1, 0, NULL, 'Test', 'Test', 'Mail@test.ch', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-10-22 20:46:01', '2024-10-22 20:55:15', NULL),
(235, 1, 0, NULL, 'Test', 'Suivant', 'mail@suivant.ch', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-10-22 20:46:01', '2024-10-22 20:55:18', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `home`
--
ALTER TABLE `home`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `person`
--
ALTER TABLE `person`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category` (`idx_category`),
  ADD KEY `idx_home` (`idx_home`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `home`
--
ALTER TABLE `home`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `person`
--
ALTER TABLE `person`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=236;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `person`
--
ALTER TABLE `person`
  ADD CONSTRAINT `fk_home` FOREIGN KEY (`idx_home`) REFERENCES `home` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
