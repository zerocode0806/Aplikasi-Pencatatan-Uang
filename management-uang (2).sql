-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 24, 2026 at 06:25 AM
-- Server version: 8.0.30
-- PHP Version: 8.3.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `management-uang`
--

-- --------------------------------------------------------

--
-- Table structure for table `sheet_data`
--

CREATE TABLE `sheet_data` (
  `id_data` int NOT NULL,
  `id_sheet` int NOT NULL,
  `tanggal` timestamp NOT NULL,
  `keterangan` varchar(50) NOT NULL,
  `notes` varchar(255) NOT NULL,
  `pemasukan` int DEFAULT NULL,
  `pengeluaran` int DEFAULT NULL,
  `saldo` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sheet_data`
--

INSERT INTO `sheet_data` (`id_data`, `id_sheet`, `tanggal`, `keterangan`, `notes`, `pemasukan`, `pengeluaran`, `saldo`) VALUES
(54, 3, '2025-04-10 17:00:00', 'Sisa saldo', '', 377500, 0, 377500),
(55, 3, '2025-04-10 17:00:00', 'Rutinan', '', 36000, 0, 413500),
(56, 3, '2025-04-10 17:00:00', 'HBH', '', 220000, 0, 633500),
(57, 3, '2025-04-10 17:00:00', 'Rutinan', '', 20000, 0, 653500),
(58, 3, '2025-04-18 17:00:00', 'Print', '', 0, 13000, 640500),
(59, 3, '2025-04-24 17:00:00', 'Konsumsi', '', 0, 26000, 614500),
(60, 3, '2025-05-01 17:00:00', 'Rutinan', '', 26000, 0, 651700),
(61, 3, '2025-05-08 17:00:00', 'Rutinan', '', 18000, 0, 669700),
(62, 3, '2025-05-18 17:00:00', 'Rutinan', '', 19000, 0, 688700),
(63, 3, '2025-05-24 17:00:00', 'Rutinan', '', 11200, 0, 625700),
(64, 3, '2025-05-29 17:00:00', 'Rutinan', '', 32000, 0, 720700),
(65, 3, '2025-06-06 17:00:00', 'DP Badminton', '', 0, 100000, 620700),
(66, 3, '2025-06-12 17:00:00', 'Rutinan', '', 30000, 0, 650700),
(67, 3, '2025-06-19 17:00:00', 'Rutinan', '', 2600, 0, 652200),
(68, 3, '2025-06-24 17:00:00', 'Rutinan', '', 38500, 0, 691200),
(69, 3, '2025-07-03 17:00:00', 'Rutinan', '', 12000, 0, 701200),
(70, 3, '2025-07-10 17:00:00', 'Rutinan', '', 22000, 0, 723200),
(71, 3, '2025-07-17 17:00:00', 'Rutinan', '', 20000, 0, 743200),
(72, 3, '2025-08-11 17:00:00', 'Rutinan', '', 10000, 0, 753200),
(73, 3, '2025-08-14 17:00:00', 'Rutinan', '', 10000, 0, 763200),
(74, 3, '2025-08-14 17:00:00', 'Sumbangan masjid', '', 0, 100000, 663200),
(75, 3, '2025-08-21 17:00:00', 'Rutinan', '', 14000, 0, 677200),
(76, 3, '2025-08-22 17:00:00', 'Print', '', 0, 15000, 662200),
(77, 3, '2025-08-25 17:00:00', 'Reward kehadiran', '', 0, 100000, 585200),
(78, 3, '2025-08-28 17:00:00', 'Rutinan', '', 23000, 0, 685200),
(79, 3, '2025-09-02 17:00:00', 'Olahraga DP', '', 0, 100000, 485200),
(80, 3, '2025-09-25 17:00:00', 'Sisa Mapesta', '', 750000, 0, 1235200),
(81, 3, '2025-10-09 17:00:00', 'Rutinan', '', 14000, 0, 1249200),
(82, 3, '2025-11-06 17:00:00', 'Rutinan', '', 65000, 0, 1314200),
(83, 5, '2026-02-01 17:00:00', 'Test', '', 20000, 0, 20000),
(84, 5, '2026-01-31 17:00:00', 'Test', '', 10000, 0, 30000),
(85, 5, '2026-02-02 17:00:00', 'Test', '', 0, 10000, 20000),
(86, 5, '2026-02-04 17:00:00', 'Test', '', 50000, 0, 70000),
(87, 5, '2026-02-05 17:00:00', 'Test', 'Tuntas ', 20000, 0, 90000),
(88, 5, '2026-02-10 17:00:00', 'Test', 'Tuntas', 45000, 0, 135000);

-- --------------------------------------------------------

--
-- Table structure for table `sheet_info`
--

CREATE TABLE `sheet_info` (
  `id_sheet` int NOT NULL,
  `nama` varchar(50) NOT NULL,
  `kategori` varchar(50) NOT NULL,
  `saldo_awal` int NOT NULL,
  `deskripsi` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sheet_info`
--

INSERT INTO `sheet_info` (`id_sheet`, `nama`, `kategori`, `saldo_awal`, `deskripsi`) VALUES
(3, 'kas IPNU IPPNU', 'Organisasi', 0, 'Buku kas IPNU IPPNU'),
(5, 'Uji Coba', 'Pribadi', 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` int NOT NULL,
  `nama` varchar(50) NOT NULL,
  `usernama` varchar(30) NOT NULL,
  `password` varchar(30) NOT NULL,
  `level` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `nama`, `usernama`, `password`, `level`) VALUES
(1, 'Ubeddahlan ', 'ubeddahlan', 'krian123', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `sheet_data`
--
ALTER TABLE `sheet_data`
  ADD PRIMARY KEY (`id_data`);

--
-- Indexes for table `sheet_info`
--
ALTER TABLE `sheet_info`
  ADD PRIMARY KEY (`id_sheet`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `sheet_data`
--
ALTER TABLE `sheet_data`
  MODIFY `id_data` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT for table `sheet_info`
--
ALTER TABLE `sheet_info`
  MODIFY `id_sheet` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
