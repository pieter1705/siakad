<?php
session_start();
include 'koneksi.php'; // Pastikan file koneksi sudah benar

// Proteksi halaman
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'mahasiswa') {
    header("Location: login.php");
    exit;
}

$nama_user = isset($_SESSION['username']) ? $_SESSION['username'] : 'Mahasiswa';
$nim_user = isset($_SESSION['nim']) ? $_SESSION['nim'] : '';

// Mengambil riwayat berdasarkan NIM yang login
$query_riwayat = mysqli_query($koneksi, "SELECT * FROM laporan WHERE nim = '$nim_user' ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pembayaran - Sistem Akademik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root { --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        body { background-color: #f0f2f5; font-family: 'Inter', sans-serif; }
        .navbar { background: var(--primary-gradient); box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .card-custom { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); background: #ffffff; }
        .badge-status { border-radius: 50px; padding: 5px 12px; font-size: 0.8rem; }
        .btn-back { border-radius: 10px; font-weight: 600; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="dashboard_index.php">
            <i class="bi bi-mortarboard-fill me-2"></i>Sistem Akademik
        </a>
        <a href="logout.php" class="btn btn-light btn-sm rounded-pill px-3 fw-bold text-danger">Keluar</a>
    </div>
</nav>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-dark mb-0">Riwayat Transaksi & Berkas</h4>
        <a href="dashboard_index.php" class="btn btn-outline-primary btn-back">
            <i class="bi bi-plus-circle me-1"></i> Input Baru
        </a>
    </div>

    <div class="card card-custom p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>Jenis Layanan</th>
                        <th>Program Studi</th>
                        <th>Semester</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($query_riwayat) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($query_riwayat)): ?>
                        <tr>
                            <td class="small"><?= date('d/m/Y', strtotime($row['created_at'] ?? 'now')) ?></td>
                            <td><span class="fw-bold"><?= $row['jenis_laporan'] ?></span></td>
                            <td><?= htmlspecialchars($row['prodi']) ?></td>
                            <td class="text-center"><?= $row['semester'] ?></td>
                            <td><span class="badge bg-success badge-status">Terkirim</span></td>
                            <td class="text-center">
                                <?php if(!empty($row['file_slip'])): ?>
                                    <a href="uploads/<?= $row['file_slip'] ?>" class="btn btn-sm btn-primary rounded-pill px-3" download>
                                        <i class="bi bi-download me-1"></i> Download Slip
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted small">Tidak ada file</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                Belum ada riwayat pengajuan berkas.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>