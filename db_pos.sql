-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 03 Des 2025 pada 07.12
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_pos`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `kasir_admin`
--

CREATE TABLE `kasir_admin` (
  `AdminID` int(11) NOT NULL,
  `Username` varchar(50) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `NamaLengkap` varchar(100) NOT NULL,
  `Status` enum('active','inactive') DEFAULT 'inactive',
  `ActivationCode` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kasir_admin`
--

INSERT INTO `kasir_admin` (`AdminID`, `Username`, `Password`, `NamaLengkap`, `Status`, `ActivationCode`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'inactive', NULL),
(4, 'rakha@gmail.com', '$2y$10$bcKqN94PaHb/aDpaZoN6OeY2pyr0N/hyaeXI6rlzAcTE3Gu15qTKG', 'danzz', 'inactive', 'fe2076cbe66fb3570ff96f3690ca053c'),
(5, 'david@gmail.com', '$2y$10$gIVDorYwqQqTLYZ5nGcuwue2XRkRPuK4EzhhevUP6jIFS.T/SQ0f2', 'david', 'active', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `kasir_detailpenjualan`
--

CREATE TABLE `kasir_detailpenjualan` (
  `DetailID` int(11) NOT NULL,
  `PenjualanID` int(11) NOT NULL,
  `ProdukID` int(11) NOT NULL,
  `Jumlah` int(11) NOT NULL,
  `Subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kasir_detailpenjualan`
--

INSERT INTO `kasir_detailpenjualan` (`DetailID`, `PenjualanID`, `ProdukID`, `Jumlah`, `Subtotal`) VALUES
(15, 28, 7, 1, 10000.00),
(16, 29, 6, 10, 200000.00),
(17, 30, 7, 2, 20000.00),
(18, 31, 7, 1, 10000.00),
(19, 32, 7, 1, 10000.00),
(20, 33, 7, 1, 10000.00),
(21, 34, 7, 5, 50000.00),
(22, 35, 7, 1, 10000.00),
(23, 35, 6, 1, 20000.00),
(24, 36, 7, 1, 10000.00),
(25, 37, 8, 1, 20000.00),
(26, 38, 8, 2, 40000.00),
(27, 39, 8, 2, 40000.00),
(28, 40, 6, 9, 180000.00),
(29, 41, 8, 1, 20000.00),
(30, 41, 7, 1, 10000.00),
(31, 42, 10, 1, 5000.00),
(32, 43, 10, 1, 5000.00),
(33, 44, 10, 8, 40000.00);

-- --------------------------------------------------------

--
-- Struktur dari tabel `kasir_pelanggan`
--

CREATE TABLE `kasir_pelanggan` (
  `PelangganID` int(11) NOT NULL,
  `NamaPelanggan` varchar(100) NOT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `Telepon` varchar(15) DEFAULT NULL,
  `Alamat` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kasir_pelanggan`
--

INSERT INTO `kasir_pelanggan` (`PelangganID`, `NamaPelanggan`, `Email`, `Telepon`, `Alamat`) VALUES
(1, 'danish', 'danishrakhap@gmail.com', '00000000099909', 'gjcfgcfytfc'),
(2, 'Pelanggan Umum', NULL, NULL, NULL),
(3, 'Pelanggan Umum', NULL, NULL, NULL),
(4, 'Pelanggan Umum', NULL, NULL, NULL),
(5, 'Pelanggan Umum', NULL, NULL, NULL),
(6, 'Pelanggan Umum', NULL, NULL, NULL),
(7, 'ilham', 'ilham@gmail.com', '00000000099909', 'pelosokkkkkkk');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kasir_pengguna`
--

CREATE TABLE `kasir_pengguna` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `level` enum('admin','kasir') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kasir_pengguna`
--

INSERT INTO `kasir_pengguna` (`id`, `nama`, `username`, `password`, `level`) VALUES
(1, 'Kasir 1', 'kasir1', 'kasir123', 'kasir'),
(2, 'Kasir 2', 'kasir2', 'kasir456', 'kasir'),
(3, 'danu', 'danu', '12345', 'kasir');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kasir_penjualan`
--

CREATE TABLE `kasir_penjualan` (
  `PenjualanID` int(11) NOT NULL,
  `PelangganID` int(11) DEFAULT NULL,
  `TanggalPenjualan` datetime NOT NULL,
  `TotalHarga` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kasir_penjualan`
--

INSERT INTO `kasir_penjualan` (`PenjualanID`, `PelangganID`, `TanggalPenjualan`, `TotalHarga`) VALUES
(1, 1, '2025-12-01 03:35:22', 222.00),
(2, 1, '2025-12-01 03:35:45', 222.00),
(3, 1, '2025-12-01 03:40:34', 1110.00),
(4, 1, '2025-12-01 03:41:19', 50000.00),
(28, 1, '2025-12-01 06:49:37', 10000.00),
(29, 1, '2025-12-01 06:50:14', 200000.00),
(30, 1, '2025-12-01 07:15:38', 20000.00),
(31, 1, '2025-12-01 07:24:10', 10000.00),
(32, 1, '2025-12-01 07:47:56', 10000.00),
(33, 1, '2025-12-03 02:39:22', 10000.00),
(34, 1, '2025-12-03 02:44:25', 50000.00),
(35, 2, '2025-12-03 02:45:56', 30000.00),
(36, 1, '2025-12-03 02:56:30', 10000.00),
(37, 1, '2025-12-03 03:02:18', 20000.00),
(38, 1, '2025-12-03 03:06:54', 40000.00),
(39, 1, '2025-12-03 03:09:59', 40000.00),
(40, 1, '2025-12-03 03:13:10', 180000.00),
(41, 1, '2025-12-03 04:39:13', 30000.00),
(42, 1, '2025-12-03 04:52:24', 5000.00),
(43, 1, '2025-12-03 10:57:23', 5000.00),
(44, 1, '2025-12-03 11:44:30', 40000.00);

-- --------------------------------------------------------

--
-- Struktur dari tabel `kasir_produk`
--

CREATE TABLE `kasir_produk` (
  `ProdukID` int(11) NOT NULL,
  `NamaProduk` varchar(100) NOT NULL,
  `Harga` decimal(10,2) NOT NULL,
  `Stok` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kasir_produk`
--

INSERT INTO `kasir_produk` (`ProdukID`, `NamaProduk`, `Harga`, `Stok`) VALUES
(10, 'aqua', 5000.00, 0),
(11, 'americano', 10000.00, 20),
(12, 'kopi', 70000.00, 100);

-- --------------------------------------------------------

--
-- Struktur dari tabel `kasir_toko`
--

CREATE TABLE `kasir_toko` (
  `TokoID` int(11) NOT NULL,
  `NamaToko` varchar(100) NOT NULL,
  `Alamat` text DEFAULT NULL,
  `Telepon` varchar(20) DEFAULT NULL,
  `Email` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kasir_toko`
--

INSERT INTO `kasir_toko` (`TokoID`, `NamaToko`, `Alamat`, `Telepon`, `Email`) VALUES
(1, 'TOKO KU', 'Jl. Contoh No. 123', '0812-3456-7890', NULL);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `kasir_admin`
--
ALTER TABLE `kasir_admin`
  ADD PRIMARY KEY (`AdminID`),
  ADD UNIQUE KEY `Username` (`Username`);

--
-- Indeks untuk tabel `kasir_detailpenjualan`
--
ALTER TABLE `kasir_detailpenjualan`
  ADD PRIMARY KEY (`DetailID`),
  ADD KEY `PenjualanID` (`PenjualanID`),
  ADD KEY `ProdukID` (`ProdukID`);

--
-- Indeks untuk tabel `kasir_pelanggan`
--
ALTER TABLE `kasir_pelanggan`
  ADD PRIMARY KEY (`PelangganID`);

--
-- Indeks untuk tabel `kasir_pengguna`
--
ALTER TABLE `kasir_pengguna`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `kasir_penjualan`
--
ALTER TABLE `kasir_penjualan`
  ADD PRIMARY KEY (`PenjualanID`),
  ADD KEY `PelangganID` (`PelangganID`);

--
-- Indeks untuk tabel `kasir_produk`
--
ALTER TABLE `kasir_produk`
  ADD PRIMARY KEY (`ProdukID`);

--
-- Indeks untuk tabel `kasir_toko`
--
ALTER TABLE `kasir_toko`
  ADD PRIMARY KEY (`TokoID`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `kasir_admin`
--
ALTER TABLE `kasir_admin`
  MODIFY `AdminID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `kasir_detailpenjualan`
--
ALTER TABLE `kasir_detailpenjualan`
  MODIFY `DetailID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT untuk tabel `kasir_pelanggan`
--
ALTER TABLE `kasir_pelanggan`
  MODIFY `PelangganID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `kasir_pengguna`
--
ALTER TABLE `kasir_pengguna`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `kasir_penjualan`
--
ALTER TABLE `kasir_penjualan`
  MODIFY `PenjualanID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT untuk tabel `kasir_produk`
--
ALTER TABLE `kasir_produk`
  MODIFY `ProdukID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `kasir_toko`
--
ALTER TABLE `kasir_toko`
  MODIFY `TokoID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `kasir_detailpenjualan`
--
ALTER TABLE `kasir_detailpenjualan`
  ADD CONSTRAINT `kasir_detailpenjualan_ibfk_1` FOREIGN KEY (`PenjualanID`) REFERENCES `kasir_penjualan` (`PenjualanID`),
  ADD CONSTRAINT `kasir_detailpenjualan_ibfk_2` FOREIGN KEY (`ProdukID`) REFERENCES `kasir_produk` (`ProdukID`);

--
-- Ketidakleluasaan untuk tabel `kasir_penjualan`
--
ALTER TABLE `kasir_penjualan`
  ADD CONSTRAINT `kasir_penjualan_ibfk_1` FOREIGN KEY (`PelangganID`) REFERENCES `kasir_pelanggan` (`PelangganID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
