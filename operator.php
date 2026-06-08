<?php
session_start();
include 'koneksi.php';

// 1. Proteksi Session
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'operator') {
    header("Location: login.php?pesan=bukan_operator");
    exit;
}

$prodi_operator = $_SESSION['prodi'];

/**
 * 2. QUERY OPTIMAL
 * l.id AS id_laporan digunakan agar link "Detail" mengarah ke record laporan yang benar
 * Filter l.prodi digunakan karena data prodi di tabel users milik mahasiswa masih kosong (NULL)
 */
$query = "SELECT 
            u.id AS id_user, 
            l.id AS id_laporan, 
            u.nim, 
            u.username, 
            l.status_cetak, 
            l.jenis_laporan 
          FROM users u
          INNER JOIN laporan_mahasiswa l ON TRIM(u.nim) = TRIM(l.nim)
          WHERE u.role = 'mahasiswa' 
          AND l.prodi = '$prodi_operator' 
          AND l.status_cetak = 'Sudah'
          ORDER BY l.id DESC";

$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Operator - <?= htmlspecialchars($prodi_operator); ?></title>
    <link rel="shortcut icon" href="unpatti.jpg" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #4f46e5;
            --secondary-color: #6366f1;
            --bg-light: #f8fafc;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-light);
            color: #1e293b;
        }

        .navbar {
            background: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 0;
        }

        .main-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            background: white;
        }

        .card-header-custom {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 16px 16px 0 0 !important;
            padding: 1.5rem;
        }

        .table thead th {
            background-color: #f1f5f9;
            color: #64748b;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            font-weight: 700;
            border: none;
            padding: 1rem;
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }

        .badge-lunas {
            background-color: #dcfce7;
            color: #166534;
            font-weight: 600;
            padding: 0.5em 1em;
            border-radius: 8px;
        }

        .btn-action {
            border-radius: 10px;
            padding: 0.5rem 1rem;
            font-weight: 600;
            transition: all 0.2s;
            text-decoration: none;
        }

        .btn-action:hover {
            transform: translateY(-2px);
        }

        .stats-box {
            background: #eef2ff;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
        }

        .stats-icon {
            width: 48px;
            height: 48px;
            background: var(--primary-color);
            color: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-right: 1rem;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <span class="navbar-brand fw-bold">
            <i class="bi bi-grid-1x2-fill me-2 text-primary"></i>Panel Operator
        </span>
        <div class="d-flex align-items-center">
            <span class="text-muted me-3 d-none d-md-inline">Halo, <strong><?= $_SESSION['username']; ?></strong></span>
            <a href="logout.php" class="btn btn-outline-danger btn-sm fw-bold border-2 rounded-pill px-4">
                <i class="bi bi-box-arrow-right me-1"></i> Keluar
            </a>
        </div>
    </div>
</nav>

<div class="container my-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="fw-bold mb-1">Manajemen Jadwal Seminar</h2>
            <p class="text-muted">Kelola plotting dosen pembimbing dan penguji mahasiswa prodi Anda.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="stats-box">
                <div class="stats-icon"><i class="bi bi-people"></i></div>
                <div>
                    <h6 class="mb-0 text-muted">Mahasiswa Terverifikasi</h6>
                    <h4 class="mb-0 fw-bold"><?= mysqli_num_rows($result); ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-8 text-md-end pt-3">
             <span class="badge bg-white text-dark shadow-sm p-3 rounded-pill border">
                <i class="bi bi-mortarboard-fill me-2 text-primary"></i> 
                Prodi: <?= htmlspecialchars($prodi_operator); ?>
             </span>
        </div>
    </div>

    <div class="card main-card">
        <div class="card-header-custom d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="bi bi-clipboard-check me-2"></i>Daftar Antrian Plotting</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th>Informasi Mahasiswa</th>
                            <th>Jenis Laporan</th>
                            <th class="text-center">Status Pembayaran</th>
                            <th class="text-center">Aksi Operator</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1; 
                        if (mysqli_num_rows($result) > 0) :
                            while($row = mysqli_fetch_assoc($result)) : 
                        ?>
                        <tr>
                            <td class="text-center fw-bold text-muted"><?= $no++; ?></td>
                            <td>
                                <div class="fw-bold"><?= htmlspecialchars($row['username']); ?></div>
                                <div class="text-muted small"><?= htmlspecialchars($row['nim']); ?></div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border"><?= htmlspecialchars($row['jenis_laporan']); ?></span>
                            </td>
                            <td class="text-center">
                                <span class="badge-lunas"><i class="bi bi-patch-check-fill me-1"></i> Terverifikasi</span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="lihat_laporan.php?id=<?= $row['id_laporan']; ?>" class="btn btn-light btn-action text-info shadow-sm">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                    <a href="atur_jadwal.php?id=<?= $row['id_user']; ?>" class="btn btn-primary btn-action shadow-sm">
                                        <i class="bi bi-calendar-event me-1"></i> Atur Jadwal
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-folder-x fs-1 opacity-25"></i>
                                    <p class="mt-2">Belum ada data pendaftaran yang diverifikasi untuk prodi ini.</p>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <footer class="mt-5 text-center text-muted small">
        &copy; 2026 Portal Akademik Universitas Pattimura. All rights reserved.
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>