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
-- Struktur dari tabel `tb_absensi`
--

CREATE TABLE `tb_absensi` (
  `id` int(100) NOT NULL,
  `pegawai_id` int(100) NOT NULL,
  `opd_id` int(100) DEFAULT NULL,
  `jam` datetime DEFAULT NULL,
  `jenis_absen` varchar(100) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `file_absensi` text DEFAULT NULL,
  `status` int(5) DEFAULT NULL,
  `is_susulan` enum('Ya') DEFAULT NULL,
  `access_key` text DEFAULT NULL,
  `approved_by` int(100) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `tb_absensi`
--

INSERT INTO `tb_absensi` (`id`, `pegawai_id`, `opd_id`, `jam`, `jenis_absen`, `keterangan`, `file_absensi`, `status`, `is_susulan`, `access_key`, `approved_by`, `approved_at`, `created_at`) VALUES
(1, 3, 1, '2021-12-27 07:27:57', 'Absen Masuk', NULL, NULL, 1, NULL, NULL, NULL, '2021-12-27 06:27:57', '2021-12-27 13:28:27'),
(2, 3, 1, '2021-12-27 12:27:57', 'Absen Istirahat', NULL, NULL, 1, NULL, NULL, NULL, '2021-12-27 06:27:57', '2021-12-27 13:28:27'),
(3, 3, 1, '2021-12-27 13:47:57', 'Absen Selesai Istirahat', NULL, NULL, 1, NULL, NULL, NULL, '2021-12-27 06:27:57', '2021-12-27 13:28:27'),
(4, 3, 1, '2021-12-27 14:47:57', 'Absen Pulang', NULL, NULL, 1, NULL, NULL, NULL, '2021-12-27 06:27:57', '2021-12-27 13:28:27');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `tb_absensi`
--
ALTER TABLE `tb_absensi`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `tb_absensi`
--
ALTER TABLE `tb_absensi`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
