-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 27 Des 2021 pada 07.53
-- Versi server: 10.4.22-MariaDB
-- Versi PHP: 7.4.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `layanan`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_izin_kerja`
--

CREATE TABLE `tb_izin_kerja` (
  `id` int(100) NOT NULL,
  `pegawai_id` int(100) NOT NULL,
  `opd_id` int(100) NOT NULL,
  `tanggal_awal` date NOT NULL,
  `tanggal_akhir` date NOT NULL,
  `jenis_izin` varchar(200) NOT NULL,
  `file_izin` text NOT NULL,
  `status` int(100) DEFAULT NULL,
  `access_key` text DEFAULT NULL,
  `aproved_by` int(100) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `tb_izin_kerja`
--

INSERT INTO `tb_izin_kerja` (`id`, `pegawai_id`, `opd_id`, `tanggal_awal`, `tanggal_akhir`, `jenis_izin`, `file_izin`, `status`, `access_key`, `aproved_by`, `created_at`) VALUES
(1, 1, 0, '2021-12-14', '2021-12-14', 'Cuti', './izin_kerja/2021/December/admin/1640580566.jpg', NULL, '94644-2f6aca4', NULL, '2021-12-27 11:49:26');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `tb_izin_kerja`
--
ALTER TABLE `tb_izin_kerja`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `tb_izin_kerja`
--
ALTER TABLE `tb_izin_kerja`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
