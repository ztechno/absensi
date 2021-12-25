-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 25, 2021 at 04:56 AM
-- Server version: 10.4.20-MariaDB
-- PHP Version: 7.4.22

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
-- Table structure for table `ci_sessions`
--

CREATE TABLE `ci_sessions` (
  `id` varchar(128) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `data` blob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ci_sessions`
--

INSERT INTO `ci_sessions` (`id`, `ip_address`, `timestamp`, `data`) VALUES
('ef199fc02c64a0057f5d953c2932e59e1fbbe604', '::1', 1640333210, 0x5f5f63695f6c6173745f726567656e65726174657c693a313634303333333231303b69647c733a313a2231223b6e616d617c733a353a2261646d696e223b757365726e616d657c733a353a2261646d696e223b70617373776f72647c733a36303a22243279243130247a79595542626172416b66484564514a6d6f6e585475334d4e6c6468534a656b6d792e59305562447247415039793858366d5a4853223b69735f6163746976657c733a323a225961223b637265617465645f61747c733a31393a22323032312d31322d32342031323a30303a3434223b726f6c65737c613a313a7b693a303b733a353a2241646d696e223b7d),
('3fd35f888f561d2e5243d30f353b05ee8f47f1d7', '::1', 1640333533, 0x5f5f63695f6c6173745f726567656e65726174657c693a313634303333333533333b69647c733a313a2231223b6e616d617c733a353a2261646d696e223b757365726e616d657c733a353a2261646d696e223b70617373776f72647c733a36303a22243279243130247a79595542626172416b66484564514a6d6f6e585475334d4e6c6468534a656b6d792e59305562447247415039793858366d5a4853223b69735f6163746976657c733a323a225961223b637265617465645f61747c733a31393a22323032312d31322d32342031323a30303a3434223b726f6c65737c613a313a7b693a303b733a353a2241646d696e223b7d),
('e4a95c05e5770006882a2ff66299513defe753ba', '::1', 1640341756, 0x5f5f63695f6c6173745f726567656e65726174657c693a313634303334313735363b69647c733a313a2231223b6e616d617c733a353a2261646d696e223b757365726e616d657c733a353a2261646d696e223b70617373776f72647c733a36303a22243279243130247a79595542626172416b66484564514a6d6f6e585475334d4e6c6468534a656b6d792e59305562447247415039793858366d5a4853223b69735f6163746976657c733a323a225961223b637265617465645f61747c733a31393a22323032312d31322d32342031323a30303a3434223b726f6c65737c613a313a7b693a303b733a353a2241646d696e223b7d),
('559e3a571b1718a0940eb3a206cc92704e3fddab', '::1', 1640342067, 0x5f5f63695f6c6173745f726567656e65726174657c693a313634303334323036373b69647c733a313a2231223b6e616d617c733a353a2261646d696e223b757365726e616d657c733a353a2261646d696e223b70617373776f72647c733a36303a22243279243130247a79595542626172416b66484564514a6d6f6e585475334d4e6c6468534a656b6d792e59305562447247415039793858366d5a4853223b69735f6163746976657c733a323a225961223b637265617465645f61747c733a31393a22323032312d31322d32342031323a30303a3434223b726f6c65737c613a313a7b693a303b733a353a2241646d696e223b7d),
('05426deb5876ca55eb1cac5b6c0786b3213a2f3e', '::1', 1640342401, 0x5f5f63695f6c6173745f726567656e65726174657c693a313634303334323430313b69647c733a313a2231223b6e616d617c733a353a2261646d696e223b757365726e616d657c733a353a2261646d696e223b70617373776f72647c733a36303a22243279243130247a79595542626172416b66484564514a6d6f6e585475334d4e6c6468534a656b6d792e59305562447247415039793858366d5a4853223b69735f6163746976657c733a323a225961223b637265617465645f61747c733a31393a22323032312d31322d32342031323a30303a3434223b726f6c65737c613a313a7b693a303b733a353a2241646d696e223b7d),
('e3e0623026939e9025b85639e8a9128f7f36f3b1', '::1', 1640342730, 0x5f5f63695f6c6173745f726567656e65726174657c693a313634303334323733303b69647c733a313a2231223b6e616d617c733a353a2261646d696e223b757365726e616d657c733a353a2261646d696e223b70617373776f72647c733a36303a22243279243130247a79595542626172416b66484564514a6d6f6e585475334d4e6c6468534a656b6d792e59305562447247415039793858366d5a4853223b69735f6163746976657c733a323a225961223b637265617465645f61747c733a31393a22323032312d31322d32342031323a30303a3434223b726f6c65737c613a313a7b693a303b733a353a2241646d696e223b7d),
('b4a574b83151cc7c163de04d203275c9c83fef4e', '::1', 1640343069, 0x5f5f63695f6c6173745f726567656e65726174657c693a313634303334333036393b69647c733a313a2231223b6e616d617c733a353a2261646d696e223b757365726e616d657c733a353a2261646d696e223b70617373776f72647c733a36303a22243279243130247a79595542626172416b66484564514a6d6f6e585475334d4e6c6468534a656b6d792e59305562447247415039793858366d5a4853223b69735f6163746976657c733a323a225961223b637265617465645f61747c733a31393a22323032312d31322d32342031323a30303a3434223b726f6c65737c613a313a7b693a303b733a353a2241646d696e223b7d),
('71d3a8f476b41b826b4f94fc2030b28125991cad', '::1', 1640343680, 0x5f5f63695f6c6173745f726567656e65726174657c693a313634303334333638303b69647c733a313a2231223b6e616d617c733a353a2261646d696e223b757365726e616d657c733a353a2261646d696e223b70617373776f72647c733a36303a22243279243130247a79595542626172416b66484564514a6d6f6e585475334d4e6c6468534a656b6d792e59305562447247415039793858366d5a4853223b69735f6163746976657c733a323a225961223b637265617465645f61747c733a31393a22323032312d31322d32342031323a30303a3434223b726f6c65737c613a313a7b693a303b733a353a2241646d696e223b7d),
('9bdb943b13cb76f89c4c16d132efbb72fad416c9', '::1', 1640346516, 0x5f5f63695f6c6173745f726567656e65726174657c693a313634303334363531363b69647c733a313a2231223b6e616d617c733a353a2261646d696e223b757365726e616d657c733a353a2261646d696e223b70617373776f72647c733a36303a22243279243130247a79595542626172416b66484564514a6d6f6e585475334d4e6c6468534a656b6d792e59305562447247415039793858366d5a4853223b69735f6163746976657c733a323a225961223b637265617465645f61747c733a31393a22323032312d31322d32342031323a30303a3434223b726f6c65737c613a313a7b693a303b733a353a2241646d696e223b7d),
('25e2539d14fa123d619dda4e2a75f048933087ef', '::1', 1640349897, 0x5f5f63695f6c6173745f726567656e65726174657c693a313634303334393839373b69647c733a313a2231223b6e616d617c733a353a2261646d696e223b757365726e616d657c733a353a2261646d696e223b70617373776f72647c733a36303a22243279243130247a79595542626172416b66484564514a6d6f6e585475334d4e6c6468534a656b6d792e59305562447247415039793858366d5a4853223b69735f6163746976657c733a323a225961223b637265617465645f61747c733a31393a22323032312d31322d32342031323a30303a3434223b726f6c65737c613a313a7b693a303b733a353a2241646d696e223b7d),
('d9d95a021eb271fec9bdb686aaf069c6291f8e3f', '::1', 1640350269, 0x5f5f63695f6c6173745f726567656e65726174657c693a313634303335303236393b69647c733a313a2231223b6e616d617c733a353a2261646d696e223b757365726e616d657c733a353a2261646d696e223b70617373776f72647c733a36303a22243279243130247a79595542626172416b66484564514a6d6f6e585475334d4e6c6468534a656b6d792e59305562447247415039793858366d5a4853223b69735f6163746976657c733a323a225961223b637265617465645f61747c733a31393a22323032312d31322d32342031323a30303a3434223b726f6c65737c613a313a7b693a303b733a353a2241646d696e223b7d),
('4d312f3c13f25a69a781d2a1801c7f5fc90ec936', '::1', 1640350629, 0x5f5f63695f6c6173745f726567656e65726174657c693a313634303335303632393b69647c733a313a2231223b6e616d617c733a353a2261646d696e223b757365726e616d657c733a353a2261646d696e223b70617373776f72647c733a36303a22243279243130247a79595542626172416b66484564514a6d6f6e585475334d4e6c6468534a656b6d792e59305562447247415039793858366d5a4853223b69735f6163746976657c733a323a225961223b637265617465645f61747c733a31393a22323032312d31322d32342031323a30303a3434223b726f6c65737c613a313a7b693a303b733a353a2241646d696e223b7d),
('912f5a60b5a3d5604d4ef421d1d073e38e4ec0ab', '::1', 1640351402, 0x5f5f63695f6c6173745f726567656e65726174657c693a313634303335313430323b69647c733a313a2231223b6e616d617c733a353a2261646d696e223b757365726e616d657c733a353a2261646d696e223b70617373776f72647c733a36303a22243279243130247a79595542626172416b66484564514a6d6f6e585475334d4e6c6468534a656b6d792e59305562447247415039793858366d5a4853223b69735f6163746976657c733a323a225961223b637265617465645f61747c733a31393a22323032312d31322d32342031323a30303a3434223b726f6c65737c613a313a7b693a303b733a353a2241646d696e223b7d),
('48dd00797f9648aca319b63a1138f150fee4d067', '::1', 1640351798, 0x5f5f63695f6c6173745f726567656e65726174657c693a313634303335313739383b69647c733a313a2231223b6e616d617c733a353a2261646d696e223b757365726e616d657c733a353a2261646d696e223b70617373776f72647c733a36303a22243279243130247a79595542626172416b66484564514a6d6f6e585475334d4e6c6468534a656b6d792e59305562447247415039793858366d5a4853223b69735f6163746976657c733a323a225961223b637265617465645f61747c733a31393a22323032312d31322d32342031323a30303a3434223b726f6c65737c613a313a7b693a303b733a353a2241646d696e223b7d),
('e936698b4f6865438a89b10116f92fde7d375e0b', '::1', 1640357337, 0x5f5f63695f6c6173745f726567656e65726174657c693a313634303335373333373b69647c733a313a2231223b6e616d617c733a353a2261646d696e223b757365726e616d657c733a353a2261646d696e223b70617373776f72647c733a36303a22243279243130247a79595542626172416b66484564514a6d6f6e585475334d4e6c6468534a656b6d792e59305562447247415039793858366d5a4853223b69735f6163746976657c733a323a225961223b637265617465645f61747c733a31393a22323032312d31322d32342031323a30303a3434223b726f6c65737c613a313a7b693a303b733a353a2241646d696e223b7d),
('c7ff30789483a4bffe3ebeb3622bb375be7f9aa5', '::1', 1640359483, 0x5f5f63695f6c6173745f726567656e65726174657c693a313634303335393438333b69647c733a313a2231223b6e616d617c733a353a2261646d696e223b757365726e616d657c733a353a2261646d696e223b70617373776f72647c733a36303a22243279243130247a79595542626172416b66484564514a6d6f6e585475334d4e6c6468534a656b6d792e59305562447247415039793858366d5a4853223b69735f6163746976657c733a323a225961223b637265617465645f61747c733a31393a22323032312d31322d32342031323a30303a3434223b726f6c65737c613a313a7b693a303b733a353a2241646d696e223b7d),
('70a71422d69a9be2f2c7534d2efec89c07bba97d', '::1', 1640359996, 0x5f5f63695f6c6173745f726567656e65726174657c693a313634303335393939363b69647c733a313a2231223b6e616d617c733a353a2261646d696e223b757365726e616d657c733a353a2261646d696e223b70617373776f72647c733a36303a22243279243130247a79595542626172416b66484564514a6d6f6e585475334d4e6c6468534a656b6d792e59305562447247415039793858366d5a4853223b69735f6163746976657c733a323a225961223b637265617465645f61747c733a31393a22323032312d31322d32342031323a30303a3434223b726f6c65737c613a313a7b693a303b733a353a2241646d696e223b7d),
('312c52cd22c62b04a31b96999577126e29598d97', '::1', 1640397966, 0x5f5f63695f6c6173745f726567656e65726174657c693a313634303339373936363b69647c733a313a2231223b6e616d617c733a353a2261646d696e223b757365726e616d657c733a353a2261646d696e223b70617373776f72647c733a36303a22243279243130247a79595542626172416b66484564514a6d6f6e585475334d4e6c6468534a656b6d792e59305562447247415039793858366d5a4853223b69735f6163746976657c733a323a225961223b637265617465645f61747c733a31393a22323032312d31322d32342031323a30303a3434223b726f6c65737c613a313a7b693a303b733a353a2241646d696e223b7d),
('605648d0e20dc0fb2aaa243515a22284d093db2d', '::1', 1640400918, 0x5f5f63695f6c6173745f726567656e65726174657c693a313634303430303931383b69647c733a313a2231223b6e616d617c733a353a2261646d696e223b757365726e616d657c733a353a2261646d696e223b70617373776f72647c733a36303a22243279243130247a79595542626172416b66484564514a6d6f6e585475334d4e6c6468534a656b6d792e59305562447247415039793858366d5a4853223b69735f6163746976657c733a323a225961223b637265617465645f61747c733a31393a22323032312d31322d32342031323a30303a3434223b726f6c65737c613a313a7b693a303b733a353a2241646d696e223b7d),
('99ed6f979e3d2c3b390a4eb248cdac4f8334062c', '::1', 1640401236, 0x5f5f63695f6c6173745f726567656e65726174657c693a313634303430313233363b69647c733a313a2231223b6e616d617c733a353a2261646d696e223b757365726e616d657c733a353a2261646d696e223b70617373776f72647c733a36303a22243279243130247a79595542626172416b66484564514a6d6f6e585475334d4e6c6468534a656b6d792e59305562447247415039793858366d5a4853223b69735f6163746976657c733a323a225961223b637265617465645f61747c733a31393a22323032312d31322d32342031323a30303a3434223b726f6c65737c613a313a7b693a303b733a353a2241646d696e223b7d),
('ca8e79b45205f5861756b2fcab5451d37616b6f1', '::1', 1640401904, 0x5f5f63695f6c6173745f726567656e65726174657c693a313634303430313930343b69647c733a313a2231223b6e616d617c733a353a2261646d696e223b757365726e616d657c733a353a2261646d696e223b70617373776f72647c733a36303a22243279243130247a79595542626172416b66484564514a6d6f6e585475334d4e6c6468534a656b6d792e59305562447247415039793858366d5a4853223b69735f6163746976657c733a323a225961223b637265617465645f61747c733a31393a22323032312d31322d32342031323a30303a3434223b726f6c65737c613a313a7b693a303b733a353a2241646d696e223b7d),
('6ba83547624f6a3ec2711f4eff40a71d867f3b20', '::1', 1640402229, 0x5f5f63695f6c6173745f726567656e65726174657c693a313634303430323232393b69647c733a313a2231223b6e616d617c733a353a2261646d696e223b757365726e616d657c733a353a2261646d696e223b70617373776f72647c733a36303a22243279243130247a79595542626172416b66484564514a6d6f6e585475334d4e6c6468534a656b6d792e59305562447247415039793858366d5a4853223b69735f6163746976657c733a323a225961223b637265617465645f61747c733a31393a22323032312d31322d32342031323a30303a3434223b726f6c65737c613a313a7b693a303b733a353a2241646d696e223b7d),
('a10448b6a69280f8213817f2cc5c16b24241cc97', '::1', 1640402700, 0x5f5f63695f6c6173745f726567656e65726174657c693a313634303430323730303b69647c733a313a2231223b6e616d617c733a353a2261646d696e223b757365726e616d657c733a353a2261646d696e223b70617373776f72647c733a36303a22243279243130247a79595542626172416b66484564514a6d6f6e585475334d4e6c6468534a656b6d792e59305562447247415039793858366d5a4853223b69735f6163746976657c733a323a225961223b637265617465645f61747c733a31393a22323032312d31322d32342031323a30303a3434223b726f6c65737c613a313a7b693a303b733a353a2241646d696e223b7d),
('980db93f1ad8a90cb8b9462d8c7d11d35639d8ca', '::1', 1640403096, 0x5f5f63695f6c6173745f726567656e65726174657c693a313634303430333039363b69647c733a313a2231223b6e616d617c733a353a2261646d696e223b757365726e616d657c733a353a2261646d696e223b70617373776f72647c733a36303a22243279243130247a79595542626172416b66484564514a6d6f6e585475334d4e6c6468534a656b6d792e59305562447247415039793858366d5a4853223b69735f6163746976657c733a323a225961223b637265617465645f61747c733a31393a22323032312d31322d32342031323a30303a3434223b726f6c65737c613a313a7b693a303b733a353a2241646d696e223b7d),
('2fa61b412597343499c33aa9d324158e26ddfed1', '::1', 1640403426, 0x5f5f63695f6c6173745f726567656e65726174657c693a313634303430333432363b69647c733a313a2231223b6e616d617c733a353a2261646d696e223b757365726e616d657c733a353a2261646d696e223b70617373776f72647c733a36303a22243279243130247a79595542626172416b66484564514a6d6f6e585475334d4e6c6468534a656b6d792e59305562447247415039793858366d5a4853223b69735f6163746976657c733a323a225961223b637265617465645f61747c733a31393a22323032312d31322d32342031323a30303a3434223b726f6c65737c613a313a7b693a303b733a353a2241646d696e223b7d),
('b810674522a173e33c4a4d6a9be37df08596485e', '::1', 1640403836, 0x5f5f63695f6c6173745f726567656e65726174657c693a313634303430333833363b69647c733a313a2231223b6e616d617c733a353a2261646d696e223b757365726e616d657c733a353a2261646d696e223b70617373776f72647c733a36303a22243279243130247a79595542626172416b66484564514a6d6f6e585475334d4e6c6468534a656b6d792e59305562447247415039793858366d5a4853223b69735f6163746976657c733a323a225961223b637265617465645f61747c733a31393a22323032312d31322d32342031323a30303a3434223b726f6c65737c613a313a7b693a303b733a353a2241646d696e223b7d),
('353ff4ef85591c2d0a06c88eb8188a57699f849e', '::1', 1640404049, 0x5f5f63695f6c6173745f726567656e65726174657c693a313634303430333833363b69647c733a313a2231223b6e616d617c733a353a2261646d696e223b757365726e616d657c733a353a2261646d696e223b70617373776f72647c733a36303a22243279243130247a79595542626172416b66484564514a6d6f6e585475334d4e6c6468534a656b6d792e59305562447247415039793858366d5a4853223b69735f6163746976657c733a323a225961223b637265617465645f61747c733a31393a22323032312d31322d32342031323a30303a3434223b726f6c65737c613a313a7b693a303b733a353a2241646d696e223b7d);

-- --------------------------------------------------------

--
-- Table structure for table `tb_absensi`
--

CREATE TABLE `tb_absensi` (
  `id` int(100) NOT NULL,
  `pegawai_id` int(100) NOT NULL,
  `jenis_pegawai` varchar(20) NOT NULL,
  `nama_pegawai` varchar(300) DEFAULT NULL,
  `skpd_id` int(100) NOT NULL,
  `nama_opd` varchar(300) DEFAULT NULL,
  `jam` datetime DEFAULT NULL,
  `jenis_absen` varchar(100) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `file_absensi` text DEFAULT NULL,
  `status` int(5) DEFAULT NULL,
  `is_susulan` enum('Ya') DEFAULT NULL,
  `access_key` text DEFAULT NULL,
  `approved_by` int(100) DEFAULT NULL,
  `approved_by_nama` varchar(300) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tb_absen_manual`
--

CREATE TABLE `tb_absen_manual` (
  `id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `pegawai_id` int(11) NOT NULL,
  `jenis_pegawai` varchar(50) NOT NULL,
  `skpd_id` int(100) NOT NULL,
  `jenis_absen` varchar(20) NOT NULL,
  `lampiran_amp` text DEFAULT NULL,
  `lampiran_ams` text DEFAULT NULL,
  `status` int(100) DEFAULT NULL,
  `aproved_by` int(100) DEFAULT NULL,
  `aproved_at` int(100) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_by` int(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tb_absen_wajah`
--

CREATE TABLE `tb_absen_wajah` (
  `id` int(100) NOT NULL,
  `pegawai_id` int(100) NOT NULL,
  `jenis_pegawai` varchar(50) NOT NULL,
  `skpd_id` int(100) NOT NULL,
  `tanggal` date DEFAULT NULL,
  `jam_masuk` varchar(40) DEFAULT NULL,
  `jam_istirahat` varchar(40) DEFAULT NULL,
  `jam_pulang` varchar(40) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updatet_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by_ip` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tb_izin_kerja`
--

CREATE TABLE `tb_izin_kerja` (
  `id` int(100) NOT NULL,
  `meta_id` int(100) NOT NULL,
  `pegawai_id` int(100) NOT NULL,
  `jenis_pegawai` varchar(40) NOT NULL,
  `nama_pegawai` varchar(300) DEFAULT NULL,
  `skpd_id` int(100) NOT NULL,
  `nama_skpd` varchar(200) DEFAULT NULL,
  `nama_opd` varchar(300) DEFAULT NULL,
  `status` int(100) DEFAULT NULL,
  `access_key` text DEFAULT NULL,
  `aproved_by` int(100) DEFAULT NULL,
  `aproved_by_nama` varchar(300) DEFAULT NULL,
  `aproved_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tb_izin_kerja_meta`
--

CREATE TABLE `tb_izin_kerja_meta` (
  `id` int(11) NOT NULL,
  `tanggal_awal` date NOT NULL,
  `tanggal_akhir` date NOT NULL,
  `jenis_izin` varchar(20) NOT NULL,
  `file_izin` text DEFAULT NULL,
  `spt_id` int(100) DEFAULT NULL,
  `user_id` int(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tb_jabatan_golongan`
--

CREATE TABLE `tb_jabatan_golongan` (
  `id` int(11) NOT NULL,
  `nama_golongan` varchar(200) NOT NULL,
  `pph` varchar(5) NOT NULL,
  `deleted` enum('Ya') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tb_jam_kerja`
--

CREATE TABLE `tb_jam_kerja` (
  `id` int(11) NOT NULL,
  `is_default` int(11) DEFAULT NULL,
  `nama_jam_kerja` varchar(300) NOT NULL,
  `deleted` int(1) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tb_jam_kerja`
--

INSERT INTO `tb_jam_kerja` (`id`, `is_default`, `nama_jam_kerja`, `deleted`, `created_at`) VALUES
(0, NULL, 'Normal', NULL, '2021-12-25 10:37:06');

-- --------------------------------------------------------

--
-- Table structure for table `tb_jam_kerja_meta`
--

CREATE TABLE `tb_jam_kerja_meta` (
  `id` int(11) NOT NULL,
  `jam_kerja_id` int(100) NOT NULL,
  `hari` int(20) NOT NULL,
  `jam_awal_masuk` time DEFAULT NULL,
  `jam_akhir_masuk` time DEFAULT NULL,
  `jam_awal_pulang` time DEFAULT NULL,
  `jam_akhir_pulang` time DEFAULT NULL,
  `jam_awal_istirahat` time DEFAULT NULL,
  `jam_akhir_istirahat` time DEFAULT NULL,
  `jam_awal_selesai_istirahat` time DEFAULT NULL,
  `jam_akhir_selesai_istirahat` time DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tb_jam_kerja_meta`
--

INSERT INTO `tb_jam_kerja_meta` (`id`, `jam_kerja_id`, `hari`, `jam_awal_masuk`, `jam_akhir_masuk`, `jam_awal_pulang`, `jam_akhir_pulang`, `jam_awal_istirahat`, `jam_akhir_istirahat`, `jam_awal_selesai_istirahat`, `jam_akhir_selesai_istirahat`, `created_at`) VALUES
(0, 0, 0, '07:15:00', '07:30:00', '16:30:00', '18:00:00', '12:00:00', '12:30:00', '13:30:00', '14:00:00', '2021-12-25 03:37:06');

-- --------------------------------------------------------

--
-- Table structure for table `tb_jam_kerja_new`
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

-- --------------------------------------------------------

--
-- Table structure for table `tb_jam_kerja_pegawai`
--

CREATE TABLE `tb_jam_kerja_pegawai` (
  `id` int(100) NOT NULL,
  `pegawai_id` int(100) NOT NULL,
  `jenis_pegawai` varchar(100) NOT NULL,
  `tanggal` date NOT NULL,
  `jam_kerja_id` int(100) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tb_jam_kerja_pegawai_new`
--

CREATE TABLE `tb_jam_kerja_pegawai_new` (
  `id` int(100) NOT NULL,
  `pegawai_id` int(100) NOT NULL,
  `jenis_pegawai` varchar(80) NOT NULL,
  `jam_kerja_id` int(100) DEFAULT NULL,
  `tanggal` date NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tb_kordinat`
--

CREATE TABLE `tb_kordinat` (
  `id` int(100) NOT NULL,
  `skpd_id` int(100) NOT NULL,
  `latitude` varchar(300) NOT NULL,
  `longitude` varchar(300) NOT NULL,
  `radius` varchar(300) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tb_kordinat`
--

INSERT INTO `tb_kordinat` (`id`, `skpd_id`, `latitude`, `longitude`, `radius`, `updated_at`) VALUES
(1, 2, '1', '1', '100', '2021-12-24 09:13:51');

-- --------------------------------------------------------

--
-- Table structure for table `tb_kordinat_tambahan`
--

CREATE TABLE `tb_kordinat_tambahan` (
  `id` int(100) NOT NULL,
  `skpd_id` int(100) NOT NULL,
  `nama_skpd` varchar(200) NOT NULL,
  `nama_kordinat` varchar(200) NOT NULL,
  `latitude` varchar(200) NOT NULL,
  `longitude` varchar(200) NOT NULL,
  `radius` varchar(200) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tb_kordinat_tambahan`
--

INSERT INTO `tb_kordinat_tambahan` (`id`, `skpd_id`, `nama_skpd`, `nama_kordinat`, `latitude`, `longitude`, `radius`, `created_at`) VALUES
(1, 2, 'Dinas Kesehatan', 'Check Point', '2', '99', '100', '2021-12-24 11:40:15');

-- --------------------------------------------------------

--
-- Table structure for table `tb_menu`
--

CREATE TABLE `tb_menu` (
  `id` int(11) NOT NULL,
  `website_id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `urutan` int(11) DEFAULT NULL,
  `nama_menu` varchar(300) NOT NULL,
  `url` varchar(200) NOT NULL,
  `icon` varchar(200) DEFAULT NULL,
  `is_actived` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tb_opd`
--

CREATE TABLE `tb_opd` (
  `id` int(11) NOT NULL,
  `nama_opd` text NOT NULL,
  `singkatan` varchar(300) DEFAULT NULL,
  `alamat` text NOT NULL,
  `radius` text DEFAULT NULL,
  `longitude` text DEFAULT NULL,
  `latitude` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tb_opd`
--

INSERT INTO `tb_opd` (`id`, `nama_opd`, `singkatan`, `alamat`, `radius`, `longitude`, `latitude`) VALUES
(1, 'Dinas Pendidikan', 'Disdik', '', NULL, NULL, NULL),
(2, 'Dinas Kesehatan', 'Dinkes', '', NULL, NULL, NULL),
(3, 'Puskesmas 1', 'Puskesmas 1', '', NULL, NULL, NULL),
(4, 'Puskesmas 2', 'Puskesmas 2', '', NULL, NULL, NULL),
(5, 'SDN 1', 'SDN 1', '', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tb_pegawai`
--

CREATE TABLE `tb_pegawai` (
  `id` bigint(20) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `nip` varchar(20) NOT NULL,
  `opd_id` int(11) NOT NULL,
  `jabatan` longtext DEFAULT NULL,
  `kepala` smallint(6) DEFAULT 0,
  `cpns` smallint(6) DEFAULT 0,
  `plt` int(11) DEFAULT 0,
  `kategori_pegawai` varchar(10) DEFAULT 'pegawai',
  `bendahara` smallint(6) DEFAULT 0,
  `foto` varchar(100) NOT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tb_pegawai_atasan`
--

CREATE TABLE `tb_pegawai_atasan` (
  `id` int(100) NOT NULL,
  `pegawai_id` int(100) NOT NULL,
  `jenis_pegawai` varchar(200) NOT NULL,
  `nama_pegawai` varchar(300) NOT NULL,
  `skpd_id` int(100) NOT NULL,
  `pegawai_atasan_id` int(100) NOT NULL,
  `jenis_pegawai_atasan` varchar(200) NOT NULL,
  `nama_pegawai_atasan` varchar(300) NOT NULL,
  `skpd_atasan_id` int(100) NOT NULL,
  `set_by_pegawai_id` int(100) NOT NULL,
  `set_by_jenis_pegawai` varchar(100) NOT NULL,
  `set_by_nama_pegawai` varchar(200) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tb_pegawai_meta`
--

CREATE TABLE `tb_pegawai_meta` (
  `id` int(11) NOT NULL,
  `nip` text DEFAULT NULL,
  `pegawai_id` text NOT NULL,
  `jenis_pegawai` text NOT NULL,
  `no_hp` text DEFAULT NULL,
  `password` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tb_rekap_absen`
--

CREATE TABLE `tb_rekap_absen` (
  `id` int(100) NOT NULL,
  `opd_id` int(100) NOT NULL,
  `skpd_id` int(100) DEFAULT NULL,
  `pegawai_id` int(100) NOT NULL,
  `jenis_pegawai` enum('pegawai','tks') NOT NULL,
  `nama_opd` varchar(300) DEFAULT NULL,
  `nama_skpd` varchar(300) DEFAULT NULL,
  `nama_pegawai` varchar(300) DEFAULT NULL,
  `bulan` varchar(20) NOT NULL,
  `JHK` int(11) NOT NULL,
  `AI` int(11) NOT NULL,
  `ASI` int(11) NOT NULL,
  `AMP` int(11) NOT NULL,
  `AMS` int(11) NOT NULL,
  `AMI` int(11) NOT NULL,
  `AMSI` int(11) NOT NULL,
  `AU` int(11) NOT NULL,
  `TDHE1` int(11) NOT NULL,
  `TDHE2` int(11) NOT NULL,
  `TM1` int(11) NOT NULL,
  `TM2` int(11) NOT NULL,
  `TM3` int(11) NOT NULL,
  `TM4` int(11) NOT NULL,
  `TM5` int(11) NOT NULL,
  `ILA1` int(11) NOT NULL,
  `ILA2` int(11) NOT NULL,
  `ILA3` int(11) NOT NULL,
  `ILA4` int(11) NOT NULL,
  `ILA5` int(11) NOT NULL,
  `TMSI1` int(11) NOT NULL,
  `TMSI2` int(11) NOT NULL,
  `TMSI3` int(11) NOT NULL,
  `TMSI4` int(11) NOT NULL,
  `TMSI5` int(11) NOT NULL,
  `PLA1` int(11) NOT NULL,
  `PLA2` int(11) NOT NULL,
  `PLA3` int(11) NOT NULL,
  `PLA4` int(11) NOT NULL,
  `PLA5` int(11) NOT NULL,
  `TAU` int(11) NOT NULL,
  `TMK` int(11) NOT NULL,
  `H` int(11) NOT NULL,
  `I` int(11) NOT NULL,
  `S` int(11) NOT NULL,
  `DL` int(11) NOT NULL,
  `status` enum('disetujui','ditolak') DEFAULT NULL,
  `approved_by` int(100) DEFAULT NULL,
  `approved_by_nama` varchar(300) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `created_by` int(100) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tb_roles`
--

CREATE TABLE `tb_roles` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tb_roles`
--

INSERT INTO `tb_roles` (`id`, `nama`) VALUES
(1, 'Admin'),
(2, 'Operator OPD'),
(3, 'Pegawai');

-- --------------------------------------------------------

--
-- Table structure for table `tb_unit_kerja`
--

CREATE TABLE `tb_unit_kerja` (
  `id` int(100) NOT NULL,
  `opd_id` int(100) NOT NULL,
  `nama_opd` varchar(300) NOT NULL,
  `skpd_id` int(100) NOT NULL,
  `nama_skpd` varchar(300) NOT NULL,
  `is_opd` enum('Ya') DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tb_unit_kerja`
--

INSERT INTO `tb_unit_kerja` (`id`, `opd_id`, `nama_opd`, `skpd_id`, `nama_skpd`, `is_opd`, `created_at`) VALUES
(1, 2, 'Dinas Kesehatan', 3, 'Puskesmas 1', NULL, '2021-12-24 14:44:52'),
(2, 2, 'Dinas Kesehatan', 4, 'Puskesmas 2', NULL, '2021-12-24 14:44:52'),
(3, 1, 'Dinas Pendidikan', 5, 'SDN 1', NULL, '2021-12-24 14:46:31');

-- --------------------------------------------------------

--
-- Table structure for table `tb_upacara_libur`
--

CREATE TABLE `tb_upacara_libur` (
  `id` int(11) NOT NULL,
  `nama_hari` varchar(200) NOT NULL,
  `tanggal` date NOT NULL,
  `kategori` enum('Upacara','Libur') NOT NULL,
  `upacara_hari_libur` varchar(5) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tb_upacara_libur`
--

INSERT INTO `tb_upacara_libur` (`id`, `nama_hari`, `tanggal`, `kategori`, `upacara_hari_libur`, `created_at`) VALUES
(0, 'Upacara Bendera', '2021-12-24', 'Upacara', 'yes', '2021-12-24 17:42:22');

-- --------------------------------------------------------

--
-- Table structure for table `tb_users`
--

CREATE TABLE `tb_users` (
  `id` int(100) NOT NULL,
  `nama` varchar(300) NOT NULL,
  `username` varchar(300) NOT NULL,
  `password` varchar(300) NOT NULL,
  `is_active` enum('Ya') DEFAULT 'Ya',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tb_users`
--

INSERT INTO `tb_users` (`id`, `nama`, `username`, `password`, `is_active`, `created_at`) VALUES
(1, 'admin', 'admin', '$2y$10$zyYUBbarAkfHEdQJmonXTu3MNldhSJekmy.Y0UbDrGAP9y8X6mZHS', 'Ya', '2021-12-24 12:00:44'),
(4, 'Hamzah Fauzi', 'fauzy', '$2y$10$/Rzcip06lWooBAKBV9UCtuyaW6Q0D4Eo4EYqT25glpS8nGaJi8iHC', 'Ya', '2021-12-25 10:17:09');

-- --------------------------------------------------------

--
-- Table structure for table `tb_user_roles`
--

CREATE TABLE `tb_user_roles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tb_user_roles`
--

INSERT INTO `tb_user_roles` (`id`, `user_id`, `role_id`) VALUES
(1, 1, 1),
(2, 4, 3);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ci_sessions`
--
ALTER TABLE `ci_sessions`
  ADD KEY `ci_sessions_timestamp` (`timestamp`);

--
-- Indexes for table `tb_absensi`
--
ALTER TABLE `tb_absensi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_absen_manual`
--
ALTER TABLE `tb_absen_manual`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_absen_wajah`
--
ALTER TABLE `tb_absen_wajah`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_izin_kerja`
--
ALTER TABLE `tb_izin_kerja`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_izin_kerja_meta`
--
ALTER TABLE `tb_izin_kerja_meta`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_jabatan_golongan`
--
ALTER TABLE `tb_jabatan_golongan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_jam_kerja`
--
ALTER TABLE `tb_jam_kerja`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_jam_kerja_meta`
--
ALTER TABLE `tb_jam_kerja_meta`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_jam_kerja_new`
--
ALTER TABLE `tb_jam_kerja_new`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_jam_kerja_pegawai`
--
ALTER TABLE `tb_jam_kerja_pegawai`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_jam_kerja_pegawai_new`
--
ALTER TABLE `tb_jam_kerja_pegawai_new`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_kordinat`
--
ALTER TABLE `tb_kordinat`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_kordinat_tambahan`
--
ALTER TABLE `tb_kordinat_tambahan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_menu`
--
ALTER TABLE `tb_menu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_opd`
--
ALTER TABLE `tb_opd`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_pegawai`
--
ALTER TABLE `tb_pegawai`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_pegawai_atasan`
--
ALTER TABLE `tb_pegawai_atasan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_pegawai_meta`
--
ALTER TABLE `tb_pegawai_meta`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_rekap_absen`
--
ALTER TABLE `tb_rekap_absen`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_roles`
--
ALTER TABLE `tb_roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_unit_kerja`
--
ALTER TABLE `tb_unit_kerja`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_upacara_libur`
--
ALTER TABLE `tb_upacara_libur`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_users`
--
ALTER TABLE `tb_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_user_roles`
--
ALTER TABLE `tb_user_roles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_absensi`
--
ALTER TABLE `tb_absensi`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tb_absen_manual`
--
ALTER TABLE `tb_absen_manual`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tb_absen_wajah`
--
ALTER TABLE `tb_absen_wajah`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tb_izin_kerja`
--
ALTER TABLE `tb_izin_kerja`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tb_kordinat`
--
ALTER TABLE `tb_kordinat`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tb_kordinat_tambahan`
--
ALTER TABLE `tb_kordinat_tambahan`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tb_menu`
--
ALTER TABLE `tb_menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tb_opd`
--
ALTER TABLE `tb_opd`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tb_pegawai`
--
ALTER TABLE `tb_pegawai`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tb_pegawai_atasan`
--
ALTER TABLE `tb_pegawai_atasan`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tb_pegawai_meta`
--
ALTER TABLE `tb_pegawai_meta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tb_roles`
--
ALTER TABLE `tb_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tb_unit_kerja`
--
ALTER TABLE `tb_unit_kerja`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tb_users`
--
ALTER TABLE `tb_users`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tb_user_roles`
--
ALTER TABLE `tb_user_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tb_user_roles`
--
ALTER TABLE `tb_user_roles`
  ADD CONSTRAINT `tb_user_roles_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `tb_roles` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `tb_user_roles_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `tb_users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
