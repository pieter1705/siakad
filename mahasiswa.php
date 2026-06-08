<?php
session_start();
include 'koneksi.php'; 

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'mahasiswa') {
    header("Location: login.php");
    exit;
}

$nama_user = $_SESSION['username'] ?? 'Mahasiswa';
$nim_user = $_SESSION['nim'] ?? '';
$nim_safe = mysqli_real_escape_string($koneksi, $nim_user);
$query_riwayat = mysqli_query($koneksi, "SELECT * FROM laporan_mahasiswa WHERE nim = '$nim_safe' ORDER BY id DESC");
$total_pengajuan = mysqli_num_rows($query_riwayat);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Mahasiswa - Sistem Akademik</title>
    <link rel="shortcut icon" href="unpatti.jpg" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #6366f1;
            --secondary: #a855f7;
            --bg-body: #f8fafc;
            --glass: rgba(255, 255, 255, 0.9);
            --wa-color: #25D366;
        }

        body { 
            background-color: var(--bg-body); 
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #1e293b;
        }

        .navbar-custom {
            background: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 15px 0;
        }

        .welcome-gradient {
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            border-radius: 24px;
            padding: 40px;
            color: white;
            margin-bottom: -60px;
            box-shadow: 0 20px 40px rgba(99, 102, 241, 0.2);
        }

        .card-modern {
            background: var(--glass);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.03);
            padding: 30px;
        }

        .stat-card {
            border-radius: 18px;
            padding: 20px;
            background: white;
            border: 1px solid #f1f5f9;
            transition: all 0.3s ease;
        }

        .stat-card:hover { transform: translateY(-5px); }

        .nav-pills-custom {
            background: #f1f5f9;
            padding: 8px;
            border-radius: 15px;
            gap: 5px;
        }

        .nav-pills-custom .nav-link {
            border-radius: 12px;
            color: #64748b;
            font-weight: 600;
            border: none;
            transition: 0.3s;
        }

        .nav-pills-custom .nav-link.active {
            background: white;
            color: var(--primary) !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .form-control, .form-select {
            border-radius: 12px;
            padding: 12px 15px;
            border: 1px solid #e2e8f0;
            background-color: #fcfcfd;
        }

        .form-control:focus {
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
            border-color: var(--primary);
        }

        .btn-submit {
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            border: none;
            border-radius: 12px;
            padding: 14px 40px;
            font-weight: 700;
            color: white;
            transition: all 0.3s;
        }

        .border-dashed { border-style: dashed !important; }

        /* --- TAMPILAN WA MODERN --- */
        .wa-container {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 9999;
            display: flex;
            align-items: center;
        }

        /* Label di samping tombol */
        .wa-label {
            background: white;
            color: #444;
            padding: 8px 16px;
            border-radius: 12px 0 0 12px;
            box-shadow: -5px 5px 15px rgba(0,0,0,0.08);
            margin-right: -10px;
            font-size: 13px;
            font-weight: 600;
            border: 1px solid #eee;
            border-right: none;
            transition: 0.3s;
        }

        .wa-floating {
            width: 60px;
            height: 60px;
            background-color: var(--wa-color);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            box-shadow: 0 8px 20px rgba(37, 211, 102, 0.4);
            text-decoration: none;
            transition: 0.3s;
            position: relative;
        }

        /* Efek Berdenyut */
        .wa-floating::after {
            content: '';
            width: 100%;
            height: 100%;
            background-color: var(--wa-color);
            position: absolute;
            border-radius: 50%;
            z-index: -1;
            animation: wa-pulse 2s infinite;
        }

        @keyframes wa-pulse {
            0% { transform: scale(1); opacity: 0.6; }
            100% { transform: scale(1.6); opacity: 0; }
        }

        .wa-floating:hover {
            transform: translateY(-5px);
            color: white;
            background-color: #128c7e;
        }

        .wa-container:hover .wa-label {
            padding-right: 25px;
            color: var(--wa-color);
        }
    </style>
</head>
<body>

<nav class="navbar navbar-custom sticky-top mb-4">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center fw-bold text-primary" href="#">
            <div class="bg-primary rounded-3 p-2 me-2 shadow-sm">
                <img src="unpatti.jpg" alt="Logo Unpatti" width="35" height="35" class="rounded-circle">
            </div>
            SIAKAD FKIP
        </a>
        <div class="dropdown">
            <button class="btn btn-light rounded-pill px-3 d-flex align-items-center border" data-bs-toggle="dropdown">
                <div class="bg-secondary rounded-circle me-2 d-flex align-items-center justify-content-center text-white" style="width: 25px; height: 25px; font-size: 12px;">
                    <?= substr($nama_user, 0, 1) ?>
                </div>
                <span class="small fw-semibold d-none d-md-inline"><?= htmlspecialchars($nama_user) ?></span>
                <i class="bi bi-chevron-down ms-2 small"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2 p-2 rounded-4">
                <li><a class="dropdown-item rounded-3 text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Keluar</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <div class="welcome-gradient">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="fw-bold mb-2">Halo, <?= htmlspecialchars($nama_user) ?> (<?= htmlspecialchars($nim_user) ?>)! 👋</h2>
                <p class="opacity-75 mb-0">Kelola pendaftaran dan riwayat akademik Anda dalam satu pintu.</p>
            </div>
            <div class="col-md-4 text-md-end d-none d-md-block">
                <span class="badge bg-white bg-opacity-25 py-2 px-3 rounded-pill">
                    <i class="bi bi-calendar3 me-2"></i><?= date('l, d M Y') ?>
                </span>
            </div>
        </div>
    </div>

    <div class="row mb-4" style="margin-top: 15px;">
        <div class="col-md-4 mb-3">
            <div class="stat-card shadow-sm">
                <div class="d-flex align-items-center">
                    <div class="p-3 bg-primary bg-opacity-10 rounded-4 text-primary me-3">
                        <i class="bi bi-folder2-open fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">Total Pengajuan</small>
                        <span class="fw-bold fs-5"><?= $total_pengajuan ?> Berkas</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="stat-card shadow-sm">
                <div class="d-flex align-items-center">
                    <div class="p-3 bg-success bg-opacity-10 rounded-4 text-success me-3">
                        <i class="bi bi-check-circle fs-4"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">Status Akun</small>
                        <span class="fw-bold fs-5">Mahasiswa Aktif</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-modern mb-5">
        <ul class="nav nav-pills nav-pills-custom mb-5" id="pills-tab">
            <li class="nav-item flex-fill"><button class="nav-link w-100 active" data-bs-toggle="pill" data-bs-target="#pengembalian"><i class="bi bi-cash-stack me-2"></i>Pengembalian</button></li>
            <li class="nav-item flex-fill"><button class="nav-link w-100" data-bs-toggle="pill" data-bs-target="#yudisium"><i class="bi bi-award me-2"></i>Yudisium</button></li>
            <li class="nav-item flex-fill"><button class="nav-link w-100" data-bs-toggle="pill" data-bs-target="#ujian"><i class="bi bi-file-earmark-text me-2"></i>Ujian</button></li>
        </ul>

        <div class="tab-content">
            <?php foreach (['pengembalian' => 'Pengembalian', 'yudisium' => 'Daftar Yudisium', 'ujian' => 'Daftar Ujian'] as $id => $title): ?>
            <div class="tab-pane fade <?= $id == 'pengembalian' ? 'show active' : '' ?>" id="<?= $id ?>">
                <div class="text-center mb-5">
                    <h4 class="fw-bold">Formulir <?= $title ?></h4>
                    <p class="text-muted small">Lengkapi data di bawah ini dengan benar untuk pengajuan berkas.</p>
                </div>
                
                <form action="simpan_laporan.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="jenis_laporan" value="<?= ucfirst($id) ?>">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($nama_user) ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">NIM</label>
                            <input type="text" name="nim" class="form-control bg-light" value="<?= htmlspecialchars($nim_user) ?>" readonly>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Program Studi</label>
                            <select name="prodi" class="form-select" required>
                                <option value="" selected disabled>-- Pilih Program Studi --</option>
                                <option value="Pendidikan Biologi">Pendidikan Biologi</option>
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
                                <option value="" selected disabled>-- Pilih Periode --</option>
                                <option value="Periode I">Periode I</option>
                                <option value="Periode II">Periode II</option>
                                <option value="Periode III">Periode III</option>
                                <option value="Periode IV">Periode IV</option>
                            </select>
                        </div>
                        <?php endif; ?>

                        <?php if($id == 'ujian'): ?>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Jenis Ujian</label>
                            <select name="sub_jenis_ujian" class="form-select" required>
                                <option value="" selected disabled>-- Pilih Jenis Ujian --</option>
                                <option value="Seminar Proposal">Seminar Proposal</option>
                                <option value="Seminar Hasil">Seminar Hasil</option>
                                <option value="Ujian Skripsi">Ujian Skripsi</option>
                            </select>
                        </div>
                        <?php endif; ?>
                        
                        <?php if($id != 'yudisium'): ?>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Semester</label>
                            <input type="number" name="semester" class="form-control" placeholder="Contoh: 8" required>
                        </div>
                        <?php else: ?>
                            <input type="hidden" name="semester" value="0">
                        <?php endif; ?>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Jalur Masuk</label>
                            <select name="jalur_masuk" class="form-select">
                                <option value="Mandiri">Mandiri</option>
                                <option value="SBMPTN">SBMPTN</option>
                                <option value="SNMPTN">SNMPTN</option>
                            </select>
                        </div>

                        <?php if($id == 'pengembalian' || $id == 'ujian'): ?>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Tanggal Pembayaran</label>
                            <input type="date" name="tanggal_pembayaran" class="form-control" required>
                        </div>
                        <?php endif; ?>
                        
                        <div class="col-12">
                            <?php if($id != 'yudisium'): ?>
                            <div class="p-4 rounded-4 border-dashed border-2 bg-light">
                                <div class="text-center">
                                    <i class="bi bi-cloud-arrow-up fs-2 text-primary mb-2 d-block"></i>
                                    <label class="form-label fw-bold">
                                        <?= $id == 'pengembalian' ? 'Upload Slip SPP' : 'Upload Bukti Pembayaran Ujian' ?>
                                    </label>
                                    <div class="mx-auto" style="max-width: 400px;">
                                        <input type="file" name="<?= $id == 'pengembalian' ? 'slip' : 'bukti_bayar' ?>" class="form-control" required>
                                    </div>
                                    <span class="text-muted small">Format PDF/JPG (Maks. 2MB)</span>
                                </div>
                            </div>
                            <?php else: ?>
                            <div class="p-4 rounded-4 border bg-light text-center">
                                <span class="text-muted d-block small">Biaya Administrasi Yudisium:</span>
                                <span class="fw-bold fs-4 text-dark">Rp 300.000</span>
                                <input type="hidden" name="jumlah_pembayaran" value="300000">
                                <p class="text-muted small mb-0 mt-2 italic">*Pendaftaran Yudisium tidak memerlukan unggahan berkas saat ini.</p>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="col-12 text-center mt-4">
                            <button type="submit" class="btn btn-submit shadow-lg">Kirim Berkas <i class="bi bi-send-fill ms-2"></i></button>
                        </div>
                    </div>
                </form>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <div class="card-modern mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>Riwayat Pengajuan</h5>
            <button class="btn btn-sm btn-outline-secondary rounded-pill">Lihat Semua</button>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th class="border-0 px-4 py-3 rounded-start">Jenis</th>
                        <th class="border-0 py-3">Prodi</th>
                        <th class="border-0 py-3">Semester</th>
                        <th class="border-0 py-3 text-center">Status</th>
                        <th class="border-0 px-4 py-3 text-center rounded-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($query_riwayat && mysqli_num_rows($query_riwayat) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($query_riwayat)): ?>
                        <tr>
                            <td><span class="fw-bold"><?= htmlspecialchars($row['jenis_laporan']) ?></span></td>
                            <td><?= htmlspecialchars($row['prodi']) ?></td>
                            <td><?= $row['semester'] == 0 ? 'Yudisium' : 'Semester '.htmlspecialchars($row['semester']) ?></td>
                            <td><span class="badge bg-success">Terkirim</span></td>
                            <td class="text-center">
                                <a href="lihat_laporan.php?id=<?= $row['id'] ?>" target="_blank" class="btn btn-sm btn-outline-info rounded-pill px-3">
                                    <i class="bi bi-eye me-1"></i> Detail
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center py-4 text-muted small">Belum ada riwayat pengajuan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="wa-container">
    <div class="wa-label d-none d-md-block">Hubungi Admin</div>
    <a href="https://wa.me/628134859376?text=Halo%20Admin%20FKIP,%20saya%20<?= urlencode($nama_user) ?>%20ingin%20bertanya%20terkait%20pengajuan%20berkas..." 
       class="wa-floating" 
       target="_blank">
        <i class="bi bi-whatsapp"></i>
    </a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>