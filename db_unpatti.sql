-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Waktu pembuatan: 08 Jan 2026 pada 14.42
-- Versi server: 11.8.3-MariaDB-log
-- Versi PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u828377116_db_unpatti`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `laporan_mahasiswa`
--

CREATE TABLE `laporan_mahasiswa` (
  `id` int(11) NOT NULL,
  `nim` varchar(50) DEFAULT NULL,
  `nama` varchar(250) DEFAULT NULL,
  `prodi` varchar(250) DEFAULT NULL,
  `semester` int(11) DEFAULT NULL,
  `jalur_masuk` enum('Mandiri','SBMPTN','SNMPTN') DEFAULT NULL,
  `jenis_laporan` enum('Pengembalian','Yudisium','Ujian') DEFAULT NULL,
  `periode` varchar(50) DEFAULT NULL,
  `slip` varchar(255) DEFAULT NULL,
  `jumlah_pembayaran` int(11) DEFAULT NULL,
  `bukti_spp` varchar(255) DEFAULT NULL,
  `tanggal_input` timestamp NOT NULL DEFAULT current_timestamp(),
  `status_cetak` enum('Belum','Sudah') DEFAULT 'Belum',
  `waktu_cetak` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nim` varchar(100) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','mahasiswa') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `nim`, `username`, `password`, `role`) VALUES
(5, '123456', 'udut', '$2y$10$M7U9.zBxICuD02Z6nNCFjeT/z0RAbqN1y1jTo0a7zvlbjvkHi8Ucq', 'mahasiswa'),
(6, 'admin_unpatti', 'administrator', '$2y$10$pBlRh38j1X7H8oVI8JYtTeyGK/CvrIL0kbvMFXS2PQoKr04t8n0kG', 'admin'),
(7, '098765', 'culla', '$2y$10$yOd8oEpo1JcKGm6hw5cO6uqvRJKD7VDnKI1AoKkZNC4HbEoAe50g6', 'mahasiswa'),
(8, '202040041', '202040041', '$2y$10$kVF5tp1EGj5PShnorJVpr.s9DBLzPXlzyzvK4nR9W8GtHp.YMXRtW', 'mahasiswa'),
(9, '199310262025212059', 'Julien B. Noya S.Si', '$2y$10$myNgbfvL89CLc6h9xOn4o.uf9vTZOvhIQQz5onmH4Z9vaNNGRcXxu', 'admin'),
(10, '198911162025211041', 'Patrick J. Toisuta, S.Pd', '$2y$10$Y5KhHq7TLtDfJGT2c/IYdOd4v51nb718M8EDsmDSV6WKq7SUxslPi', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `laporan_mahasiswa`
--
ALTER TABLE `laporan_mahasiswa`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `laporan_mahasiswa`
--
ALTER TABLE `laporan_mahasiswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
