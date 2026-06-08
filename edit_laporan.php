<?php
session_start();
include 'koneksi.php';

// 1. Proteksi akses admin
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// 2. Ambil data mahasiswa berdasarkan ID
if (!isset($_GET['id'])) {
    header("Location: admin.php");
    exit;
}

$id = mysqli_real_escape_string($koneksi, $_GET['id']);
$query = mysqli_query($koneksi, "SELECT * FROM laporan_mahasiswa WHERE id = '$id'");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    die("Data tidak ditemukan di database.");
}

// 3. Proses Update Data
if (isset($_POST['update'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $nim = mysqli_real_escape_string($koneksi, $_POST['nim']);
    $prodi = mysqli_real_escape_string($koneksi, $_POST['prodi']);
    $semester = mysqli_real_escape_string($koneksi, $_POST['semester']);
    $jalur = mysqli_real_escape_string($koneksi, $_POST['jalur_masuk']);
    $jenis = mysqli_real_escape_string($koneksi, $_POST['jenis_laporan']);
    
    // Logika penggabungan Jenis Laporan jika tipenya Ujian
    if ($jenis == 'Ujian' && isset($_POST['sub_jenis_ujian'])) {
        $sub_ujian = mysqli_real_escape_string($koneksi, $_POST['sub_jenis_ujian']);
        $jenis_final = "Ujian (" . $sub_ujian . ")";
    } else {
        $jenis_final = $jenis;
    }
    
    $periode = isset($_POST['periode']) ? mysqli_real_escape_string($koneksi, $_POST['periode']) : "";
    $jumlah = isset($_POST['jumlah_pembayaran']) ? mysqli_real_escape_string($koneksi, $_POST['jumlah_pembayaran']) : 0;

    $sql_update = "UPDATE laporan_mahasiswa SET 
                    nama = '$nama', 
                    nim = '$nim', 
                    prodi = '$prodi', 
                    semester = '$semester', 
                    jalur_masuk = '$jalur',
                    jenis_laporan = '$jenis_final',
                    periode = '$periode',
                    jumlah_pembayaran = '$jumlah'
                   WHERE id = '$id'";

    if (mysqli_query($koneksi, $sql_update)) {
        echo "<script>alert('Data berhasil diperbarui!'); window.location='admin.php';</script>";
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}

// Logika untuk menentukan nilai awal dropdown (memisahkan "Ujian" dari "Seminar Proposal" dsb)
$current_jenis = $data['jenis_laporan'];
$is_ujian = (strpos($current_jenis, 'Ujian') !== false);
$sub_jenis_value = "";
if($is_ujian) {
    preg_match('#\((.*?)\)#', $current_jenis, $match);
    $sub_jenis_value = $match[1] ?? "";
    $current_jenis = "Ujian";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Mahasiswa - Panel Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root { --primary-gradient: linear-gradient(135deg, #4e73df 0%, #224abe 100%); }
        body { background-color: #f8f9fc; font-family: 'Inter', sans-serif; }
        .navbar { background: var(--primary-gradient); box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .card-custom { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); background: #ffffff; }
        .btn-submit { background: var(--primary-gradient); border: none; border-radius: 10px; padding: 12px 30px; font-weight: 600; color:white; }
        .form-label { font-weight: 600; color: #4e73df; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="admin.php"><i class="bi bi-shield-lock-fill me-2"></i>Edit Data Akademik</a>
        <a href="admin.php" class="btn btn-light btn-sm rounded-pill px-3 fw-bold text-primary">Kembali</a>
    </div>
</nav>

<div class="container mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card card-custom p-4">
                <h5 class="fw-bold mb-4 text-center"><i class="bi bi-pencil-square me-2"></i>Form Perbaikan Data Mahasiswa</h5>
                
                <form action="" method="POST">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($data['nama']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">NIM</label>
                            <input type="text" name="nim" class="form-control" value="<?= htmlspecialchars($data['nim']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Program Studi</label>
                            <input type="text" name="prodi" class="form-control" value="<?= htmlspecialchars($data['prodi']) ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Semester</label>
                            <input type="number" name="semester" class="form-control" value="<?= htmlspecialchars($data['semester']) ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Jenis Laporan</label>
                            <select name="jenis_laporan" id="jenis_laporan" class="form-select" onchange="toggleFields()">
                                <option value="Pengembalian" <?= $current_jenis == 'Pengembalian' ? 'selected' : '' ?>>Pengembalian</option>
                                <option value="Yudisium" <?= $current_jenis == 'Yudisium' ? 'selected' : '' ?>>Yudisium</option>
                                <option value="Ujian" <?= $current_jenis == 'Ujian' ? 'selected' : '' ?>>Ujian</option>
                            </select>
                        </div>

                        <div class="col-md-6" id="wrapper_periode" style="display: <?= $current_jenis == 'Yudisium' ? 'block' : 'none' ?>;">
                            <label class="form-label">Periode Yudisium</label>
                            <select name="periode" class="form-select">
                                <option value="" disabled <?= empty($data['periode']) ? 'selected' : '' ?>>-- Pilih Periode --</option>
                                <option value="Periode I" <?= $data['periode'] == 'Periode I' ? 'selected' : '' ?>>Periode I</option>
                                <option value="Periode II" <?= $data['periode'] == 'Periode II' ? 'selected' : '' ?>>Periode II</option>
                                <option value="Periode III" <?= $data['periode'] == 'Periode III' ? 'selected' : '' ?>>Periode III</option>
                                <option value="Periode IV" <?= $data['periode'] == 'Periode IV' ? 'selected' : '' ?>>Periode IV</option>
                            </select>
                        </div>

                        <div class="col-md-6" id="wrapper_ujian" style="display: <?= $current_jenis == 'Ujian' ? 'block' : 'none' ?>;">
                            <label class="form-label">Jenis Ujian</label>
                            <select name="sub_jenis_ujian" class="form-select">
                                <option value="" disabled <?= empty($sub_jenis_value) ? 'selected' : '' ?>>-- Pilih Jenis Ujian --</option>
                                <option value="Seminar Proposal" <?= $sub_jenis_value == 'Seminar Proposal' ? 'selected' : '' ?>>Seminar Proposal</option>
                                <option value="Seminar Hasil" <?= $sub_jenis_value == 'Seminar Hasil' ? 'selected' : '' ?>>Seminar Hasil</option>
                                <option value="Ujian Skripsi" <?= $sub_jenis_value == 'Ujian Skripsi' ? 'selected' : '' ?>>Ujian Skripsi</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Jalur Masuk</label>
                            <select name="jalur_masuk" class="form-select">
                                <option value="Mandiri" <?= $data['jalur_masuk'] == 'Mandiri' ? 'selected' : '' ?>>Mandiri</option>
                                <option value="SBMPTN" <?= $data['jalur_masuk'] == 'SBMPTN' ? 'selected' : '' ?>>SBMPTN</option>
                                <option value="SNMPTN" <?= $data['jalur_masuk'] == 'SNMPTN' ? 'selected' : '' ?>>SNMPTN</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Jumlah Pembayaran (Khusus Yudisium/Ujian)</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="jumlah_pembayaran" class="form-control" value="<?= htmlspecialchars($data['jumlah_pembayaran']) ?>">
                            </div>
                        </div>

                        <div class="col-12 mt-4">
                            <div class="p-3 bg-light rounded-3 border">
                                <p class="mb-1 fw-bold text-muted small text-uppercase">Informasi Berkas Saat Ini:</p>
                                <?php if(!empty($data['slip'])): ?>
                                    <span class="text-success small"><i class="bi bi-file-earmark-check me-1"></i> File: <?= $data['slip'] ?></span>
                                <?php else: ?>
                                    <span class="text-danger small"><i class="bi bi-file-earmark-x me-1"></i> Tidak ada slip yang diupload</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="col-12 text-center mt-5">
                            <button type="submit" name="update" class="btn btn-submit px-5 shadow">Simpan Perubahan Data</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleFields() {
        const jenisLaporan = document.getElementById('jenis_laporan').value;
        const wrapperPeriode = document.getElementById('wrapper_periode');
        const wrapperUjian = document.getElementById('wrapper_ujian');
        
        // Sembunyikan semua dulu
        wrapperPeriode.style.display = 'none';
        wrapperUjian.style.display = 'none';

        if (jenisLaporan === 'Yudisium') {
            wrapperPeriode.style.display = 'block';
        } else if (jenisLaporan === 'Ujian') {
            wrapperUjian.style.display = 'block';
        }
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>