-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : lun. 18 avr. 2022 à 17:29
-- Version du serveur :  5.7.31
-- Version de PHP : 7.3.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `bilemoapi`
--

-- --------------------------------------------------------

--
-- Structure de la table `clients`
--

DROP TABLE IF EXISTS `clients`;
CREATE TABLE IF NOT EXISTS `clients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_C82E74E7927C74` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `clients`
--

INSERT INTO `clients` (`id`, `email`, `password`) VALUES
(1, 'nabilmecili@gmail.com', '6d43ded7278fcb013a4e88bc4f8312bd13ede859'),
(2, 'mecjeux@yahoo.fr', '39889c98ce58e4383a5b83c62d53dabb');

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
CREATE TABLE IF NOT EXISTS `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20220316090915', '2022-03-16 09:10:13', 202),
('DoctrineMigrations\\Version20220317080747', '2022-03-17 08:08:14', 462),
('DoctrineMigrations\\Version20220317081025', '2022-03-17 08:10:32', 161),
('DoctrineMigrations\\Version20220407100521', '2022-04-07 10:05:44', 817),
('DoctrineMigrations\\Version20220407103347', '2022-04-07 10:33:59', 376),
('DoctrineMigrations\\Version20220407104628', '2022-04-07 10:46:37', 409),
('DoctrineMigrations\\Version20220407105411', '2022-04-07 10:54:23', 364),
('DoctrineMigrations\\Version20220407105619', '2022-04-07 10:57:42', 357),
('DoctrineMigrations\\Version20220407110348', '2022-04-07 11:04:01', 426),
('DoctrineMigrations\\Version20220418114505', '2022-04-18 11:45:31', 1040);

-- --------------------------------------------------------

--
-- Structure de la table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `brand` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `featured_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `products`
--

INSERT INTO `products` (`id`, `title`, `description`, `color`, `created_at`, `brand`, `featured_image`) VALUES
(1, '1S 2021', '1S 2021', 'Noir', '2022-01-06 10:30:18', 'Alcatel', 'image.jpg'),
(2, 'Galaxy A33 5G', 'Galaxy A33 5G', 'Noir', '2022-01-06 10:30:18', 'Samsung', 'image2.jpg'),
(3, 'A94 5G', 'A94 5G', 'Noir', '2022-04-18 13:30:04', 'Oppo', 'oppoimage.jpg'),
(4, '11T 5G', '11T 5G', 'Gris', '2022-04-18 13:30:04', 'Xiaomi', 'XiaomiImage.jpg'),
(5, '20 SE', '20 SE', 'Gris', '2022-04-17 13:33:51', 'TCL', 'TCLimage.jpg'),
(6, 'Galaxy XCover 5', 'Galaxy XCover 5', 'Noir', '2022-04-15 13:33:51', 'Samsung', 'SamsungImage.jpg'),
(7, 'Galaxy A52s 5G New', 'Galaxy A52s 5G New', 'Noir', '2022-04-11 13:49:53', 'Samsung', 'SamsungImage.jpg'),
(8, 'iPhone SE', 'iPhone SE', 'Rouge', '2022-04-18 13:49:53', 'Apple', 'AppleImage.jpg');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_1483A5E9E7927C74` (`email`),
  KEY `IDX_1483A5E919EB6921` (`client_id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `client_id`) VALUES
(1, 'mecjeux@yahoo.fr', '$2y$13$ZC9OPkwzR74HY0ciwpH4.u1H6ivxh.5ix1YMb4H3Xi6vc2dzJMXae', 1),
(20, 'moi20@moi.fr', '$2y$13$IuAoPt2dh38Wz0V4AToV/ewWz/jkYsQSMot7iyKKdkxHqzb6Bv8j.', 1),
(21, 'moi8@gmai.com', '$2y$13$XFGO19X3M06lvLYVQ9weve7rw2iXVLa2zApF0lkSgpVfHXeP7RuL.', 1),
(22, 'moi10@gmail.com', '$2y$13$.YS4yVHcdCIUqVZFISc9xuQktihD9j9leXyIFTxt1SIqGIQuI68Cy', 1),
(24, 'moi22@gmail.com', '$2y$13$9xQEMXgBsiJGuoxZpbNQYe6HmTqzrSuxYNzIFpps5uflKR5jH6doW', 1),
(25, 'moi25@gmail.com', '$2y$13$jRrWuMlwV2ZJF8r7SSUQZOIeN110MN3YsDm77KCF6fist4k7sytrS', 2),
(26, 'moi26@moi.fr', '$2y$13$JCuMdvT9xmBP/VI9HpLGm.bm8tnhC0WgDvw7ggY5D6COVVwEa2Q36', 1),
(27, 'moi27@moi.fr', '$2y$13$176PLI0X68ENpHg2ljx3xuLc8igxcUqj4NU7Xap7kWqZhxLLFF1X2', 1),
(28, 'moi28@moi.fr', '$2y$13$XmZcR/TLDo.iXiZEyp8Qwu9NEyntWg53QS9zdJD0THPvbmGMY4/eG', 1),
(29, 'moi29@moi.fr', '$2y$13$CsvOAF3WlulLPppEBt9nHuPMyHePrbW35S1p1ua0D3JQgSfQtRSQe', 1),
(30, 'moi30@moi.fr', '$2y$13$tlV5ATXhJlau8m28PstV4eg7DkvD.rwHiwY3B5Z6K2RWA3ujxwqXu', 1);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `FK_1483A5E919EB6921` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
