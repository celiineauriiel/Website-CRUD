-- phpMyAdmin SQL Dump

-- version 5.1.1

-- https://www.phpmyadmin.net/

--

-- Host: 127.0.0.1

-- Waktu pembuatan: 28 Mar 2022 pada 18.27 (INI HANYA TANGGAL DUMP ASLI, AKAN DIUPDATE DI BAWAH)

-- Versi server: 10.4.21-MariaDB

-- Versi PHP: 8.0.10



SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

START TRANSACTION;

SET time_zone = "+00:00";





/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;

/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;

/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;

/*!40101 SET NAMES utf8mb4 */;



--

-- Database: `Data_siswa`

--



-- --------------------------------------------------------



--

-- Hapus tabel yang sudah ada jika ingin memulai dari awal (opsional)

-- Jika Anda hanya ingin ALTER TABLE dan INSERT baru, Anda bisa mengomentari baris DROP TABLE

-- DROP TABLE IF EXISTS `siswa`;

-- DROP TABLE IF EXISTS `user`;





--

-- Struktur dari tabel `siswa` (REVISI STRUKTUR)

-- Menambahkan kolom `ipk` dan `jalur_masuk`

--

CREATE TABLE IF NOT EXISTS `siswa` (

  `nis` varchar(50) NOT NULL PRIMARY KEY, -- Pastikan NIS adalah PRIMARY KEY

  `nama` varchar(255) NOT NULL,

  `tmpt_Lahir` varchar(50) NOT NULL,

  `tgl_Lahir` date NOT NULL,

  `jekel` enum('Laki - Laki','Perempuan') NOT NULL,

  `jurusan` enum('Teknik Listrik','Teknik Komputer dan Jaringan','Multimedia','Rekayasa Perangkat Lunak','Geomatika','Mesin') NOT NULL,

  `ipk` FLOAT(4,2) NOT NULL, -- Kolom baru: IPK, contoh 3.13, 3.77 (4 digit total, 2 digit di belakang koma)

  `jalur_masuk` VARCHAR(50) NOT NULL, -- Kolom baru: Jalur Masuk

  `email` varchar(255) NOT NULL,

  `gambar` varchar(255) NOT NULL,

  `alamat` text NOT NULL

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



--

-- Mengosongkan tabel `siswa` sebelum memasukkan data baru

--

TRUNCATE TABLE `siswa`;



--

-- Dumping data untuk tabel `siswa` (DATA BARU)

--

INSERT INTO `siswa` (`nis`, `nama`, `tmpt_Lahir`, `tgl_Lahir`, `jekel`, `jurusan`, `ipk`, `jalur_masuk`, `email`, `gambar`, `alamat`) VALUES

('5026221001', 'Siti Rahayu', 'Surabaya', '2004-11-23', 'Perempuan', 'Multimedia', 3.13, 'Mandiri', 'siti.rahayu@example.com', '', 'Perumahan Indah Permai Blok C7, Surabaya'),

('5026221002', 'Agus Setiawan', 'Bandung', '2003-01-01', 'Laki - Laki', 'Rekayasa Perangkat Lunak', 3.15, 'SNBT', 'agus.setiawan@example.com', '', 'Kp. Melati RT 05 RW 03, Bandung Barat'),

('5026221003', 'Dewi Lestari', 'Yogyakarta', '2004-07-15', 'Perempuan', 'Geomatika', 3.36, 'SNBT', 'dewi.lestari@example.com', '', 'Gg. Anggrek No. 22, Sleman'),

('5026221004', 'Rudi Haryanto', 'Medan', '2003-09-30', 'Laki - Laki', 'Mesin', 3.37, 'Mandiri', 'rudi.haryanto@example.com', '', 'Jl. Pahlawan No. 5, Medan'),

('5026221005', 'Putri Ayu', 'Makassar', '2004-03-08', 'Perempuan', 'Teknik Listrik', 3.77, 'Mandiri', 'putri.ayu@example.com', '', 'Komplek Griya Elok Tahap II, Makassar');



-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO user (username, password) VALUES ('Admin', MD5('admin'));

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
