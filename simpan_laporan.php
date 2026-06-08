<?php
session_start();
include 'koneksi.php';

/**
 * Validasi Akses: Pastikan hanya melalui metode POST
 */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

/**
 * 1. AMBIL DAN BERSIHKAN DATA (SANITIZATION)
 */
$nama      = mysqli_real_escape_string($koneksi, $_POST['nama']);
$nim       = mysqli_real_escape_string($koneksi, $_POST['nim']);
$prodi     = mysqli_real_escape_string($koneksi, $_POST['prodi']);
$semester  = mysqli_real_escape_string($koneksi, $_POST['semester']);
$jalur     = mysqli_real_escape_string($koneksi, $_POST['jalur_masuk']);

// Data Kondisional
$jenis_raw = isset($_POST['jenis_laporan']) ? trim($_POST['jenis_laporan']) : "";
$jenis     = mysqli_real_escape_string($koneksi, $jenis_raw);
$periode   = isset($_POST['periode']) ? mysqli_real_escape_string($koneksi, $_POST['periode']) : "";
$sub_ujian = isset($_POST['sub_jenis_ujian']) ? mysqli_real_escape_string($koneksi, $_POST['sub_jenis_ujian']) : "";
$tgl_bayar = isset($_POST['tanggal_pembayaran']) ? mysqli_real_escape_string($koneksi, $_POST['tanggal_pembayaran']) : null;

/**
 * 2. LOGIKA PENAMAAN JENIS LAPORAN
 */
if (strcasecmp($jenis, 'ujian') == 0 && !empty($sub_ujian)) {
    $jenis = "Ujian (" . $sub_ujian . ")";
}

/**
 * 3. LOGIKA PENGELOLAAN FILE (UPLOAD)
 */
$nama_file  = "";
$jumlah     = 0;
$target_dir = "uploads/";

if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true);
}

// Cek apakah jenisnya Yudisium
$is_yudisium = (stripos($jenis, 'yudisium') !== false);

// Tentukan field file berdasarkan jenis laporan
$field_file = (strcasecmp($jenis, 'pengembalian') == 0) ? 'slip' : 'bukti_bayar';

// Logika Upload: Wajib jika BUKAN yudisium
if (isset($_FILES[$field_file]) && $_FILES[$field_file]['error'] != 4) {
    $file_ext    = pathinfo($_FILES[$field_file]["name"], PATHINFO_EXTENSION);
    $nama_file   = time() . "_" . $nim . "_" . uniqid() . "." . $file_ext;
    $target_file = $target_dir . $nama_file;

    $allowed_ext = ['jpg', 'jpeg', 'png', 'pdf'];
    if (!in_array(strtolower($file_ext), $allowed_ext)) {
        echo "<script>alert('Format file tidak didukung! Gunakan JPG/PNG/PDF.'); window.history.back();</script>";
        exit;
    }

    move_uploaded_file($_FILES[$field_file]["tmp_name"], $target_file);
} else {
    // Jika tidak ada file, cek apakah ini yudisium. 
    // Jika yudisium, biarkan lewat. Jika bukan, maka error.
    if (!$is_yudisium) {
        echo "<script>alert('Wajib mengunggah bukti/slip untuk pengajuan ini!'); window.history.back();</script>";
        exit;
    }
}

/**
 * 4. LOGIKA NOMINAL PEMBAYARAN
 */
if ($is_yudisium) {
    $jumlah = isset($_POST['jumlah_pembayaran']) ? (int)$_POST['jumlah_pembayaran'] : 300000;
} else {
    $jumlah = 0; // Atur 0 atau sesuaikan jika ada nominal lain
}

/**
 * 5. EKSEKUSI QUERY INSERT KE DATABASE
 */
$query = "INSERT INTO laporan_mahasiswa (
            nama, 
            nim, 
            prodi, 
            semester, 
            jalur_masuk, 
            jenis_laporan, 
            slip, 
            jumlah_pembayaran, 
            periode, 
            tanggal_pembayaran, 
            tanggal_input, 
            status_cetak
          ) VALUES (
            '$nama', 
            '$nim', 
            '$prodi', 
            '$semester', 
            '$jalur', 
            '$jenis', 
            '$nama_file', 
            '$jumlah', 
            '$periode', 
            " . ($tgl_bayar ? "'$tgl_bayar'" : "NULL") . ", 
            NOW(), 
            'Belum'
          )";

if (mysqli_query($koneksi, $query)) {
    echo "<script>
            alert('Berhasil! Berkas pengajuan $jenis telah terkirim.');
            window.location.href = 'index.php';
          </script>";
} else {
    echo "Gagal menyimpan data ke database: " . mysqli_error($koneksi);
}

mysqli_close($koneksi);
?>