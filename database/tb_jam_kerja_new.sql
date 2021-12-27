-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 27 Des 2021 pada 07.54
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
-- Struktur dari tabel `tb_jam_kerja_new`
--

CREATE TABLE `tb_jam_kerja_new` (
  `id` int(100) NOT NULL,
  `nama_jam_kerja` varchar(200) NOT NULL,
  `opd_id` int(100) DEFAULT NULL,
  `nama_opd` varchar(200) DEFAULT NULL,
  `jam_awal_masuk` time DEFAULT NULL,
  `jam_akhir_masuk` time DEFAULT NULL,
  `jam_awal_pulang` time DEFAULT NULL,
  `jam_akhir_pulang` time DEFAULT NULL,
  `jam_awal_istirahat` time DEFAULT NULL,
  `jam_akhir_istirahat` time DEFAULT NULL,
  `jam_awal_selesai_istirahat` time DEFAULT NULL,
  `jam_akhir_selesai_istirahat` time DEFAULT NULL,
  `deleted` int(20) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `tb_jam_kerja_new`
--

INSERT INTO `tb_jam_kerja_new` (`id`, `nama_jam_kerja`, `opd_id`, `nama_opd`, `jam_awal_masuk`, `jam_akhir_masuk`, `jam_awal_pulang`, `jam_akhir_pulang`, `jam_awal_istirahat`, `jam_akhir_istirahat`, `jam_awal_selesai_istirahat`, `jam_akhir_selesai_istirahat`, `deleted`, `created_at`) VALUES
(1, 'PERCOBAAN (DAY 1)', 1, 'Dinas Pendidikan', '06:00:00', '08:00:00', '12:00:00', '01:01:00', NULL, NULL, NULL, NULL, NULL, '2021-12-27 11:55:52');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `tb_jam_kerja_new`
--
ALTER TABLE `tb_jam_kerja_new`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `tb_jam_kerja_new`
--
ALTER TABLE `tb_jam_kerja_new`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
