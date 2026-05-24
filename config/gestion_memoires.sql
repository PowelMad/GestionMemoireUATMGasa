-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : dim. 24 mai 2026 à 18:12
-- Version du serveur : 8.4.7
-- Version de PHP : 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
CREATE DATABASE IF NOT EXISTS gestion_memoires
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gestion_memoires;
--
-- Base de données : `gestion_memoires`
--

-- --------------------------------------------------------

--
-- Structure de la table `admin`
--

DROP TABLE IF EXISTS `admin`;
CREATE TABLE IF NOT EXISTS `admin` (
  `id_admin` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenoms` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_utilisateur` int NOT NULL,
  PRIMARY KEY (`id_admin`),
  UNIQUE KEY `id_utilisateur` (`id_utilisateur`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `centre`
--

DROP TABLE IF EXISTS `centre`;
CREATE TABLE IF NOT EXISTS `centre` (
  `id_centre` int NOT NULL AUTO_INCREMENT,
  `libelle_centre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_centre`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `commentaire`
--

DROP TABLE IF EXISTS `commentaire`;
CREATE TABLE IF NOT EXISTS `commentaire` (
  `id_commentaire` int NOT NULL AUTO_INCREMENT,
  `text_comment` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_comment` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `parent_id` int DEFAULT NULL,
  `id_memoire` int NOT NULL,
  `id_utilisateur` int NOT NULL,
  PRIMARY KEY (`id_commentaire`),
  KEY `parent_id` (`parent_id`),
  KEY `id_memoire` (`id_memoire`),
  KEY `id_utilisateur` (`id_utilisateur`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `etudiant`
--

DROP TABLE IF EXISTS `etudiant`;
CREATE TABLE IF NOT EXISTS `etudiant` (
  `matricule` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenoms` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_etudiant` enum('L3','M1','M2') COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_filiere` int NOT NULL,
  `id_centre` int NOT NULL,
  `id_utilisateur` int NOT NULL,
  PRIMARY KEY (`matricule`),
  UNIQUE KEY `id_utilisateur` (`id_utilisateur`),
  KEY `id_filiere` (`id_filiere`),
  KEY `id_centre` (`id_centre`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `filiere`
--

DROP TABLE IF EXISTS `filiere`;
CREATE TABLE IF NOT EXISTS `filiere` (
  `id_filiere` int NOT NULL AUTO_INCREMENT,
  `libelle_filiere` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_filiere`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `like_memoire`
--

DROP TABLE IF EXISTS `like_memoire`;
CREATE TABLE IF NOT EXISTS `like_memoire` (
  `id_like` int NOT NULL AUTO_INCREMENT,
  `date_like` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `id_memoire` int NOT NULL,
  `id_utilisateur` int NOT NULL,
  PRIMARY KEY (`id_like`),
  UNIQUE KEY `unique_like` (`id_memoire`,`id_utilisateur`),
  KEY `id_utilisateur` (`id_utilisateur`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `memoire`
--

DROP TABLE IF EXISTS `memoire`;
CREATE TABLE IF NOT EXISTS `memoire` (
  `id_memoire` int NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `resume` text COLLATE utf8mb4_unicode_ci,
  `nom_fichier` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `chemin_acces_fichier` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_soutenu` date NOT NULL,
  `annee_academique` varchar(9) COLLATE utf8mb4_unicode_ci NOT NULL,
  `statut` enum('soumis','valide','rejete') COLLATE utf8mb4_unicode_ci DEFAULT 'soumis',
  `nb_vues` int DEFAULT '0',
  `date_mise_en_ligne` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `id_filiere` int NOT NULL,
  `id_centre` int NOT NULL,
  `id_professeur` int DEFAULT NULL,
  PRIMARY KEY (`id_memoire`),
  KEY `id_filiere` (`id_filiere`),
  KEY `id_centre` (`id_centre`),
  KEY `id_professeur` (`id_professeur`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `memoire_mot_cle`
--

DROP TABLE IF EXISTS `memoire_mot_cle`;
CREATE TABLE IF NOT EXISTS `memoire_mot_cle` (
  `id_memoire` int NOT NULL,
  `id_mot_cle` int NOT NULL,
  PRIMARY KEY (`id_memoire`,`id_mot_cle`),
  KEY `id_mot_cle` (`id_mot_cle`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `mot_cle`
--

DROP TABLE IF EXISTS `mot_cle`;
CREATE TABLE IF NOT EXISTS `mot_cle` (
  `id_mot_cle` int NOT NULL AUTO_INCREMENT,
  `libelle` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_mot_cle`),
  UNIQUE KEY `libelle` (`libelle`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `professeur`
--

DROP TABLE IF EXISTS `professeur`;
CREATE TABLE IF NOT EXISTS `professeur` (
  `id_professeur` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenoms` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `titre` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_utilisateur` int NOT NULL,
  PRIMARY KEY (`id_professeur`),
  UNIQUE KEY `id_utilisateur` (`id_utilisateur`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `soumettre`
--

DROP TABLE IF EXISTS `soumettre`;
CREATE TABLE IF NOT EXISTS `soumettre` (
  `matricule` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_memoire` int NOT NULL,
  `date_soumission` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`matricule`,`id_memoire`),
  KEY `id_memoire` (`id_memoire`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
CREATE TABLE IF NOT EXISTS `utilisateur` (
  `id_utilisateur` int NOT NULL AUTO_INCREMENT,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_utilisateur`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
