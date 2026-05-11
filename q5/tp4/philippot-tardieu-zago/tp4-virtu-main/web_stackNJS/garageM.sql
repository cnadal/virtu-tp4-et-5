-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Generation Time: Mar 17, 2026 at 02:07 PM
-- Server version: 9.2.0
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

DROP DATABASE IF EXISTS garageM;
--
-- Database: `garageM`
--
CREATE DATABASE IF NOT EXISTS garageM;

use garageM;

-- --------------------------------------------------------

--
-- Table structure for table `Voitures`
--

CREATE TABLE `Voitures` (
  `immatriculation` int NOT NULL,
  `couleur` varchar(20) NOT NULL,
  `km` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Voitures`
--

INSERT INTO `Voitures` (`immatriculation`, `couleur`, `km`) VALUES
(111222333, 'rouge', 50000),
(123456789, 'verte', 200000);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Voitures`
--
ALTER TABLE `Voitures`
  ADD PRIMARY KEY (`immatriculation`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
