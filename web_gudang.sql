-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 09, 2025 at 04:19 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `web_gudang`
--

-- --------------------------------------------------------

--
-- Table structure for table `tb_barang_keluar`
--

CREATE TABLE `tb_barang_keluar` (
  `id` int(10) NOT NULL,
  `id_transaksi` varchar(50) NOT NULL,
  `tanggal_masuk` varchar(20) NOT NULL,
  `tanggal_keluar` varchar(20) NOT NULL,
  `lokasi` varchar(100) NOT NULL,
  `brand` varchar(30) NOT NULL,
  `jenis_item` varchar(30) NOT NULL,
  `po_number` varchar(30) NOT NULL,
  `art` varchar(30) NOT NULL,
  `color` varchar(40) NOT NULL,
  `kode_barang` varchar(100) NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `satuan` varchar(50) NOT NULL,
  `jumlah` varchar(10) NOT NULL,
  `dept_tujuan` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tb_barang_keluar`
--

INSERT INTO `tb_barang_keluar` (`id`, `id_transaksi`, `tanggal_masuk`, `tanggal_keluar`, `lokasi`, `brand`, `jenis_item`, `po_number`, `art`, `color`, `kode_barang`, `nama_barang`, `satuan`, `jumlah`, `dept_tujuan`) VALUES
(31, 'WG-202541965230', '08/07/2025', '08/07/2025', 'WAREHOUSE', 'ROSSI', 'ACCESORIES', '123', 'CG 565', 'BROWN', '1', '11', 'FTK', '100', 'Cutting/Setting');

--
-- Triggers `tb_barang_keluar`
--
DELIMITER $$
CREATE TRIGGER `TG_BARANG_KELUAR` AFTER INSERT ON `tb_barang_keluar` FOR EACH ROW BEGIN
 UPDATE tb_barang_masuk SET jumlah=jumlah-NEW.jumlah
 WHERE kode_barang=NEW.kode_barang;
 DELETE FROM tb_barang_masuk WHERE jumlah = 0;

END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `tb_barang_masuk`
--

CREATE TABLE `tb_barang_masuk` (
  `id_transaksi` varchar(50) NOT NULL,
  `tanggal` varchar(20) NOT NULL,
  `lokasi` varchar(100) NOT NULL,
  `brand` varchar(30) NOT NULL,
  `jenis_item` varchar(30) NOT NULL,
  `po_number` varchar(40) NOT NULL,
  `art` varchar(40) NOT NULL,
  `color` varchar(40) NOT NULL,
  `kode_barang` varchar(100) NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `satuan` varchar(50) NOT NULL,
  `jumlah` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_brand`
--

CREATE TABLE `tb_brand` (
  `id_brand` int(11) NOT NULL,
  `kode_brand` varchar(30) NOT NULL,
  `nama_brand` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_jenis_item`
--

CREATE TABLE `tb_jenis_item` (
  `id_item` int(11) NOT NULL,
  `kode_item` varchar(50) NOT NULL,
  `jenis_item` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_jenis_item`
--

INSERT INTO `tb_jenis_item` (`id_item`, `kode_item`, `jenis_item`) VALUES
(1, 'LEATHER', 'LEATHER'),
(2, 'CHEMICAL', 'CHEMICAL'),
(3, 'ACCESORIES', 'ACCESORIES'),
(4, 'OUTSOLE', 'OUTSOLE'),
(5, 'TEXON', 'TEXON');

-- --------------------------------------------------------

--
-- Table structure for table `tb_po_number`
--

CREATE TABLE `tb_po_number` (
  `po_number` varchar(30) NOT NULL,
  `brand` varchar(30) NOT NULL,
  `art` varchar(30) NOT NULL,
  `color` varchar(30) NOT NULL,
  `qty_order` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_po_number`
--

INSERT INTO `tb_po_number` (`po_number`, `brand`, `art`, `color`, `qty_order`) VALUES
('123', 'BLACKSTONE', 'CG 565', 'BROWN', 1),
('IO25-0104 -24', 'BLACKSTONE', 'EG 60', 'MALT BALL', 20);

-- --------------------------------------------------------

--
-- Table structure for table `tb_satuan`
--

CREATE TABLE `tb_satuan` (
  `id_satuan` int(11) NOT NULL,
  `kode_satuan` varchar(100) NOT NULL,
  `nama_satuan` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tb_satuan`
--

INSERT INTO `tb_satuan` (`id_satuan`, `kode_satuan`, `nama_satuan`) VALUES
(1, 'FTK', 'FTK'),
(2, 'PCS', 'PCS'),
(5, 'CONE', 'CONE'),
(6, 'KGM', 'KGM'),
(7, 'ROLL', 'ROLL'),
(8, 'PCE', 'PCE'),
(9, 'MTR', 'MTR'),
(10, 'MTK', 'MTK'),
(11, 'LTR', 'LTR'),
(12, 'SET', 'SET'),
(13, 'SHT', 'SHT'),
(14, 'YARD', 'YARD'),
(15, 'NPR', 'NRP'),
(16, 'TIN', 'TIN');

-- --------------------------------------------------------

--
-- Table structure for table `tb_upload_gambar_user`
--

CREATE TABLE `tb_upload_gambar_user` (
  `id` int(11) NOT NULL,
  `username_user` varchar(100) NOT NULL,
  `nama_file` varchar(220) NOT NULL,
  `ukuran_file` varchar(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tb_upload_gambar_user`
--

INSERT INTO `tb_upload_gambar_user` (`id`, `username_user`, `nama_file`, `ukuran_file`) VALUES
(1, 'zahidin', 'nopic5.png', '6.33'),
(2, 'test', 'nopic4.png', '6.33'),
(3, 'coba', 'logo_unsada1.jpg', '16.69'),
(4, 'admin', 'nopic2.png', '6.33'),
(5, 'b', 'nopic2.png', '6.33');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(12) NOT NULL,
  `username` varchar(200) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(200) NOT NULL,
  `role` tinyint(4) NOT NULL DEFAULT 0,
  `last_login` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `email`, `password`, `role`, `last_login`) VALUES
(20, 'admin', 'admin@gmail.com', '$2y$10$3HNkMOtwX8X88Xb3DIveYuhXScTnJ9m16/rPDF1/VTa/VTisxVZ4i', 1, '08-07-2025 11:20'),
(23, 'bagas', 'bagasprastyowibowo@gmail.com', '$2y$10$BYOL.oO4VTBqNvVfZ4pdtOm.4vNEO7r1D8.RNdTej5YDOYvoEbN.G', 1, '03-07-2025 6:20'),
(24, 'nathan', 'nathan.tannady@gmail.com', '$2y$10$7UVr37AuudrxqP3ueu5B7.0javf2zSer4mtrxsXBoyBQ03O/TD3dO', 0, '08-07-2025 11:11'),
(25, 'cipto', 'tjipto.tannady@gmail.com', '$2y$10$rACd30iUhYGGgnWvE0ihlObc71YTZgcfh2IxfTfmRimf7mo7XP6nK', 0, '08-07-2025 11:09'),
(26, 'b', 'b@gmail.com', '$2y$10$4mWNZgDpV2K3G8cME3heKO0oZVM1SPraH2FjbTy6gOksKGEyC36Iy', 0, '03-07-2025 11:41');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_barang_keluar`
--
ALTER TABLE `tb_barang_keluar`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_barang_masuk`
--
ALTER TABLE `tb_barang_masuk`
  ADD PRIMARY KEY (`id_transaksi`);

--
-- Indexes for table `tb_brand`
--
ALTER TABLE `tb_brand`
  ADD PRIMARY KEY (`id_brand`);

--
-- Indexes for table `tb_jenis_item`
--
ALTER TABLE `tb_jenis_item`
  ADD PRIMARY KEY (`id_item`);

--
-- Indexes for table `tb_po_number`
--
ALTER TABLE `tb_po_number`
  ADD PRIMARY KEY (`po_number`);

--
-- Indexes for table `tb_satuan`
--
ALTER TABLE `tb_satuan`
  ADD PRIMARY KEY (`id_satuan`);

--
-- Indexes for table `tb_upload_gambar_user`
--
ALTER TABLE `tb_upload_gambar_user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_barang_keluar`
--
ALTER TABLE `tb_barang_keluar`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `tb_brand`
--
ALTER TABLE `tb_brand`
  MODIFY `id_brand` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tb_jenis_item`
--
ALTER TABLE `tb_jenis_item`
  MODIFY `id_item` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tb_satuan`
--
ALTER TABLE `tb_satuan`
  MODIFY `id_satuan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `tb_upload_gambar_user`
--
ALTER TABLE `tb_upload_gambar_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
