-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 03, 2024 at 09:16 PM
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
-- Database: `rambert_membres_handmade`
--

-- --------------------------------------------------------

--
-- Table structure for table `access`
--

CREATE TABLE `access` (
  `id` int UNSIGNED NOT NULL,
  `fk_access_level` int UNSIGNED NOT NULL,
  `fk_person` int UNSIGNED NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modification` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `date_delete` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `access_level`
--

CREATE TABLE `access_level` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) NOT NULL,
  `level` int NOT NULL,
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modification` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `date_delete` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `access_level`
--

INSERT INTO `access_level` (`id`, `name`, `description`, `level`, `date_creation`, `date_modification`, `date_delete`) VALUES
(1, 'Administrateur', '', 5, '2024-10-27 21:42:19', NULL, NULL),
(2, 'Gestionnaire', 'Gestionnaire du fichier des membres', 4, '2024-10-27 21:42:19', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `admission_price` float DEFAULT NULL,
  `annual_price` float DEFAULT NULL,
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modification` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `date_delete` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `name`, `description`, `admission_price`, `annual_price`, `date_creation`, `date_modification`, `date_delete`) VALUES
(1, 'Junior', 'Entre 8 et 16 ans', NULL, NULL, '2024-10-24 18:43:08', NULL, NULL),
(2, 'Jeune', 'Entre 17 et 24 ans', NULL, NULL, '2024-10-24 18:43:08', NULL, NULL),
(3, 'Actif', 'dès 25 ans', NULL, NULL, '2024-10-24 18:46:00', NULL, NULL),
(4, 'Conjoint-e d\'actif', 'Conjoint ou conjointe d\'un membre actif', NULL, NULL, '2024-10-24 18:47:43', NULL, NULL),
(5, 'Honoraire', '25 ans de sociétariat', NULL, NULL, '2024-10-24 18:48:21', NULL, NULL),
(6, 'Conjoint-e d\'honoraire', 'Conjoint ou conjointe d\'un membre honoraire', NULL, NULL, '2024-10-24 18:48:51', NULL, NULL),
(7, 'Jubilaire', '50 ans de sociétariat', NULL, NULL, '2024-10-24 18:49:28', NULL, NULL),
(8, 'Membre d\'honneur', 'Reconnaissance pour un engagement particulier', NULL, NULL, '2024-10-24 18:50:29', NULL, NULL),
(9, 'Veuf-ve', 'Conjoint ou conjointe veuf-ve d\'un membre décédé', NULL, NULL, '2024-10-24 18:51:27', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `change`
--

CREATE TABLE `change` (
  `id` int UNSIGNED NOT NULL,
  `fk_change_author` int UNSIGNED NOT NULL,
  `fk_person_concerned` int UNSIGNED DEFAULT NULL,
  `fk_change_type` int UNSIGNED DEFAULT NULL,
  `field` varchar(100) DEFAULT NULL,
  `value_old` text,
  `value_new` text,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `change_type`
--

CREATE TABLE `change_type` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modification` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `date_delete` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `change_type`
--

INSERT INTO `change_type` (`id`, `name`, `description`, `date_creation`, `date_modification`, `date_delete`) VALUES
(1, 'Admission', 'Inscription d\'une nouvelle personne en tant que membre du club Rambert', '2024-10-28 19:47:56', '2024-10-28 19:55:56', NULL),
(2, 'Sortie', 'Démission, décès ou toute autre raison qui fait qu\'une personne n\'est plus membre du club Rambert', '2024-10-28 19:47:56', '2024-10-28 19:56:01', NULL),
(3, 'Changement de catégorie de membre', NULL, '2024-10-28 19:50:11', '2024-10-28 19:53:20', NULL),
(4, 'Changement d\'adresse postale', 'Changement de foyer ou modification de l\'adresse postale d\'un foyer', '2024-10-28 19:50:11', '2024-10-28 19:54:00', NULL),
(5, 'Changement de nom', 'Modification du nom ou du prénom d\'un membre', '2024-10-28 19:55:45', NULL, NULL),
(6, 'Changement de coordonnées', 'Modification d\'un numéro de téléphone ou de l\'e-mail d\'un membre', '2024-10-28 19:55:45', NULL, NULL),
(7, 'Changement d\'une information', 'Modification des parrains, de la date de naissance ou de la profession d\'un membre', '2024-10-28 19:59:08', NULL, NULL),
(8, 'Changement de mot de passe', 'Modification du mot de passe d\'un membre', '2024-10-28 19:59:08', NULL, NULL),
(9, 'Changement de contribution', 'Début ou fin de la contribution d\'un membre à une commission ou autre fonction', '2024-10-28 20:00:39', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `contribution`
--

CREATE TABLE `contribution` (
  `id` int UNSIGNED NOT NULL,
  `fk_person` int UNSIGNED NOT NULL,
  `fk_function` int UNSIGNED NOT NULL,
  `date_begin` datetime DEFAULT NULL,
  `date_end` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `function`
--

CREATE TABLE `function` (
  `id` int UNSIGNED NOT NULL,
  `fk_team` int UNSIGNED DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modification` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `date_delete` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `function`
--

INSERT INTO `function` (`id`, `fk_team`, `name`, `description`, `date_creation`, `date_modification`, `date_delete`) VALUES
(1, 1, 'Président-e', NULL, '2024-10-28 20:23:45', NULL, NULL),
(2, 1, 'Vice-président-e', NULL, '2024-10-28 20:23:45', '2024-10-28 20:24:00', NULL),
(3, 1, 'Caissier / Caissière', NULL, '2024-10-28 20:25:05', NULL, NULL),
(4, 1, 'Responsable des réservations de chalets', NULL, '2024-10-28 20:25:05', NULL, NULL),
(5, 1, 'Membre', NULL, '2024-10-28 20:25:32', NULL, NULL),
(6, 2, 'Membre', NULL, '2024-10-28 20:26:56', NULL, NULL),
(7, 3, 'Président-e', NULL, '2024-10-28 20:27:29', NULL, NULL),
(8, 3, 'Vice-président-e', NULL, '2024-10-28 20:29:55', NULL, NULL),
(9, 3, 'Membre', NULL, '2024-10-28 20:27:29', '2024-10-28 20:29:31', NULL),
(10, 4, 'Président-e', NULL, '2024-10-28 20:28:37', '2024-10-28 20:29:17', NULL),
(11, 4, 'Secrétaire', NULL, '2024-10-28 20:30:39', NULL, NULL),
(12, 4, 'Membre', NULL, '2024-10-28 20:28:37', '2024-10-28 20:29:10', NULL),
(13, 5, 'Membre', NULL, '2024-10-28 20:32:42', NULL, NULL),
(14, 6, 'Membre', NULL, '2024-10-28 20:33:05', NULL, NULL),
(15, 7, 'Responsable', NULL, '2024-10-28 20:34:14', '2024-10-28 20:35:50', NULL),
(16, 8, 'Président-e', NULL, '2024-10-28 20:34:14', NULL, NULL),
(17, 8, 'Membre', NULL, '2024-10-28 20:34:28', NULL, NULL),
(18, 9, 'Responsable', NULL, '2024-10-28 20:37:40', NULL, NULL),
(19, 10, 'Responsable du site web', NULL, '2024-10-28 20:37:40', NULL, NULL),
(20, 10, 'Responsable des réseaux sociaux', NULL, '2024-10-28 20:38:23', NULL, NULL),
(21, 10, 'Membre', NULL, '2024-10-28 20:38:23', NULL, NULL),
(22, 11, 'Responsable', NULL, '2024-10-28 20:39:26', NULL, NULL),
(23, 12, 'Responsable', NULL, '2024-10-28 20:39:26', NULL, NULL),
(24, 13, 'Délégué', NULL, '2024-10-28 20:40:55', NULL, NULL);

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
  `date_delete` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `home`
--

INSERT INTO `home` (`id`, `address_title`, `address_name`, `address_line_1`, `address_line_2`, `postal_code`, `city`, `nb_bulletins`, `comments`, `date_creation`, `date_modification`, `date_delete`) VALUES
(1, 'Monsieur', 'Prénom Nom', 'Route des tests 1', NULL, '9999', 'Maville', 2, 'C\'est juste un test, pas de quoi s\'inquiéter', '2024-10-22 20:07:04', '2024-10-22 20:07:44', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `newsletter`
--

CREATE TABLE `newsletter` (
  `id` int UNSIGNED NOT NULL,
  `title` varchar(100) NOT NULL,
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modification` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `date_delete` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `newsletter`
--

INSERT INTO `newsletter` (`id`, `title`, `date_creation`, `date_modification`, `date_delete`) VALUES
(1, 'Bulletin d\'informations par e-mail', '2024-11-03 20:54:11', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `newsletter_subscription`
--

CREATE TABLE `newsletter_subscription` (
  `id` int UNSIGNED NOT NULL,
  `fk_person` int UNSIGNED NOT NULL,
  `fk_newsletter` int UNSIGNED NOT NULL,
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modification` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `date_delete` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `person`
--

CREATE TABLE `person` (
  `id` int UNSIGNED NOT NULL,
  `fk_home` int UNSIGNED NOT NULL,
  `fk_category` int UNSIGNED NOT NULL,
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
  `date_delete` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `person`
--

INSERT INTO `person` (`id`, `fk_home`, `fk_category`, `title`, `first_name`, `last_name`, `email`, `phone_1`, `phone_2`, `birth`, `profession`, `godfathers`, `membership_start`, `membership_end`, `membership_end_reason`, `comments`, `date_creation`, `date_modification`, `date_delete`) VALUES
(234, 1, 1, NULL, 'Test', 'Test', 'Mail@test.ch', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-10-22 20:46:01', '2024-10-24 18:30:39', NULL),
(235, 1, 1, NULL, 'Test', 'Suivant', 'mail@suivant.ch', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-10-22 20:46:01', '2024-10-24 18:30:43', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `team`
--

CREATE TABLE `team` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modification` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `date_delete` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `team`
--

INSERT INTO `team` (`id`, `name`, `description`, `date_creation`, `date_modification`, `date_delete`) VALUES
(1, 'Comité central', NULL, '2024-10-28 20:21:12', NULL, NULL),
(2, 'Commission de vérification des comptes', NULL, '2024-10-28 20:21:12', NULL, NULL),
(3, 'Commission Rosaly', NULL, '2024-10-28 20:22:03', NULL, NULL),
(4, 'Commission Saanenwald', NULL, '2024-10-28 20:22:03', NULL, NULL),
(5, 'Commission des bâtiments', NULL, '2024-10-28 20:31:10', NULL, NULL),
(6, 'Commission des finances', NULL, '2024-10-28 20:31:22', NULL, NULL),
(7, 'Jeudistes', NULL, '2024-10-28 20:33:40', NULL, NULL),
(8, 'Commission jeunesse', NULL, '2024-10-28 20:33:40', NULL, NULL),
(9, 'Programme des courses', NULL, '2024-10-28 20:37:12', NULL, NULL),
(10, 'Commission Internet', NULL, '2024-10-28 20:37:12', NULL, NULL),
(11, 'Bulletin', NULL, '2024-10-28 20:39:02', NULL, NULL),
(12, 'Livret annuel', NULL, '2024-10-28 20:39:02', NULL, NULL),
(13, 'FUL, Foyer Unioniste Lausannois', NULL, '2024-10-28 20:40:22', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `access`
--
ALTER TABLE `access`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_person` (`fk_person`),
  ADD KEY `idx_access_level` (`fk_access_level`);

--
-- Indexes for table `access_level`
--
ALTER TABLE `access_level`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `change`
--
ALTER TABLE `change`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_person` (`fk_person_concerned`),
  ADD KEY `idx_change_type` (`fk_change_type`),
  ADD KEY `idx_change_author` (`fk_change_author`);

--
-- Indexes for table `change_type`
--
ALTER TABLE `change_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contribution`
--
ALTER TABLE `contribution`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_person` (`fk_person`),
  ADD KEY `idx_function` (`fk_function`);

--
-- Indexes for table `function`
--
ALTER TABLE `function`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_team` (`fk_team`);

--
-- Indexes for table `home`
--
ALTER TABLE `home`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `newsletter`
--
ALTER TABLE `newsletter`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `newsletter_subscription`
--
ALTER TABLE `newsletter_subscription`
  ADD PRIMARY KEY (`id`),
  ADD KEY `newsletter_subscription_person` (`fk_person`),
  ADD KEY `newsletter_subscription_newsletter` (`fk_newsletter`);

--
-- Indexes for table `person`
--
ALTER TABLE `person`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category` (`fk_category`),
  ADD KEY `idx_home` (`fk_home`);

--
-- Indexes for table `team`
--
ALTER TABLE `team`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `access`
--
ALTER TABLE `access`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `access_level`
--
ALTER TABLE `access_level`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `change`
--
ALTER TABLE `change`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `change_type`
--
ALTER TABLE `change_type`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `contribution`
--
ALTER TABLE `contribution`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `function`
--
ALTER TABLE `function`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `home`
--
ALTER TABLE `home`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `newsletter`
--
ALTER TABLE `newsletter`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `newsletter_subscription`
--
ALTER TABLE `newsletter_subscription`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `person`
--
ALTER TABLE `person`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=236;

--
-- AUTO_INCREMENT for table `team`
--
ALTER TABLE `team`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `access`
--
ALTER TABLE `access`
  ADD CONSTRAINT `access_level` FOREIGN KEY (`fk_access_level`) REFERENCES `access_level` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `access_person` FOREIGN KEY (`fk_person`) REFERENCES `person` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `change`
--
ALTER TABLE `change`
  ADD CONSTRAINT `change_log_change_author` FOREIGN KEY (`fk_change_author`) REFERENCES `person` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `change_log_person_concerned` FOREIGN KEY (`fk_person_concerned`) REFERENCES `person` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `change_log_type` FOREIGN KEY (`fk_change_type`) REFERENCES `change_type` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `contribution`
--
ALTER TABLE `contribution`
  ADD CONSTRAINT `contribution_function` FOREIGN KEY (`fk_function`) REFERENCES `function` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `contribution_person` FOREIGN KEY (`fk_person`) REFERENCES `person` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `function`
--
ALTER TABLE `function`
  ADD CONSTRAINT `function_team` FOREIGN KEY (`fk_team`) REFERENCES `team` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `newsletter_subscription`
--
ALTER TABLE `newsletter_subscription`
  ADD CONSTRAINT `newsletter_subscription_newsletter` FOREIGN KEY (`fk_newsletter`) REFERENCES `newsletter` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `newsletter_subscription_person` FOREIGN KEY (`fk_person`) REFERENCES `person` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `person`
--
ALTER TABLE `person`
  ADD CONSTRAINT `person_category` FOREIGN KEY (`fk_category`) REFERENCES `category` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `person_home` FOREIGN KEY (`fk_home`) REFERENCES `home` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
