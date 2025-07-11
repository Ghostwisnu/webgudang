-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping data for table webgudang.tb_art: ~2 rows (approximately)
INSERT INTO `tb_art` (`id_art`, `art_name`) VALUES
	(1, 'CG 104'),
	(3, 'EG 555'),
	(4, 'CG177');

-- Dumping data for table webgudang.tb_art_color: ~2 rows (approximately)
INSERT INTO `tb_art_color` (`id_artcolor`, `art_name`, `color_name`, `artcolor_name`) VALUES
	(1, 'CG 104', 'BLACK', 'CG 104 BLACK'),
	(2, 'EG 555', 'GUN METAL', 'EG 555 GUN METAL'),
	(3, 'CG177', 'BLACK COFFEE', 'CG177 BLACK COFFEE');

-- Dumping data for table webgudang.tb_barang_keluar: ~0 rows (approximately)
INSERT INTO `tb_barang_keluar` (`id`, `id_transaksi`, `tanggal_masuk`, `tanggal_keluar`, `lokasi`, `brand`, `jenis_item`, `po_number`, `art`, `color`, `kode_barang`, `nama_barang`, `satuan`, `jumlah`, `dept_tujuan`) VALUES
	(31, 'WG-202541965230', '08/07/2025', '08/07/2025', 'WAREHOUSE', 'ROSSI', 'ACCESORIES', '123', 'CG 565', 'BROWN', '1', '11', 'FTK', '100', 'Cutting/Setting');

-- Dumping data for table webgudang.tb_barang_masuk: ~0 rows (approximately)

-- Dumping data for table webgudang.tb_brand: ~1 rows (approximately)
INSERT INTO `tb_brand` (`id_brand`, `brand_name`) VALUES
	(1, 'BLACK STONE');

-- Dumping data for table webgudang.tb_color: ~1 rows (approximately)
INSERT INTO `tb_color` (`id_color`, `color_name`) VALUES
	(1, 'BLACK'),
	(2, 'GUN METAL'),
	(3, 'BLACK COFFEE');

-- Dumping data for table webgudang.tb_itemlist: ~2 rows (approximately)
INSERT INTO `tb_itemlist` (`id_itemlist`, `item_name`, `unit_name`) VALUES
	(1, 'LATEX CAIR 60% @ 180 KG', 'LTR'),
	(2, 'STICKER PITOGRAM / GOLD', 'PCE');

-- Dumping data for table webgudang.tb_jenis_item: ~5 rows (approximately)
INSERT INTO `tb_jenis_item` (`id_item`, `kode_item`, `jenis_item`) VALUES
	(1, 'LEATHER', 'LEATHER'),
	(2, 'CHEMICAL', 'CHEMICAL'),
	(3, 'ACCESORIES', 'ACCESORIES'),
	(4, 'OUTSOLE', 'OUTSOLE'),
	(5, 'TEXON', 'TEXON');

-- Dumping data for table webgudang.tb_listcons: ~0 rows (approximately)
INSERT INTO `tb_listcons` (`id_cons`, `artcolor_name`, `item_name`, `unit_name`, `cons_rate`) VALUES
	(1, 'CG 104 BLACK', 'LATEX CAIR 60% @ 180 KG', 'LTR', 0.0250);

-- Dumping data for table webgudang.tb_po_number: ~2 rows (approximately)
INSERT INTO `tb_po_number` (`id_po`, `po_number`, `artcolor_name`, `brand_name`, `xfd`, `qty_total`) VALUES
	(3, 'IO24-0152', 'CG177 BLACK COFFEE', 'BLACK STONE', '2024-03-29', 0);

-- Dumping data for table webgudang.tb_satuan: ~14 rows (approximately)
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

-- Dumping data for table webgudang.tb_size: ~0 rows (approximately)
INSERT INTO `tb_size` (`id_size`, `size_name`) VALUES
	(1, 39),
	(2, 40),
	(3, 41),
	(4, 42),
	(5, 43),
	(6, 44),
	(7, 45),
	(8, 46),
	(9, 47),
	(10, 48),
	(11, 49),
	(12, 50);

-- Dumping data for table webgudang.tb_size_run: ~0 rows (approximately)

-- Dumping data for table webgudang.tb_unitlist: ~2 rows (approximately)
INSERT INTO `tb_unitlist` (`id_unit`, `unit_name`) VALUES
	(1, 'LTR'),
	(2, 'PCE');

-- Dumping data for table webgudang.tb_upload_gambar_user: ~5 rows (approximately)
INSERT INTO `tb_upload_gambar_user` (`id`, `username_user`, `nama_file`, `ukuran_file`) VALUES
	(1, 'zahidin', 'nopic5.png', '6.33'),
	(2, 'test', 'nopic4.png', '6.33'),
	(3, 'coba', 'logo_unsada1.jpg', '16.69'),
	(4, 'admin', 'nopic2.png', '6.33'),
	(5, 'b', 'nopic2.png', '6.33'),
	(6, 'wisnu', 'nopic2.png', '6.33');

-- Dumping data for table webgudang.user: ~5 rows (approximately)
INSERT INTO `user` (`id`, `username`, `email`, `password`, `role`, `last_login`) VALUES
	(20, 'admin', 'admin@gmail.com', '$2y$10$3HNkMOtwX8X88Xb3DIveYuhXScTnJ9m16/rPDF1/VTa/VTisxVZ4i', 1, '11-07-2025 1:13'),
	(23, 'bagas', 'bagasprastyowibowo@gmail.com', '$2y$10$BYOL.oO4VTBqNvVfZ4pdtOm.4vNEO7r1D8.RNdTej5YDOYvoEbN.G', 1, '03-07-2025 6:20'),
	(24, 'nathan', 'nathan.tannady@gmail.com', '$2y$10$7UVr37AuudrxqP3ueu5B7.0javf2zSer4mtrxsXBoyBQ03O/TD3dO', 0, '08-07-2025 11:11'),
	(25, 'cipto', 'tjipto.tannady@gmail.com', '$2y$10$rACd30iUhYGGgnWvE0ihlObc71YTZgcfh2IxfTfmRimf7mo7XP6nK', 0, '08-07-2025 11:09'),
	(26, 'b', 'b@gmail.com', '$2y$10$4mWNZgDpV2K3G8cME3heKO0oZVM1SPraH2FjbTy6gOksKGEyC36Iy', 0, '03-07-2025 11:41'),
	(27, 'wisnu', 'wisnu@mail.com', '$2y$10$fOdZMFTuxxKWQZ/qizkaRONZN9VnNrq/TFx3.BWCcFQcddOVvNcDO', 1, '');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
