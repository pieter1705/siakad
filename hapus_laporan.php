<?php
session_start();
require_once 'koneksi.php';

// Proteksi halaman: Pastikan hanya admin yang bisa menghapus
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Cek apakah ada ID yang dikirim melalui URL
if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['id']);

    // 1. (Opsional) Ambil nama file slip jika ingin menghapus file fisiknya juga dari folder uploads
    $query_file = mysqli_query($koneksi, "SELECT slip FROM laporan_mahasiswa WHERE id = '$id'");
    $data_file = mysqli_fetch_assoc($query_file);
    
    if ($data_file && !empty($data_file['slip'])) {
        $path = "uploads/" . $data_file['slip'];
        if (file_exists($path)) {
            unlink($path); // Menghapus file gambar dari server
        }
    }

    // 2. Jalankan perintah hapus dari database
    $delete = mysqli_query($koneksi, "DELETE FROM laporan_mahasiswa WHERE id = '$id'");

    if ($delete) {
        // Jika berhasil, kirim pesan sukses
        echo "<script>
                alert('Data mahasiswa berhasil dihapus!');
                window.location.href = 'admin.php';
              </script>";
    } else {
        // Jika gagal
        echo "<script>
                alert('Gagal menghapus data: " . mysqli_error($koneksi) . "');
                window.location.href = 'admin.php';
              </script>";
    }
} else {
    // Jika tidak ada ID, balikkan ke dashboard
    header("Location: admin.php");
}
?>