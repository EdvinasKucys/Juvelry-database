-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 26, 2025 at 09:49 PM
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
-- Database: `juvelyrika_2`
--

-- --------------------------------------------------------

--
-- Table structure for table `gamintojas`
--

CREATE TABLE `gamintojas` (
  `gamintojo_id` varchar(64) NOT NULL,
  `pavadinimas` varchar(200) NOT NULL,
  `salis` varchar(50) DEFAULT NULL,
  `kontaktai` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gamintojas`
--

INSERT INTO `gamintojas` (`gamintojo_id`, `pavadinimas`, `salis`, `kontaktai`) VALUES
('G001', 'Aukso Dirbtuvės', 'Lietuva', 'info@aukso-dirbtuves.lt'),
('G002', 'Silver Dreams', 'Italija', 'sales@silverdreams.it');

-- --------------------------------------------------------

--
-- Table structure for table `kategorija`
--

CREATE TABLE `kategorija` (
  `id_KATEGORIJA` int(11) NOT NULL,
  `pavadinimas` varchar(50) NOT NULL,
  `aprasymas` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategorija`
--

INSERT INTO `kategorija` (`id_KATEGORIJA`, `pavadinimas`, `aprasymas`) VALUES
(1, 'Žiedai', 'Įvairių stilių ir dizainų žiedai'),
(2, 'Auskarai', 'Klasikiniai ir modernūs auskarai'),
(3, 'Apyrankės', 'Įvairaus pločio ir medžiagų apyrankės');

-- --------------------------------------------------------

--
-- Table structure for table `preke`
--

CREATE TABLE `preke` (
  `id` varchar(64) NOT NULL,
  `pavadinimas` varchar(200) NOT NULL,
  `aprasymas` varchar(255) DEFAULT NULL,
  `kaina` float NOT NULL,
  `svoris` float NOT NULL,
  `medziaga` varchar(100) DEFAULT NULL,
  `fk_GAMINTOJASgamintojo_id` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `preke`
--

INSERT INTO `preke` (`id`, `pavadinimas`, `aprasymas`, `kaina`, `svoris`, `medziaga`, `fk_GAMINTOJASgamintojo_id`) VALUES
('P001', 'Auksinis žiedas', 'Klasikinis auksinis žiedas', 299.99, 5.2, 'Auksas 585', 'G001'),
('P002', 'Sidabriniai auskarai', 'Minimalistiniai auskarai', 89.99, 3.1, 'Sidabras 925', 'G002');

-- --------------------------------------------------------

--
-- Table structure for table `preke_kategorija`
--

CREATE TABLE `preke_kategorija` (
  `id` int(11) NOT NULL,
  `fk_PREKEid` varchar(64) NOT NULL,
  `fk_KATEGORIJAid_KATEGORIJA` int(11) NOT NULL,
  `pagrindine_kategorija` tinyint(1) DEFAULT 0,
  `priskirimo_data` date NOT NULL DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `preke_kategorija`
--

INSERT INTO `preke_kategorija` (`id`, `fk_PREKEid`, `fk_KATEGORIJAid_KATEGORIJA`, `pagrindine_kategorija`, `priskirimo_data`) VALUES
(1, 'P001', 1, 1, '2025-04-26'),
(2, 'P001', 3, 0, '2025-04-26'),
(3, 'P002', 2, 1, '2025-04-26');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gamintojas`
--
ALTER TABLE `gamintojas`
  ADD PRIMARY KEY (`gamintojo_id`);

--
-- Indexes for table `kategorija`
--
ALTER TABLE `kategorija`
  ADD PRIMARY KEY (`id_KATEGORIJA`);

--
-- Indexes for table `preke`
--
ALTER TABLE `preke`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_GAMINTOJASgamintojo_id` (`fk_GAMINTOJASgamintojo_id`);

--
-- Indexes for table `preke_kategorija`
--
ALTER TABLE `preke_kategorija`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `fk_PREKEid` (`fk_PREKEid`,`fk_KATEGORIJAid_KATEGORIJA`),
  ADD KEY `fk_KATEGORIJAid_KATEGORIJA` (`fk_KATEGORIJAid_KATEGORIJA`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `kategorija`
--
ALTER TABLE `kategorija`
  MODIFY `id_KATEGORIJA` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `preke_kategorija`
--
ALTER TABLE `preke_kategorija`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `preke`
--
ALTER TABLE `preke`
  ADD CONSTRAINT `preke_ibfk_1` FOREIGN KEY (`fk_GAMINTOJASgamintojo_id`) REFERENCES `gamintojas` (`gamintojo_id`);

--
-- Constraints for table `preke_kategorija`
--
ALTER TABLE `preke_kategorija`
  ADD CONSTRAINT `preke_kategorija_ibfk_1` FOREIGN KEY (`fk_PREKEid`) REFERENCES `preke` (`id`),
  ADD CONSTRAINT `preke_kategorija_ibfk_2` FOREIGN KEY (`fk_KATEGORIJAid_KATEGORIJA`) REFERENCES `kategorija` (`id_KATEGORIJA`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
