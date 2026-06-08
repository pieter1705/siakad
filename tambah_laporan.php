<?php
session_start();
include 'koneksi.php'; 

// Proteksi Admin
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Proses Simpan Data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama      = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $nim       = mysqli_real_escape_string($koneksi, $_POST['nim']);
    $prodi     = mysqli_real_escape_string($koneksi, $_POST['prodi']);
    $semester  = mysqli_real_escape_string($koneksi, $_POST['semester']);
    $jalur     = mysqli_real_escape_string($koneksi, $_POST['jalur_masuk']);
    $jenis     = mysqli_real_escape_string($koneksi, $_POST['jenis_laporan']);
    $jumlah    = mysqli_real_escape_string($koneksi, $_POST['jumlah_pembayaran']);
    $periode   = isset($_POST['periode']) ? mysqli_real_escape_string($koneksi, $_POST['periode']) : "";
    $sub_ujian = isset($_POST['sub_jenis_ujian']) ? mysqli_real_escape_string($koneksi, $_POST['sub_jenis_ujian']) : "";

    // Logika Penggabungan Nama Ujian agar muncul di Admin Panel
    if (strcasecmp($jenis, 'ujian') == 0 && !empty($sub_ujian)) {
        $jenis = "Ujian (" . $sub_ujian . ")";
    }

    $nama_file = "";
    // Logika Upload File (Mendukung input name 'slip' atau 'bukti_bayar')
    $file_input = isset($_FILES['slip']) && $_FILES['slip']['error'] == 0 ? 'slip' : (isset($_FILES['bukti_bayar']) && $_FILES['bukti_bayar']['error'] == 0 ? 'bukti_bayar' : '');

    if ($file_input != '') {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $nama_file = time() . "_" . basename($_FILES[$file_input]["name"]);
        move_uploaded_file($_FILES[$file_input]["tmp_name"], $target_dir . $nama_file);
    }

    // Query Insert dengan status_cetak default 'Belum'
    $query = "INSERT INTO laporan_mahasiswa (nim, nama, prodi, semester, jalur_masuk, jenis_laporan, periode, jumlah_pembayaran, slip, tanggal_input, status_cetak) 
              VALUES ('$nim', '$nama', '$prodi', '$semester', '$jalur', '$jenis', '$periode', '$jumlah', '$nama_file', NOW(), 'Belum')";

    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Data berhasil ditambahkan sebagai $jenis!'); window.location='admin.php';</script>";
        exit;
    } else {
        echo "<script>alert('Gagal: " . mysqli_error($koneksi) . "');</script>";
    }
}

$nama_user = $_SESSION['username'] ?? 'Admin';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Mahasiswa - SIAKAD</title>
    <link rel="shortcut icon" href="unpatti.jpg" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #6366f1; --secondary: #a855f7; --bg-body: #f8fafc; }
        body { background-color: var(--bg-body); font-family: 'Plus Jakarta Sans', sans-serif; }
        .navbar-custom { background: white; border-bottom: 1px solid #e2e8f0; padding: 15px 0; }
        .card-modern { background: white; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.03); padding: 30px; border: 1px solid #eee; }
        .nav-pills-custom { background: #f1f5f9; padding: 8px; border-radius: 15px; gap: 5px; }
        .nav-pills-custom .nav-link { border-radius: 12px; color: #64748b; font-weight: 600; border: none; }
        .nav-pills-custom .nav-link.active { background: white; color: var(--primary) !important; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .form-control, .form-select { border-radius: 12px; padding: 12px 15px; border: 1px solid #e2e8f0; }
        .btn-submit { background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%); border: none; border-radius: 12px; padding: 14px 40px; font-weight: 700; color: white; }
    </style>
</head>
<body>

<nav class="navbar navbar-custom sticky-top mb-4">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center fw-bold text-primary" href="admin.php">
            <img src="unpatti.jpg" alt="Logo" width="35" height="35" class="rounded-circle me-2">
            SIAKAD FKIP
        </a>
        <a href="admin.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">Kembali ke Dashboard</a>
    </div>
</nav>

<div class="container">
    <div class="card-modern mb-5">
        <ul class="nav nav-pills nav-pills-custom mb-5" id="pills-tab">
            <li class="nav-item flex-fill"><button class="nav-link w-100 active" data-bs-toggle="pill" data-bs-target="#pengembalian">Pengembalian</button></li>
            <li class="nav-item flex-fill"><button class="nav-link w-100" data-bs-toggle="pill" data-bs-target="#yudisium">Yudisium</button></li>
            <li class="nav-item flex-fill"><button class="nav-link w-100" data-bs-toggle="pill" data-bs-target="#ujian">Ujian</button></li>
        </ul>

        <div class="tab-content">
            <?php foreach (['pengembalian' => 'Pengembalian', 'yudisium' => 'Daftar Yudisium', 'ujian' => 'Daftar Ujian'] as $id => $title): ?>
            <div class="tab-pane fade <?= $id == 'pengembalian' ? 'show active' : '' ?>" id="<?= $id ?>">
                <div class="text-center mb-5">
                    <h4 class="fw-bold">Formulir <?= $title ?></h4>
                    <p class="text-muted small">Input data mahasiswa secara manual melalui akses admin</p>
                </div>
                
                <form action="" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="jenis_laporan" value="<?= ucfirst($id) ?>">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" placeholder="Nama Mahasiswa" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">NIM</label>
                            <input type="text" name="nim" class="form-control" placeholder="Nomor Induk Mahasiswa" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Program Studi</label>
                            <select name="prodi" class="form-select" required>
                               <option value="" selected disabled>-- Pilih Program Studi --</option>
            <option value="Pendidikan Fisika">Pendidikan Biologi</option>
            <option value="Pendidikan Fisika">Pendidikan Fisika</option>
            <option value="Pendidikan Kimia">Pendidikan Kimia</option>
            <option value="Pendidikan Matematika">Pendidikan Matematika</option>
            <option value="Pendidikan Geografi">Pendidikan Geografi</option>
            <option value="Pendidikan Sejarah">Pendidikan Sejarah</option>
            <option value="Pendidikan PKn">Pendidikan PKn</option>
            <option value="Pendidikan Ekonomi">Pendidikan Ekonomi</option>
            <option value="Pendidikan Akuntansi">Pendidikan Akuntansi</option>
            <option value="Pendidikan Bahasa Jerman">Pendidikan Bahasa Jerman</option>
            <option value="Pendidikan Bahasa dan Sastra Indonesia">Pendidikan Bahasa dan Sastra Indonesia</option>
            <option value="Pendidikan Bahasa Inggris">Pendidikan Bahasa Inggris</option>
            <option value="PGSD">PGSD</option>
            <option value="Bimbingan Konseling">Bimbingan Konseling</option>
            <option value="Pendidikan Luar Sekolah">Pendidikan Luar Sekolah</option>
            <option value="Penjaskesrek">Penjaskesrek</option>
            <option value="Administrasi Pendidikan">Administrasi Pendidikan</option>
                                </select>
                        </div>

                        <?php if($id == 'yudisium'): ?>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Periode Yudisium</label>
                            <select name="periode" class="form-select" required>
                                <option value="Periode I">Periode I</option>
                                <option value="Periode II">Periode II</option>
                                <option value="Periode III">Periode III</option>
                                <option value="Periode IV">Periode IV</option>
                            </select>
                        </div>
                        <?php endif; ?>

                        <?php if($id == 'ujian'): ?>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Jenis Ujian / Seminar</label>
                            <select name="sub_jenis_ujian" class="form-select" required>
                                <option value="Seminar Proposal">Seminar Proposal</option>
                                <option value="Seminar Hasil">Seminar Hasil</option>
                                <option value="Ujian Skripsi">Ujian Skripsi</option>
                            </select>
                        </div>
                        <?php endif; ?>
                        
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Semester</label>
                            <input type="number" name="semester" class="form-control" value="<?= $id == 'yudisium' ? '0' : '' ?>" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Jalur Masuk</label>
                            <select name="jalur_masuk" class="form-select">
                                <option value="Mandiri">Mandiri</option>
                                <option value="SBMPTN">SBMPTN</option>
                                <option value="SNMPTN">SNMPTN</option>
                            </select>
                        </div>
                        
                        <div class="col-12">
                            <div class="p-4 rounded-4 border bg-light">
                                <?php if($id == 'pengembalian'): ?>
                                    <label class="form-label fw-bold small">Upload Slip SPP (Bukti Fisik)</label>
                                    <input type="file" name="slip" class="form-control" required>
                                    <input type="hidden" name="jumlah_pembayaran" value="0">
                                <?php else: ?>
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold small">Upload Bukti Bayar</label>
                                            <input type="file" name="bukti_bayar" class="form-control" required>
                                        </div>
                                        <!--<div class="col-md-6 text-end">-->
                                        <!--    <span class="text-muted small">Nominal Bayar:</span>-->
                                        <!--    <span class="fw-bold fs-4 d-block text-primary">Rp <?= $id == 'yudisium' ? '300.000' : '1.250.000' ?></span>-->
                                        <!--    <input type="hidden" name="jumlah_pembayaran" value="<?= $id == 'yudisium' ? '300000' : '1250000' ?>">-->
                                        <!--</div>-->
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="col-12 text-center mt-4">
                            <button type="submit" class="btn btn-submit w-50">Simpan Data Mahasiswa</button>
                        </div>
                    </div>
                </form>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>