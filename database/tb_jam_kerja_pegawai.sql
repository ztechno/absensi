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
-- Struktur dari tabel `tb_jam_kerja_pegawai`
--

CREATE TABLE `tb_jam_kerja_pegawai` (
  `id` int(100) NOT NULL,
  `pegawai_id` int(100) NOT NULL,
  `jenis_pegawai` varchar(100) NOT NULL,
  `tanggal` date NOT NULL,
  `jam_kerja_id` int(100) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `tb_jam_kerja_pegawai`
--

INSERT INTO `tb_jam_kerja_pegawai` (`id`, `pegawai_id`, `jenis_pegawai`, `tanggal`, `jam_kerja_id`, `created_at`) VALUES
(9, 3, '', '2021-12-01', 1, '2021-12-27 12:11:11'),
(10, 3, '', '2021-12-02', NULL, '2021-12-27 12:11:11'),
(11, 3, '', '2021-12-03', NULL, '2021-12-27 12:11:12');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `tb_jam_kerja_pegawai`
--
ALTER TABLE `tb_jam_kerja_pegawai`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `tb_jam_kerja_pegawai`
--
ALTER TABLE `tb_jam_kerja_pegawai`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
