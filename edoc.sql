-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : sam. 29 nov. 2025 à 18:17
-- Version du serveur : 9.1.0
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `edoc`
--

-- --------------------------------------------------------

--
-- Structure de la table `admin`
--

DROP TABLE IF EXISTS `admin`;
CREATE TABLE IF NOT EXISTS `admin` (
  `aemail` varchar(255) NOT NULL,
  `apassword` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`aemail`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `admin`
--

INSERT INTO `admin` (`aemail`, `apassword`) VALUES
('rayenkhadhraoui@gmail.com', 'rayen123');

-- --------------------------------------------------------

--
-- Structure de la table `appointment`
--

DROP TABLE IF EXISTS `appointment`;
CREATE TABLE IF NOT EXISTS `appointment` (
  `appoid` int NOT NULL AUTO_INCREMENT,
  `pid` int DEFAULT NULL,
  `apponum` int DEFAULT NULL,
  `scheduleid` int DEFAULT NULL,
  `appodate` date DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `doctorid` int NOT NULL,
  `description` text,
  PRIMARY KEY (`appoid`),
  KEY `pid` (`pid`),
  KEY `scheduleid` (`scheduleid`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `appointment`
--

INSERT INTO `appointment` (`appoid`, `pid`, `apponum`, `scheduleid`, `appodate`, `status`, `doctorid`, `description`) VALUES
(1, 1, 1, 1, '2022-06-03', 'cancelled', 0, NULL),
(2, 1, 92722352, 1, '2025-04-17', 'active', 1, 'hamza zayeni'),
(3, 5, 1, 9, '2025-06-08', 'pending', 3, ''),
(4, 5, 1, 1, '2025-06-09', 'active', 1, ''),
(5, 5, 1, 11, '2025-06-18', 'pending', 3, ''),
(6, 5, 1, 15, '2025-06-12', 'cancelled', 3, 'hamza zayeni'),
(7, 5, 1, 17, '2025-06-11', 'active', 3, 'mridh b rasi'),
(8, 5, 1, 19, '2025-06-10', 'pending', 5, 'soker'),
(9, 5, 1, 17, '2025-06-13', 'active', 3, 'vérification'),
(10, 8, 1, 17, '2025-06-29', 'active', 3, 'fracture'),
(11, 8, 1, 19, '2025-06-11', 'cancelled', 5, 'sampthome'),
(12, 5, 1, 18, '2025-06-11', 'active', 2, 'aandi soker'),
(16, 9, 1, 20, '2025-06-28', 'active', 4, 'fracture '),
(15, 8, 1, 19, '2025-06-12', 'active', 5, 'hamza'),
(17, 10, 1, 20, '2025-06-21', 'active', 4, 'fracture'),
(18, 7, 1, 23, '2025-10-30', 'active', 7, ''),
(19, 11, 1, 24, '2025-11-27', 'active', 2, '');

-- --------------------------------------------------------

--
-- Structure de la table `doctor`
--

DROP TABLE IF EXISTS `doctor`;
CREATE TABLE IF NOT EXISTS `doctor` (
  `docid` int NOT NULL AUTO_INCREMENT,
  `docemail` varchar(255) DEFAULT NULL,
  `docname` varchar(255) DEFAULT NULL,
  `docpassword` varchar(255) DEFAULT NULL,
  `docnic` varchar(15) DEFAULT NULL,
  `doctel` varchar(15) DEFAULT NULL,
  `specialties` int DEFAULT NULL,
  PRIMARY KEY (`docid`),
  KEY `specialties` (`specialties`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `doctor`
--

INSERT INTO `doctor` (`docid`, `docemail`, `docname`, `docpassword`, `docnic`, `doctel`, `specialties`) VALUES
(5, 'amine@gmail.com', 'Dr. Amine', '123', '15265294', '92722352', 6),
(2, 'hamza@gmail.com', 'Dr. Hamza Zayeni', '123', '0123456781', '92722352', 1),
(3, 'amarkhadhraoui@gmail.com', 'Dr. Amar Khadhraoui', '123', '11647852', '98252961', 1),
(4, 'houssem@gmail.com', 'Dr. Houssem Khadhraoui', '123', '11647853', '92722353', 1),
(6, 'Taha@gmail.com', 'Dr. Taha khadhraoui', '123', '11647859', '92722355', 45),
(7, 'saif@gmail.com', 'Dr. saif ', '123', '11647852', '92722352', 54);

-- --------------------------------------------------------

--
-- Structure de la table `medicalhistory`
--

DROP TABLE IF EXISTS `medicalhistory`;
CREATE TABLE IF NOT EXISTS `medicalhistory` (
  `mid` int NOT NULL AUTO_INCREMENT,
  `pid` int NOT NULL,
  `docid` int NOT NULL,
  `mdate` date NOT NULL,
  `title` varchar(100) NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`mid`),
  KEY `pid` (`pid`),
  KEY `docid` (`docid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `medicalhistory`
--

INSERT INTO `medicalhistory` (`mid`, `pid`, `docid`, `mdate`, `title`, `content`) VALUES
(1, 8, 5, '2025-06-19', 'youcef', 'hamza zayeni');

-- --------------------------------------------------------

--
-- Structure de la table `notifications`
--

CREATE TABLE IF NOT EXISTS `notifications` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `user_type` ENUM('p','s') NOT NULL,
    `message` TEXT NOT NULL,
    `created_at` DATETIME NOT NULL,
    `is_read` TINYINT(1) DEFAULT 0
);

-- --------------------------------------------------------

--
-- Structure de la table `patient`
--

DROP TABLE IF EXISTS `patient`;
CREATE TABLE IF NOT EXISTS `patient` (
  `pid` int NOT NULL AUTO_INCREMENT,
  `pemail` varchar(255) DEFAULT NULL,
  `pname` varchar(255) DEFAULT NULL,
  `ppassword` varchar(255) DEFAULT NULL,
  `paddress` varchar(255) DEFAULT NULL,
  `pnic` varchar(15) DEFAULT NULL,
  `pdob` date DEFAULT NULL,
  `ptel` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`pid`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `patient`
--

INSERT INTO `patient` (`pid`, `pemail`, `pname`, `ppassword`, `paddress`, `pnic`, `pdob`, `ptel`) VALUES
(1, 'patient@edoc.com', 'Test Patient', '123', 'Sri Lanka', '0000000000', '2000-01-01', '0120000000'),
(2, 'emhashenudara@gmail.com', 'Hashen Udara', '123', 'Sri Lanka', '0110000000', '2022-06-03', '0700000000'),
(6, 'razi@gmail.com', 'razi khadhraoui', '123', 'razi@gmail.com', '11647855', '2025-06-07', '0715228888'),
(4, 'khadhraouirayen20@gmail.com', 'rayen khadhraoui', 'rayen123', 'sidi bouzid', '50188076', '2002-11-07', '0123456789'),
(5, 'hamzazayeni@gmail.com', 'hamza zayeni', '123', 'sidi bouzid', '92722352', '2025-04-16', '0715228888'),
(7, 'zayeni@gmail.com', 'Hamza Zayeni', '123', 'sidi bouzid', '11647852', '2025-06-12', '0715228888'),
(8, 'yousef@gmail.com', 'yousef rhimieee', '123', 'yousef@gmail.com', '11647857', '2025-06-11', '0123456789'),
(9, 'khalilbou@gmail.com', 'khalil bou', '123', 'khalilgmail.com', '11647852', '2025-06-30', '0123456789'),
(10, 'yassine@gmail.com', 'yassine samaali5', '123', 'sidi bouzid', '1164785200', '2025-06-21', '0123456789'),
(11, 'maryemmnefki14@gmail.com', 'Maryouma Ep zayeni', 'maryem@123', 'maryemmnefki14@gmail.com', '15254411', '2003-05-04', '0123456789');

-- --------------------------------------------------------

--
-- Structure de la table `schedule`
--

DROP TABLE IF EXISTS `schedule`;
CREATE TABLE IF NOT EXISTS `schedule` (
  `scheduleid` int NOT NULL AUTO_INCREMENT,
  `docid` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `scheduledate` date DEFAULT NULL,
  `scheduletime` time DEFAULT NULL,
  `nop` int DEFAULT NULL,
  PRIMARY KEY (`scheduleid`),
  KEY `docid` (`docid`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `schedule`
--

INSERT INTO `schedule` (`scheduleid`, `docid`, `title`, `scheduledate`, `scheduletime`, `nop`) VALUES
(18, '2', ' Séance Spéciale Ramadan 1446', '2025-06-11', '23:17:00', 50),
(19, '5', 'Consultations ORL Juin', '2025-06-12', '23:20:00', 70),
(20, '4', 'Séance Spéciale', '2025-06-29', '23:18:00', 100),
(21, '6', 'Suivi Thérapie Cognitive (Mineurs)', '2025-06-29', '10:57:00', 30),
(22, '7', 'session ramadhan', '2025-06-29', '23:02:00', 50),
(23, '7', 'Séance Spéciale Ramadan', '2025-10-30', '09:09:00', 30),
(17, '3', 'Consultation de Rentrée Septe', '2025-06-11', '23:17:00', 50),
(24, '2', 'session Jeudi', '2025-11-27', '16:03:00', 30);

-- --------------------------------------------------------

--
-- Structure de la table `secretary`
--

DROP TABLE IF EXISTS `secretary`;
CREATE TABLE IF NOT EXISTS `secretary` (
  `sid` int NOT NULL AUTO_INCREMENT,
  `semail` varchar(50) NOT NULL,
  `sname` varchar(50) NOT NULL,
  `spassword` varchar(50) NOT NULL,
  `stele` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`sid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `secretary`
--

INSERT INTO `secretary` (`sid`, `semail`, `sname`, `spassword`, `stele`) VALUES
(1, 'abir@gmail.com', 'Abir', '123456', '0612345678');

-- --------------------------------------------------------

--
-- Structure de la table `specialties`
--

DROP TABLE IF EXISTS `specialties`;
CREATE TABLE IF NOT EXISTS `specialties` (
  `id` int NOT NULL,
  `sname` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `specialties`
--

INSERT INTO `specialties` (`id`, `sname`) VALUES
(1, 'Accident and emergency medicine'),
(2, 'Allergology'),
(3, 'Anaesthetics'),
(4, 'Biological hematology'),
(5, 'Cardiology'),
(6, 'Child psychiatry'),
(7, 'Clinical biology'),
(8, 'Clinical chemistry'),
(9, 'Clinical neurophysiology'),
(10, 'Clinical radiology'),
(11, 'Dental, oral and maxillo-facial surgery'),
(12, 'Dermato-venerology'),
(13, 'Dermatology'),
(14, 'Endocrinology'),
(15, 'Gastro-enterologic surgery'),
(16, 'Gastroenterology'),
(17, 'General hematology'),
(18, 'General Practice'),
(19, 'General surgery'),
(20, 'Geriatrics'),
(21, 'Immunology'),
(22, 'Infectious diseases'),
(23, 'Internal medicine'),
(24, 'Laboratory medicine'),
(25, 'Maxillo-facial surgery'),
(26, 'Microbiology'),
(27, 'Nephrology'),
(28, 'Neuro-psychiatry'),
(29, 'Neurology'),
(30, 'Neurosurgery'),
(31, 'Nuclear medicine'),
(32, 'Obstetrics and gynecology'),
(33, 'Occupational medicine'),
(34, 'Ophthalmology'),
(35, 'Orthopaedics'),
(36, 'Otorhinolaryngology'),
(37, 'Paediatric surgery'),
(38, 'Paediatrics'),
(39, 'Pathology'),
(40, 'Pharmacology'),
(41, 'Physical medicine and rehabilitation'),
(42, 'Plastic surgery'),
(43, 'Podiatric Medicine'),
(44, 'Podiatric Surgery'),
(45, 'Psychiatry'),
(46, 'Public health and Preventive Medicine'),
(47, 'Radiology'),
(48, 'Radiotherapy'),
(49, 'Respiratory medicine'),
(50, 'Rheumatology'),
(51, 'Stomatology'),
(52, 'Thoracic surgery'),
(53, 'Tropical medicine'),
(54, 'Urology'),
(55, 'Vascular surgery'),
(56, 'Venereology');

-- --------------------------------------------------------

--
-- Structure de la table `webuser`
--

DROP TABLE IF EXISTS `webuser`;
CREATE TABLE IF NOT EXISTS `webuser` (
  `email` varchar(255) NOT NULL,
  `usertype` enum('a','d','p','s') NOT NULL,
  PRIMARY KEY (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `webuser`
--

INSERT INTO `webuser` (`email`, `usertype`) VALUES
('admin@edoc.com', 'a'),
('amine@gmail.com', 'd'),
('patient@edoc.com', 'p'),
('emhashenudara@gmail.com', 'p'),
('hamzazayeni417@gmail.com', 'p'),
('khadhraouirayen20@gmail.com', 'p'),
('hamza@gmail.com', 'd'),
('hamzazayeni@gmail.com', 'p'),
('abir@gmail.com', 's'),
('rayenkhadhraoui@gmail.com', 'a'),
('', ''),
('amarkhadhraoui@gmail.com', 'd'),
('houssem@gmail.com', 'd'),
('razi@gmail.com', 'p'),
('zayeni@gmail.com', 'p'),
('yousef@gmail.com', 'p'),
('Taha@gmail.com', 'd'),
('saif@gmail.com', 'd'),
('khalilbou@gmail.com', 'p'),
('yassine@gmail.com', 'p'),
('maryemmnefki14@gmail.com', 'p');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
