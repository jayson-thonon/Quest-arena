-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Hôte : db
-- Généré le : dim. 26 oct. 2025 à 08:13
-- Version du serveur : 10.5.29-MariaDB-ubu2004
-- Version de PHP : 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `questarena`
--

-- --------------------------------------------------------

--
-- Structure de la table `combat`
--

CREATE TABLE `combat` (
  `id` int(11) NOT NULL,
  `joueur1_id` int(11) DEFAULT NULL,
  `joueur2_id` int(11) DEFAULT NULL,
  `gagnant_id` int(11) DEFAULT NULL,
  `date_combat` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `combat`
--

INSERT INTO `combat` (`id`, `joueur1_id`, `joueur2_id`, `gagnant_id`, `date_combat`) VALUES
(1, 12, 11, 12, '2025-10-13 09:02:41'),
(2, 12, 11, 12, '2025-10-13 10:00:25'),
(3, 11, 12, 11, '2025-10-16 18:22:48'),
(4, 11, 12, 11, '2025-10-22 12:47:02'),
(5, 11, 12, 11, '2025-10-23 09:48:42'),
(6, 17, 13, 17, '2025-10-26 01:27:21'),
(7, 18, 11, 11, '2025-10-26 01:49:03'),
(8, 18, 14, 18, '2025-10-26 01:49:23'),
(9, 18, 13, 18, '2025-10-26 01:50:43'),
(10, 18, 17, 18, '2025-10-26 01:50:49'),
(11, 13, 19, 13, '2025-10-26 04:17:59'),
(12, 13, 21, 13, '2025-10-26 04:18:09'),
(13, 13, 22, 13, '2025-10-26 04:18:13'),
(14, 13, 17, 13, '2025-10-26 04:18:16'),
(15, 13, 14, 13, '2025-10-26 04:18:19'),
(16, 13, 12, 12, '2025-10-26 04:18:40'),
(17, 24, 21, 24, '2025-10-26 07:39:51'),
(18, 24, 22, 24, '2025-10-26 07:40:53'),
(19, 24, 19, 24, '2025-10-26 07:43:58'),
(20, 24, 14, 24, '2025-10-26 07:44:14'),
(21, 24, 17, 24, '2025-10-26 07:45:09'),
(22, 24, 13, 24, '2025-10-26 07:45:53'),
(23, 24, 18, 24, '2025-10-26 07:46:05'),
(24, 24, 12, 24, '2025-10-26 07:46:19'),
(25, 24, 11, 11, '2025-10-26 07:46:38'),
(26, 24, 11, 11, '2025-10-26 08:02:23'),
(27, 24, 12, 24, '2025-10-26 08:02:54'),
(28, 24, 13, 24, '2025-10-26 08:03:04'),
(29, 24, 14, 24, '2025-10-26 08:03:15'),
(30, 24, 17, 24, '2025-10-26 08:03:23'),
(31, 24, 18, 24, '2025-10-26 08:03:33'),
(32, 24, 19, 24, '2025-10-26 08:03:44'),
(33, 24, 21, 24, '2025-10-26 08:03:55'),
(34, 24, 22, 24, '2025-10-26 08:04:00');

-- --------------------------------------------------------

--
-- Structure de la table `joueur`
--

CREATE TABLE `joueur` (
  `id` int(11) NOT NULL,
  `pseudo` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `joueur`
--

INSERT INTO `joueur` (`id`, `pseudo`, `email`, `mot_de_passe`) VALUES
(11, 'lajayz12', 'lajayz@123', '$2y$10$mVSrd.JX5Tr8jqqcSkJxROqECafpfBxTSp8ic2hhDQq3HZDe82nNW'),
(12, 'yo', 'yo@123', '$2y$10$yKwLsPpRyZYjyESq3UaiiepgB7X/JJQQ5MYWxiW.lhi1LPDYUjdgW'),
(13, 'Qanef', 'thomasmoisson0@gmail.com', '$2y$10$GAZP0f9GA.ZVjoy2wmAG.u0PpdbTO8ll7O14PtnAvM1dpLAhTf18a'),
(14, 'thomas', 'thomas@123', '$2y$10$.bG2yvi80WTiryUPjYWiYeqDMBQUfrCgI2npMEBmEf5YEaTNj39PS'),
(17, 'vysh', 'vysh@123', '$2y$10$aOqOsO1PUCv93C5nQczoROJMadz2qLTeXgAuNWiGOG0eZV7YJT0MK'),
(18, 'railey', 'railey@123', '$2y$10$byFPv6q/uoLMszdRBWJeqOZ9Ciow66IrRI3h2rqEMPjYezpgy01DC'),
(19, 'nosh', 'noamorandeau@gmail.com', '$2y$10$70EHDv41sjw8FxhdgXnPcec0NcHI1QARiw8cGYfCXM5rZpVuf4Qg.'),
(21, 'fdsgv', 'noshmorandeau@gmail.com', '$2y$10$97xZ5.Dw8x5PX9TtzrAQdu/vW9/xhxavuY5U.eaYfDwSWzJ0BW.zq'),
(22, 'fsdgs', 'nonomorandeau@gmail.com', '$2y$10$PAepElGaOQHPkUv9rTp5xutcdxaP6njg2BZKYyA2Ynbtn/lKahmFy'),
(24, 'Shellsea123', 'shellsea@123', '$2y$10$cBVzlIWrZzjZTk54fbjf5uIyW/6Ca65sQfXbolBC0kjtwZGZRJvMq');

-- --------------------------------------------------------

--
-- Structure de la table `personnage`
--

CREATE TABLE `personnage` (
  `id` int(11) NOT NULL,
  `joueur_id` int(11) DEFAULT NULL,
  `nom` varchar(50) DEFAULT NULL,
  `niveau` int(11) DEFAULT 1,
  `points_vie` int(11) DEFAULT 100,
  `attaque` int(11) DEFAULT 10,
  `defense` int(11) DEFAULT 5,
  `experience` int(11) DEFAULT 0,
  `victoires` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `personnage`
--

INSERT INTO `personnage` (`id`, `joueur_id`, `nom`, `niveau`, `points_vie`, `attaque`, `defense`, `experience`, `victoires`) VALUES
(2, 11, 'lajayz12', 4, 140, 18, 10, 760, 0),
(3, 12, 'yo', 3, 130, 16, 9, 760, 2),
(4, 13, 'Qanef', 2, 110, 12, 6, 0, 2),
(5, 14, 'thomas', 1, 100, 10, 5, 0, 0),
(6, 17, 'vysh', 1, 100, 10, 5, 0, 1),
(7, 18, 'railey', 3, 125, 15, 8, 260, 0),
(8, 19, 'nosh', 1, 100, 10, 5, 0, 0),
(9, 21, 'fdsgv', 1, 100, 10, 5, 0, 0),
(10, 22, 'fsdgs', 1, 100, 10, 5, 0, 0),
(11, 24, 'Shellsea123', 7, 165, 23, 12, 610, 1);

-- --------------------------------------------------------

--
-- Structure de la table `quete`
--

CREATE TABLE `quete` (
  `id` int(11) NOT NULL,
  `titre` varchar(100) NOT NULL,
  `question` text NOT NULL,
  `option1` varchar(255) DEFAULT NULL,
  `option2` varchar(255) DEFAULT NULL,
  `option3` varchar(255) DEFAULT NULL,
  `option4` varchar(255) DEFAULT NULL,
  `bonne_reponse` varchar(255) DEFAULT NULL,
  `recompense_xp` int(11) DEFAULT 50,
  `niveau` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `quete`
--

INSERT INTO `quete` (`id`, `titre`, `question`, `option1`, `option2`, `option3`, `option4`, `bonne_reponse`, `recompense_xp`, `niveau`) VALUES
(1, 'Langage de Programmation', 'Quel langage est principalement utilisé pour le développement Android ?', 'Python', 'Java', 'PHP', 'C#', 'Java', 50, 1),
(2, 'Réseau', 'Que signifie HTTP ?', 'Hyper Text Transfer Protocol', 'Hyper Tool Transfer Program', 'Home Transfer Text Program', 'Hyper Text Test Protocol', 'Hyper Text Transfer Protocol', 60, 1),
(3, 'Base de Données', 'Quel langage est utilisé pour manipuler les bases de données ?', 'SQL', 'HTML', 'CSS', 'XML', 'SQL', 70, 1),
(4, 'Informatique Générale', 'Que signifie CPU ?', 'Central Processing Unit', 'Computer Personal Unit', 'Central Power Unit', 'Core Process Utility', 'Central Processing Unit', 80, 1),
(5, 'Système d’exploitation', 'Quel est un système d’exploitation ?', 'Chrome', 'Windows', 'Photoshop', 'Google', 'Windows', 100, 2),
(6, 'Réseau avancé', 'Quelle adresse IP est privée ?', '8.8.8.8', '192.168.0.1', '172.34.2.9', '1.1.1.1', '192.168.0.1', 120, 2),
(7, 'Sécurité', 'Que signifie HTTPS ?', 'HyperText Transfer Protocol Secure', 'High Transfer Tool Protocol', 'Hyper Transfer Text Protocol', 'Hidden Transfer Protocol Secure', 'HyperText Transfer Protocol Secure', 130, 2),
(8, 'Programmation', 'Quel langage est utilisé pour le développement Android ?', 'Swift', 'Kotlin', 'PHP', 'C', 'Kotlin', 150, 2),
(9, 'Base de Données avancée', 'Que signifie SQL ?', 'Structured Query Language', 'Simple Question Logic', 'Server Query Layer', 'Sequential Query Loop', 'Structured Query Language', 200, 3),
(10, 'Système', 'Que signifie BIOS ?', 'Binary Input Output System', 'Basic Input Output System', 'Base Information Output Software', 'Basic Integrated Operation System', 'Basic Input Output System', 220, 3),
(11, 'Réseau', 'Quel protocole envoie des e-mails ?', 'SMTP', 'HTTP', 'FTP', 'IMAP', 'SMTP', 230, 3),
(12, 'Programmation', 'Que fait la boucle FOR ?', 'Répète un bloc de code', 'Crée une base de données', 'Affiche une image', 'Arrête le programme', 'Répète un bloc de code', 250, 3);

-- --------------------------------------------------------

--
-- Structure de la table `quete_joueur`
--

CREATE TABLE `quete_joueur` (
  `id` int(11) NOT NULL,
  `joueur_id` int(11) NOT NULL,
  `quete_id` int(11) NOT NULL,
  `date_realisation` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `quete_joueur`
--

INSERT INTO `quete_joueur` (`id`, `joueur_id`, `quete_id`, `date_realisation`) VALUES
(1, 12, 5, '2025-10-13 09:33:57'),
(2, 12, 6, '2025-10-13 09:33:57'),
(3, 12, 7, '2025-10-13 09:33:57'),
(4, 12, 8, '2025-10-13 09:33:57'),
(8, 12, 1, '2025-10-13 09:45:26'),
(9, 12, 2, '2025-10-13 09:45:26'),
(10, 12, 3, '2025-10-13 09:45:26'),
(11, 12, 4, '2025-10-13 09:45:26'),
(15, 11, 1, '2025-10-13 19:06:34'),
(16, 11, 2, '2025-10-13 19:06:34'),
(17, 11, 3, '2025-10-13 19:06:34'),
(18, 11, 4, '2025-10-13 19:06:34'),
(22, 11, 5, '2025-10-13 19:07:44'),
(23, 11, 6, '2025-10-13 19:07:44'),
(24, 11, 7, '2025-10-13 19:07:44'),
(25, 11, 8, '2025-10-13 19:07:44'),
(26, 18, 1, '2025-10-26 01:49:56'),
(27, 18, 2, '2025-10-26 01:49:56'),
(28, 18, 3, '2025-10-26 01:49:56'),
(29, 18, 4, '2025-10-26 01:49:56'),
(33, 24, 1, '2025-10-26 07:43:26'),
(34, 24, 2, '2025-10-26 07:43:26'),
(35, 24, 3, '2025-10-26 07:43:26'),
(36, 24, 4, '2025-10-26 07:43:26'),
(40, 24, 5, '2025-10-26 08:08:35'),
(41, 24, 6, '2025-10-26 08:08:35'),
(42, 24, 7, '2025-10-26 08:08:35'),
(43, 24, 8, '2025-10-26 08:08:35');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `combat`
--
ALTER TABLE `combat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `joueur1_id` (`joueur1_id`),
  ADD KEY `joueur2_id` (`joueur2_id`),
  ADD KEY `gagnant_id` (`gagnant_id`);

--
-- Index pour la table `joueur`
--
ALTER TABLE `joueur`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `personnage`
--
ALTER TABLE `personnage`
  ADD PRIMARY KEY (`id`),
  ADD KEY `joueur_id` (`joueur_id`);

--
-- Index pour la table `quete`
--
ALTER TABLE `quete`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `quete_joueur`
--
ALTER TABLE `quete_joueur`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `joueur_id` (`joueur_id`,`quete_id`),
  ADD KEY `quete_id` (`quete_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `combat`
--
ALTER TABLE `combat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT pour la table `joueur`
--
ALTER TABLE `joueur`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT pour la table `personnage`
--
ALTER TABLE `personnage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `quete`
--
ALTER TABLE `quete`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `quete_joueur`
--
ALTER TABLE `quete_joueur`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `combat`
--
ALTER TABLE `combat`
  ADD CONSTRAINT `combat_ibfk_1` FOREIGN KEY (`joueur1_id`) REFERENCES `joueur` (`id`),
  ADD CONSTRAINT `combat_ibfk_2` FOREIGN KEY (`joueur2_id`) REFERENCES `joueur` (`id`),
  ADD CONSTRAINT `combat_ibfk_3` FOREIGN KEY (`gagnant_id`) REFERENCES `joueur` (`id`);

--
-- Contraintes pour la table `personnage`
--
ALTER TABLE `personnage`
  ADD CONSTRAINT `personnage_ibfk_1` FOREIGN KEY (`joueur_id`) REFERENCES `joueur` (`id`);

--
-- Contraintes pour la table `quete_joueur`
--
ALTER TABLE `quete_joueur`
  ADD CONSTRAINT `quete_joueur_ibfk_1` FOREIGN KEY (`joueur_id`) REFERENCES `joueur` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quete_joueur_ibfk_2` FOREIGN KEY (`quete_id`) REFERENCES `quete` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
