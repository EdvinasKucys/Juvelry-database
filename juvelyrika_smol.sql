-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 26, 2025 at 08:09 PM
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
-- Database: `juvelyrika_smol`
--

-- --------------------------------------------------------

--
-- Table structure for table `gamintojas`
--

CREATE TABLE `gamintojas` (
  `gamintojo_id` varchar(20) NOT NULL,
  `pavadinimas` varchar(100) NOT NULL,
  `salis` varchar(50) DEFAULT NULL,
  `kontaktai` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gamintojas`
--

INSERT INTO `gamintojas` (`gamintojo_id`, `pavadinimas`, `salis`, `kontaktai`) VALUES
('AMYR008', 'Gintaro papuošalai', 'Lietuva', 'info@gintaropapuosalai.lt, +370-5-212-1212'),
('BCVR009', 'Baltijos amatai', 'Latvija', 'sales@balticrafts.lv, +371-67-223344'),
('BVLG003', 'Bulgari', 'Italija', 'support@bulgari.com, +39-06-8888-7766'),
('CART002', 'Cartier', 'Prancūzija', 'info@cartier.com, +33-1-4455-3322'),
('GCCI007', 'Gucci', 'Italija', 'jewels@gucci.com, +39-055-7592-7010'),
('HRMS006', 'Hermès Jewelry', 'Prancūzija', 'jewelry@hermes.com, +33-1-4017-4717'),
('LTHR010', 'Lietuviškas paveldas', 'Lietuva', 'orders@ltpaveldas.lt, +370-5-111-2222'),
('PAND004', 'Pandora', 'Danija', 'service@pandora.net, +45-3333-2211'),
('SWRV005', 'Swarovski', 'Austrija', 'crystal@swarovski.com, +43-5224-5000'),
('TIFF001', 'Tiffany & Co.', 'JAV', 'contact@tiffany.com, +1-212-555-0101');

-- --------------------------------------------------------

--
-- Table structure for table `kategorija`
--

CREATE TABLE `kategorija` (
  `id_KATEGORIJA` int(11) NOT NULL,
  `pavadinimas` varchar(100) NOT NULL,
  `aprasymas` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategorija`
--

INSERT INTO `kategorija` (`id_KATEGORIJA`, `pavadinimas`, `aprasymas`) VALUES
(1, 'Žiedai', 'Įvairių tipų juvelyriniai žiedai skirtingoms progoms'),
(2, 'Vėriniai', 'Elegantiški vėriniai kasdieniam dėvėjimui ir ypatingoms progoms'),
(3, 'Apyrankės', 'Stilingos apyrankės, kurios papildo bet kurį aprangos derinį'),
(4, 'Auskarai', 'Gražūs auskarai nuo sagučių iki kabančių modelių'),
(5, 'Laikrodžiai', 'Aukščiausios kokybės laikrodžiai vyrams ir moterims'),
(6, 'Pakabukai', 'Unikalūs pakabukai jūsų vėriniams personalizuoti'),
(7, 'Sagės', 'Klasikinės ir modernios sagės bet kuriai progai'),
(8, 'Kojos papuošalai', 'Subtilūs kojos papuošalai kasdieniam ir iškilmingam dėvėjimui'),
(9, 'Vestuviniai papuošalai', 'Specialūs papuošalai vestuvių ceremonijai'),
(10, 'Rinkiniai', 'Suderinti juvelyriniai rinkiniai vieningam įvaizdžiui');

-- --------------------------------------------------------

--
-- Table structure for table `preke`
--

CREATE TABLE `preke` (
  `id` varchar(20) NOT NULL,
  `pavadinimas` varchar(200) NOT NULL,
  `aprasymas` text DEFAULT NULL,
  `kaina` decimal(10,2) NOT NULL,
  `svoris` decimal(10,2) DEFAULT NULL,
  `medziaga` varchar(100) DEFAULT NULL,
  `fk_GAMINTOJASgamintojo_id` varchar(20) NOT NULL,
  `fk_KATEGORIJAid_KATEGORIJA` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `preke`
--

INSERT INTO `preke` (`id`, `pavadinimas`, `aprasymas`, `kaina`, `svoris`, `medziaga`, `fk_GAMINTOJASgamintojo_id`, `fk_KATEGORIJAid_KATEGORIJA`) VALUES
('PROD0001', 'Deimantinis aukso žiedas', 'Elegantiškas deimantinis žiedas iš aukštos kokybės aukso.', 1299.99, 5.20, 'Auksas', 'TIFF001', 1),
('PROD0002', 'Sidabrinis sužadėtuvių žiedas', 'Klasikinis sidabrinis žiedas su mažu deimantu.', 499.99, 4.50, 'Sidabras', 'CART002', 1),
('PROD0003', 'Platinos vestuviniai žiedai (pora)', 'Aukščiausios kokybės platinos vestuvinių žiedų pora.', 1899.99, 12.00, 'Platina', 'BVLG003', 1),
('PROD0004', 'Gintarinis žiedas', 'Tradicinis žiedas su gintaro akmeniu.', 199.99, 3.80, 'Sidabras, Gintaras', 'AMYR008', 1),
('PROD0005', 'Rubino žiedas', 'Prabangus žiedas su rubino akmeniu.', 899.99, 4.20, 'Baltas auksas', 'CART002', 1),
('PROD0006', 'Minimalistinis žiedas', 'Modernaus dizaino minimalistinis žiedas.', 149.99, 2.50, 'Sidabras', 'PAND004', 1),
('PROD0007', 'Perlų vėrinys', 'Elegantiškas perlų vėrinys su sidabrine užsegimo sistema.', 299.99, 18.50, 'Perlai, Sidabras', 'PAND004', 2),
('PROD0008', 'Aukso grandinėlė', 'Klasikinio dizaino aukso grandinėlė.', 699.99, 12.30, 'Auksas', 'SWRV005', 2),
('PROD0009', 'Gintaro karoliai', 'Tradiciniai gintaro karoliai su unikaliais natūraliais gintaro akmenimis.', 149.99, 22.00, 'Gintaras', 'AMYR008', 2),
('PROD0010', 'Sidabrinis vėrinys su safyru', 'Elegantiškas sidabrinis vėrinys su safyro akmeniu.', 249.99, 15.00, 'Sidabras, Safyras', 'BVLG003', 2),
('PROD0011', 'Chokeris su kristalais', 'Modernus chokerio stiliaus vėrinys su Swarovski kristalais.', 179.99, 14.00, 'Oda, Kristalai', 'SWRV005', 2),
('PROD0012', 'Ilgas perlų vėrinys', 'Klasikinis ilgas perlų vėrinys.', 349.99, 35.00, 'Perlai', 'TIFF001', 2),
('PROD0013', 'Odinė apyrankė vyrams', 'Stilinga odinė apyrankė su nerūdijančio plieno elementais.', 89.99, 15.00, 'Oda, Plienas', 'HRMS006', 3),
('PROD0014', 'Sidabrinė tenisinė apyrankė', 'Klasikinė sidabrinė tenisinė apyrankė su cirkoniais.', 199.99, 14.20, 'Sidabras', 'GCCI007', 3),
('PROD0015', 'Gintaro apyrankė', 'Moderni apyrankė su gintaro intarpais.', 129.99, 16.50, 'Gintaras, Sidabras', 'AMYR008', 3),
('PROD0016', 'Auksinė grandinė apyrankė', 'Elegantiška auksinė grandinės apyrankė.', 599.99, 18.00, 'Auksas', 'CART002', 3),
('PROD0017', 'Charm apyrankė', 'Sidabrinė apyrankė su keičiamais pakabutėliais.', 149.99, 17.50, 'Sidabras', 'PAND004', 3),
('PROD0018', 'Sportinė apyrankė', 'Titaninė apyrankė aktyviam gyvenimo būdui.', 129.99, 12.00, 'Titanas', 'SWRV005', 3),
('PROD0019', 'Deimantiniai sagučiai', 'Klasikiniai deimanto sagučiai.', 899.99, 1.80, 'Baltas auksas', 'TIFF001', 4),
('PROD0020', 'Sidabriniai kabantys auskarai', 'Elegantiški kabantys auskarai su cirkoniais.', 149.99, 3.20, 'Sidabras', 'CART002', 4),
('PROD0021', 'Gintaro auskarai', 'Tradiciniai gintaro auskarai su sidabriniais elementais.', 79.99, 2.50, 'Gintaras, Sidabras', 'AMYR008', 4),
('PROD0022', 'Perlų auskarai', 'Klasikiniai perlų sagučiai.', 129.99, 2.00, 'Perlai, Sidabras', 'PAND004', 4),
('PROD0023', 'Chandelier auskarai', 'Prabangūs ilgi chandelier stiliaus auskarai.', 199.99, 5.00, 'Sidabras, Kristalai', 'BVLG003', 4),
('PROD0024', 'Minimalistiniai auskarai', 'Modernaus dizaino geometriniai auskarai.', 69.99, 1.50, 'Sidabras', 'HRMS006', 4),
('PROD0025', 'Vyriškas sportinis laikrodis', 'Aukštos kokybės sportinis laikrodis su daugybe funkcijų.', 299.99, 85.00, 'Nerūdijantis plienas', 'SWRV005', 5),
('PROD0026', 'Moteriškas prabangus laikrodis', 'Elegantiškas moteriškas laikrodis su deimantais.', 1999.99, 45.00, 'Rožinis auksas', 'CART002', 5),
('PROD0027', 'Unisex minimalistinis laikrodis', 'Modernus, minimalistinio dizaino laikrodis.', 199.99, 52.00, 'Titanas', 'BVLG003', 5),
('PROD0028', 'Išmanusis laikrodis', 'Aukštos kokybės išmanusis laikrodis su visomis funkcijomis.', 399.99, 48.00, 'Aliuminis', 'TIFF001', 5),
('PROD0029', 'Vintažinis laikrodis', 'Klasikinio dizaino vintažinis laikrodis.', 299.99, 60.00, 'Žalvaris', 'BCVR009', 5),
('PROD0030', 'Kišeninis laikrodis', 'Elegantiškas kišeninis laikrodis su grandinėle.', 259.99, 75.00, 'Sidabras', 'LTHR010', 5),
('PROD0031', 'Širdies formos pakabukas', 'Romantiškas širdies formos pakabukas su cirkoniais.', 89.99, 3.50, 'Sidabras', 'PAND004', 6),
('PROD0032', 'Gintarinis pakabukas su vabzdžiu', 'Unikalus gintaro pakabukas su suakmenėjusiu vabzdžiu.', 199.99, 5.20, 'Gintaras, Sidabras', 'AMYR008', 6),
('PROD0033', 'Kryžiaus formos pakabukas', 'Tradicinis kryžiaus formos pakabukas.', 129.99, 4.00, 'Auksas', 'TIFF001', 6),
('PROD0034', 'Inicialų pakabukas', 'Personalizuotas pakabukas su inicialais.', 99.99, 3.00, 'Sidabras', 'CART002', 6),
('PROD0035', 'Zodiako ženklo pakabukas', 'Pakabukas su zodiako ženklo simboliu.', 79.99, 2.80, 'Sidabras', 'SWRV005', 6),
('PROD0036', 'Geometrinis pakabukas', 'Modernaus dizaino geometrinis pakabukas.', 69.99, 2.50, 'Nerūdijantis plienas', 'GCCI007', 6),
('PROD0037', 'Gėlės formos sagė', 'Elegantiška gėlės formos sagė su spalvotais akmenimis.', 149.99, 8.50, 'Sidabras', 'BCVR009', 7),
('PROD0038', 'Vintage stiliaus sagė', 'Klasikinė vintage stiliaus sagė su perlais.', 199.99, 10.20, 'Sidabras, Perlai', 'LTHR010', 7),
('PROD0039', 'Modernistinė geometrinė sagė', 'Šiuolaikinė geometrinių formų sagė.', 179.99, 7.80, 'Nerūdijantis plienas', 'GCCI007', 7),
('PROD0040', 'Drugelio sagė', 'Detaliai išdirbta drugelio formos sagė.', 159.99, 9.00, 'Sidabras, Emalė', 'BVLG003', 7),
('PROD0041', 'Art deco sagė', 'Elegantiška art deco periodo stiliaus sagė.', 229.99, 12.50, 'Sidabras, Onikso', 'CART002', 7),
('PROD0042', 'Gyvūno formos sagė', 'Žaisminga gyvūno formos sagė.', 139.99, 8.20, 'Sidabras, Emalė', 'PAND004', 7),
('PROD0043', 'Sidabrinė kojos grandinėlė', 'Subtili sidabrinė kojos grandinėlė.', 69.99, 5.20, 'Sidabras', 'PAND004', 8),
('PROD0044', 'Kojos apyrankė su pakabutėliais', 'Linksma kojos apyrankė su įvairiais pakabutėliais.', 89.99, 7.50, 'Sidabras', 'SWRV005', 8),
('PROD0045', 'Perlų kojos papuošalas', 'Elegantiškas kojos papuošalas su perlais.', 99.99, 6.30, 'Perlai, Sidabras', 'BCVR009', 8),
('PROD0046', 'Vasaros kojos grandinėlė', 'Lengva kojos grandinėlė vasaros sezonui.', 59.99, 4.50, 'Sidabras', 'AMYR008', 8),
('PROD0047', 'Paplūdimio kojos papuošalas', 'Spalvingas kojos papuošalas paplūdimiui.', 49.99, 5.00, 'Sidabras, Spalvoti akmenys', 'LTHR010', 8),
('PROD0048', 'Boho stiliaus kojos papuošalas', 'Daugiasluoksnis boho stiliaus kojos papuošalas.', 79.99, 8.00, 'Sidabras, Oda', 'HRMS006', 8),
('PROD0049', 'Vestuvinė tiara', 'Prabangaus dizaino vestuvinė tiara su kristalais.', 399.99, 120.00, 'Sidabras, Kristalai', 'TIFF001', 9),
('PROD0050', 'Vestuvių žiedų pagalvėlė', 'Rankomis siuvinėta vestuvių žiedų pagalvėlė.', 59.99, 80.00, 'Šilkas, Satinas', 'LTHR010', 9),
('PROD0051', 'Vestuvinė apyrankė nuotakai', 'Subtili apyrankė nuotakai su kristalais.', 149.99, 8.50, 'Sidabras, Kristalai', 'CART002', 9),
('PROD0052', 'Vestuviniai plaukų papuošalai', 'Elegantiški plaukų papuošalai nuotakai.', 129.99, 15.00, 'Sidabras, Perlai', 'SWRV005', 9),
('PROD0053', 'Jaunikio sagė', 'Stilinga sagė jaunikiui.', 89.99, 6.00, 'Sidabras', 'GCCI007', 9),
('PROD0054', 'Nuotakos vualio segė', 'Subtili segė nuotakos vualį pritvirtinti.', 79.99, 4.00, 'Sidabras, Kristalai', 'BVLG003', 9),
('PROD0055', 'Perlų rinkinys (vėrinys ir auskarai)', 'Elegantiškas perlų vėrinys su derančiais auskarais.', 399.99, 25.00, 'Perlai, Sidabras', 'BVLG003', 10),
('PROD0056', 'Gintaro rinkinys', 'Trijų dalių gintaro rinkinys - vėrinys, auskarai ir žiedas.', 349.99, 32.50, 'Gintaras, Sidabras', 'AMYR008', 10),
('PROD0057', 'Deimantų rinkinys ypatingai progai', 'Prabangus rinkinys ypatingoms progoms su deimantais.', 2999.99, 18.70, 'Baltas auksas, Deimantai', 'TIFF001', 10),
('PROD0058', 'Vestuvių papuošalų rinkinys', 'Pilnas vestuvinis papuošalų rinkinys nuotakai.', 799.99, 45.00, 'Sidabras, Perlai, Kristalai', 'CART002', 10),
('PROD0059', 'Verslo stiliaus rinkinys', 'Elegantiškas rinkinys verslui - sagė ir auskarai.', 299.99, 14.00, 'Sidabras', 'HRMS006', 10),
('PROD0060', 'Kasdieninio dėvėjimo rinkinys', 'Minimalistinis rinkinys kasdienai.', 199.99, 12.00, 'Sidabras', 'PAND004', 10);

-- --------------------------------------------------------

--
-- Table structure for table `sandeliuojama_preke`
--

CREATE TABLE `sandeliuojama_preke` (
  `id_SANDELIUOJAMA_PREKE` int(11) NOT NULL,
  `kiekis` int(11) NOT NULL DEFAULT 0,
  `fk_PREKEid` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sandeliuojama_preke`
--

INSERT INTO `sandeliuojama_preke` (`id_SANDELIUOJAMA_PREKE`, `kiekis`, `fk_PREKEid`) VALUES
(1, 5, 'PROD0001'),
(2, 3, 'PROD0001'),
(3, 2, 'PROD0001'),
(4, 10, 'PROD0002'),
(5, 7, 'PROD0002'),
(6, 3, 'PROD0002'),
(7, 2, 'PROD0003'),
(8, 1, 'PROD0003'),
(9, 12, 'PROD0004'),
(10, 8, 'PROD0004'),
(11, 6, 'PROD0005'),
(12, 4, 'PROD0005'),
(13, 15, 'PROD0006'),
(14, 10, 'PROD0006'),
(15, 8, 'PROD0007'),
(16, 5, 'PROD0007'),
(17, 12, 'PROD0008'),
(18, 0, 'PROD0008'),
(19, 15, 'PROD0009'),
(20, 9, 'PROD0009'),
(21, 7, 'PROD0010'),
(22, 3, 'PROD0010'),
(23, 11, 'PROD0011'),
(24, 6, 'PROD0011'),
(25, 4, 'PROD0012'),
(26, 2, 'PROD0012'),
(27, 20, 'PROD0013'),
(28, 8, 'PROD0013'),
(29, 6, 'PROD0014'),
(30, 4, 'PROD0014'),
(31, 9, 'PROD0015'),
(32, 7, 'PROD0015'),
(33, 5, 'PROD0016'),
(34, 3, 'PROD0016'),
(35, 14, 'PROD0017'),
(36, 8, 'PROD0017'),
(37, 12, 'PROD0018'),
(38, 9, 'PROD0018'),
(39, 15, 'PROD0019'),
(40, 12, 'PROD0019'),
(41, 25, 'PROD0020'),
(42, 0, 'PROD0020'),
(43, 18, 'PROD0021'),
(44, 13, 'PROD0021'),
(45, 10, 'PROD0022'),
(46, 5, 'PROD0022'),
(47, 8, 'PROD0023'),
(48, 4, 'PROD0023'),
(49, 22, 'PROD0024'),
(50, 16, 'PROD0024'),
(51, 5, 'PROD0025'),
(52, 3, 'PROD0025'),
(53, 7, 'PROD0026'),
(54, 2, 'PROD0026'),
(55, 10, 'PROD0027'),
(56, 6, 'PROD0027'),
(57, 8, 'PROD0028'),
(58, 5, 'PROD0028'),
(59, 4, 'PROD0029'),
(60, 2, 'PROD0029'),
(61, 6, 'PROD0030'),
(62, 3, 'PROD0030'),
(63, 30, 'PROD0031'),
(64, 15, 'PROD0031'),
(65, 8, 'PROD0032'),
(66, 5, 'PROD0032'),
(67, 12, 'PROD0033'),
(68, 8, 'PROD0033'),
(69, 18, 'PROD0034'),
(70, 10, 'PROD0034'),
(71, 25, 'PROD0035'),
(72, 15, 'PROD0035'),
(73, 20, 'PROD0036'),
(74, 12, 'PROD0036'),
(75, 7, 'PROD0037'),
(76, 4, 'PROD0037'),
(77, 9, 'PROD0038'),
(78, 6, 'PROD0038'),
(79, 11, 'PROD0039'),
(80, 7, 'PROD0039'),
(81, 5, 'PROD0040'),
(82, 3, 'PROD0040'),
(83, 8, 'PROD0041'),
(84, 5, 'PROD0041'),
(85, 12, 'PROD0042'),
(86, 8, 'PROD0042'),
(87, 14, 'PROD0043'),
(88, 8, 'PROD0043'),
(89, 11, 'PROD0044'),
(90, 7, 'PROD0044'),
(91, 9, 'PROD0045'),
(92, 5, 'PROD0045'),
(93, 16, 'PROD0046'),
(94, 10, 'PROD0046'),
(95, 20, 'PROD0047'),
(96, 15, 'PROD0047'),
(97, 13, 'PROD0048'),
(98, 7, 'PROD0048'),
(99, 3, 'PROD0049'),
(100, 2, 'PROD0049'),
(101, 15, 'PROD0050'),
(102, 10, 'PROD0050'),
(103, 8, 'PROD0051'),
(104, 5, 'PROD0051'),
(105, 6, 'PROD0052'),
(106, 4, 'PROD0052'),
(107, 9, 'PROD0053'),
(108, 6, 'PROD0053'),
(109, 12, 'PROD0054'),
(110, 8, 'PROD0054'),
(111, 5, 'PROD0055'),
(112, 3, 'PROD0055'),
(113, 7, 'PROD0056'),
(114, 4, 'PROD0056'),
(115, 2, 'PROD0057'),
(116, 1, 'PROD0057'),
(117, 6, 'PROD0058'),
(118, 3, 'PROD0058'),
(119, 8, 'PROD0059'),
(120, 4, 'PROD0059'),
(121, 10, 'PROD0060'),
(122, 7, 'PROD0060');

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
  ADD KEY `idx_preke_kategorija` (`fk_KATEGORIJAid_KATEGORIJA`),
  ADD KEY `idx_preke_gamintojas` (`fk_GAMINTOJASgamintojo_id`);

--
-- Indexes for table `sandeliuojama_preke`
--
ALTER TABLE `sandeliuojama_preke`
  ADD PRIMARY KEY (`id_SANDELIUOJAMA_PREKE`),
  ADD KEY `idx_sandeliuojama_preke_produktas` (`fk_PREKEid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `kategorija`
--
ALTER TABLE `kategorija`
  MODIFY `id_KATEGORIJA` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `sandeliuojama_preke`
--
ALTER TABLE `sandeliuojama_preke`
  MODIFY `id_SANDELIUOJAMA_PREKE` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `preke`
--
ALTER TABLE `preke`
  ADD CONSTRAINT `FK_preke_gamintojas` FOREIGN KEY (`fk_GAMINTOJASgamintojo_id`) REFERENCES `gamintojas` (`gamintojo_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_preke_kategorija` FOREIGN KEY (`fk_KATEGORIJAid_KATEGORIJA`) REFERENCES `kategorija` (`id_KATEGORIJA`) ON UPDATE CASCADE;

--
-- Constraints for table `sandeliuojama_preke`
--
ALTER TABLE `sandeliuojama_preke`
  ADD CONSTRAINT `FK_sandeliuojama_preke_preke` FOREIGN KEY (`fk_PREKEid`) REFERENCES `preke` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
