<?php
session_start();
// SET ZONA WAKTU KE WIT (Asia/Jayapura) agar realtime sesuai lokasi Ambon
date_default_timezone_set('Asia/Jayapura');
// 1. Proteksi Login dan Role
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;   
}

require_once 'koneksi.php'; 

if (!isset($koneksi)) {
    die("Error: Variabel koneksi database (\$koneksi) tidak ditemukan.");
}

// 2. DEFINISI ROLE & USER
$username_session = strtolower(trim($_SESSION['username']));
$is_patrick = ($username_session == 'patrick' || $username_session == 'patrick j. toisuta, s.pd');
$is_restricted = ($username_session == 'jelien' || 
                  $username_session == 'jelien b. noya, sh' || 
                  strpos($username_session, 'christian') !== false);

// 3. LOGIKA FILTER DATA
$search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';
$filter_tipe = isset($_GET['tipe']) ? mysqli_real_escape_string($koneksi, $_GET['tipe']) : '';

if ($is_restricted) {
    $filter_tipe = 'Yudisium';
}

$sql = "SELECT * FROM laporan_mahasiswa WHERE 1=1";
if ($search != '') {
    $sql .= " AND (nama LIKE '%$search%' OR nim LIKE '%$search%' OR prodi LIKE '%$search%')";
}
if ($filter_tipe != '') {
    $sql .= " AND jenis_laporan LIKE '%$filter_tipe%'";
}

$sql .= " ORDER BY id DESC";
$query = mysqli_query($koneksi, $sql);
$total_data = mysqli_num_rows($query);

// Hitung statistik validasi
$sql_valid = "SELECT COUNT(*) as total FROM laporan_mahasiswa WHERE status_cetak = 'Sudah'";
if($is_restricted) $sql_valid .= " AND jenis_laporan LIKE '%Yudisium%'";
$res_valid = mysqli_fetch_assoc(mysqli_query($koneksi, $sql_valid));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - SIAKAD FKIP</title>
    <link rel="shortcut icon" href="unpatti.jpg" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-purple: #6a11cb; --secondary-purple: #2575fc; --deep-purple: #4834d4;
            --soft-purple: #f3f0ff; --accent-purple: #7d5fff; --success: #00b894;
            --danger: #ff7675; --light-bg: #f5f6fa;
        }
        body { background-color: var(--light-bg); font-family: 'Inter', sans-serif; color: #2f3640; }
        .navbar { background: linear-gradient(135deg, var(--primary-purple) 0%, var(--secondary-purple) 100%) !important; box-shadow: 0 4px 15px rgba(106, 17, 203, 0.2); padding: 15px 0; }
        .stat-card { border: none; border-radius: 20px; padding: 1.5rem; background: #fff; transition: all 0.4s; box-shadow: 0 10px 20px rgba(0,0,0,0.02); }
        .card-main { border: none; border-radius: 24px; box-shadow: 0 15px 35px rgba(0,0,0,0.05); background: #fff; overflow: hidden; margin-top: 20px; }
        .table thead th { background-color: #f8faff; border-bottom: 2px solid #edf2f7; padding: 20px; font-weight: 700; font-size: 0.75rem; color: #7f8fa6; text-transform: uppercase; }
        .table tbody td { padding: 20px; border-bottom: 1px solid #f1f2f6; }
        .search-box { background: #fff; border: 2px solid #f1f2f6; border-radius: 15px; padding: 12px 15px; transition: 0.3s; }
        .badge-modern { padding: 8px 16px; border-radius: 10px; font-weight: 600; font-size: 0.7rem; display: inline-block; min-width: 100px; text-align: center; }
        .bg-yudisium { background: #fff3e0; color: #ef6c00; }
        .bg-ujian { background: #e8f5e9; color: #2e7d32; }
        .bg-pengembalian { background: #e3f2fd; color: #1565c0; }
        .bg-unknown { background: #f1f2f6; color: #7f8fa6; }
        .btn-purple { background: var(--primary-purple); color: white; border-radius: 12px; padding: 10px 20px; font-weight: 600; border: none; }
        .btn-action-group .btn { width: 38px; height: 38px; border-radius: 12px; margin-right: 6px; display: inline-flex; align-items: center; justify-content: center; border: none; background: #f8faff; }
        .icon-shape { width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top mb-5">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center text-white" href="admin.php">
            <div class="bg-white rounded-circle p-1 me-2 shadow-sm">
                <img src="unpatti.jpg" width="35" height="35" class="rounded-circle"> 
            </div>
            SIAKAD FKIP ADMIN
        </a>
        <div class="ms-auto d-flex align-items-center text-white">
            <div class="me-3 d-none d-md-block text-end">
                <div class="small fw-bold">Hi, <?= $_SESSION['username']; ?></div>
                <span class="badge bg-white text-primary rounded-pill px-2" style="font-size: 0.6rem;">
                    <?= $is_patrick ? 'SUPER ADMIN' : 'USER AKSES'; ?>
                </span>
            </div>
            <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav ms-3">
        <?php if ($is_patrick): ?>
        <li class="nav-item">
            <a class="nav-link text-white fw-bold" href="operator.php"><i class="bi bi-file-earmark-text me-1"></i> Verifikasi Operator</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white-50" href="kelola_user.php"><i class="bi bi-people me-1"></i> Kelola User</a>
        </li>
        <?php endif; ?>
    </ul>
</div>
            <a href="logout.php" class="btn btn-light btn-sm rounded-pill px-4 fw-bold text-primary shadow-sm">Keluar</a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row mb-5">
        <div class="col-md-4 mb-3">
            <div class="stat-card">
                <div class="d-flex align-items-center">
                    <div class="icon-shape bg-info-light me-3 text-info"><i class="bi bi-people-fill"></i></div>
                    <div>
                        <div class="text-muted small fw-medium">Total Mahasiswa</div>
                        <div class="h3 fw-bold mb-0 text-primary"><?= $total_data; ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="stat-card">
                <div class="d-flex align-items-center">
                    <div class="icon-shape bg-success-light me-3 text-success"><i class="bi bi-check-circle-fill"></i></div>
                    <div>
                        <div class="text-muted small fw-medium">Sudah Validasi</div>
                        <div class="h3 fw-bold mb-0 text-success"><?= $res_valid['total']; ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="stat-card">
                <div class="d-flex align-items-center">
                    <div class="icon-shape bg-purple-light me-3 text-primary"><i class="bi bi-calendar3"></i></div>
                    <div>
                        <div class="text-muted small fw-medium">Sesi Aktif</div>
                        <div class="h4 fw-bold mb-0 text-dark"><?= date('M Y'); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4 align-items-center">
        <div class="col-lg-7">
            <form action="" method="GET" class="row g-2">
                <div class="col-md-8">
                    <input type="text" name="search" class="form-control search-box shadow-sm" placeholder="Cari Nama/NIM/Prodi..." value="<?= htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-4">
                    <select name="tipe" class="form-select search-box shadow-sm" onchange="this.form.submit()" <?= $is_restricted ? 'disabled' : ''; ?>>
                        <option value="">Semua Berkas</option>
                        <option value="Pengembalian" <?= $filter_tipe == 'Pengembalian' ? 'selected' : ''; ?>>Pengembalian</option>
                        <option value="Ujian" <?= $filter_tipe == 'Ujian' ? 'selected' : ''; ?>>Ujian</option>
                        <option value="Yudisium" <?= ($is_restricted || $filter_tipe == 'Yudisium') ? 'selected' : ''; ?>>Yudisium</option>
                    </select>
                </div>
            </form>
        </div>
        <div class="col-lg-5 text-lg-end mt-3 mt-lg-0">
            <a href="cetak_excel.php" class="btn btn-success btn-sm px-3 shadow-sm"><i class="bi bi-file-earmark-excel"></i> Excel</a>
            <a href="cetak_pdf.php" class="btn btn-danger btn-sm px-3 shadow-sm"><i class="bi bi-file-earmark-pdf"></i> PDF</a>
            <a href="tambah_laporan.php" class="btn btn-purple shadow-sm ms-2"><i class="bi bi-plus-circle-fill me-2"></i> Tambah</a>
            
        </div>
    </div>

    <div class="card card-main">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Status & Jenis</th>
                        <th>Mahasiswa & NIM</th>
                        <th>Program Studi</th>
                        <th class="text-center">Histori Cetak</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
    <?php if($total_data > 0): ?>
        <?php while($row = mysqli_fetch_array($query)): ?>
        <?php 
            $is_locked = (!empty($row['waktu_cetak']) && $row['waktu_cetak'] != '0000-00-00 00:00:00');
            $jenis_db = trim($row['jenis_laporan']);
            
            $cl = 'bg-unknown'; 
            if (stripos($jenis_db, 'Yudisium') !== false) { $cl = 'bg-yudisium'; }
            elseif (stripos($jenis_db, 'Pengembalian') !== false) { $cl = 'bg-pengembalian'; }
            elseif (stripos($jenis_db, 'Ujian') !== false || !empty($jenis_db)) { $cl = 'bg-ujian'; }
        ?>
        <tr>
            <td class="ps-4">
                <span class="badge-modern <?= $cl; ?>">
                    <?= !empty($jenis_db) ? strtoupper($jenis_db) : 'BELUM DIISI'; ?>
                </span>
            </td>
            <td>
                <div class="fw-bold text-dark mb-0"><?= htmlspecialchars($row['nama']) ?></div>
                <div class="text-muted small"><?= htmlspecialchars($row['nim']) ?></div>
            </td>
            <td><div class="small fw-medium"><?= htmlspecialchars($row['prodi']) ?></div></td>
            <td class="text-center">
                <?php if($is_locked): ?>
                    <div class="text-success small fw-bold"><i class="bi bi-patch-check-fill fs-5"></i><br><?= date('d/m/y', strtotime($row['waktu_cetak'])) ?></div>
                <?php else: ?>
                    <span class="text-muted opacity-50 small">--</span>
                <?php endif; ?>
            </td>
            <td class="text-end pe-4">
                <div class="btn-action-group">
                    <a href="lihat_laporan.php?id=<?= $row['id'] ?>" class="btn text-info" title="Detail"><i class="bi bi-zoom-in"></i></a>
                    
                    <?php if(stripos($jenis_db, 'Yudisium') !== false): ?>
                        <a href="cetak_kwitansi.php?id=<?= $row['id'] ?>" target="_blank" class="btn text-primary" title="Kwitansi"><i class="bi bi-receipt-cutoff"></i></a>
                    <?php endif; ?>

                    <?php if($is_locked): ?>
                        <button type="button" class="btn text-secondary" data-bs-toggle="modal" data-bs-target="#modalRiwayat<?= $row['id'] ?>" title="Riwayat Cetak">
                            <i class="bi bi-clock-history"></i>
                        </button>
                    <?php endif; ?>
                    
                    <?php if ($is_patrick || !$is_locked): ?>
                        <a href="edit_laporan.php?id=<?= $row['id'] ?>" class="btn text-warning" title="Edit"><i class="bi bi-pencil-square"></i></a>
                        <a href="hapus_laporan.php?id=<?= $row['id'] ?>" class="btn text-danger" onclick="return confirm('Hapus data ini?')" title="Hapus"><i class="bi bi-trash3-fill"></i></a>
                    <?php else: ?>
                        <button class="btn text-muted" title="Data Terkunci" disabled><i class="bi bi-lock-fill"></i></button>
                    <?php endif; ?>
                </div>

                <?php if ($is_patrick): ?>
                <div class="modal fade" id="modalRiwayat<?= $row['id'] ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-sm">
                        <div class="modal-content" style="border-radius: 20px; border: none;">
                            <div class="modal-header border-0 pb-0">
                                <h6 class="modal-title fw-bold"><i class="bi bi-info-circle me-2"></i>Riwayat Cetak</h6>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body text-start">
                                <div class="p-3 bg-light rounded-4">
                                    <div class="small text-muted mb-1">Dicetak Oleh:</div>
                                    <div class="fw-bold text-primary mb-3"><i class="bi bi-person-circle me-1"></i> <?= !empty($row['admin_cetak']) ? $row['admin_cetak'] : 'Tidak Terdeteksi'; ?></div>
                                    
                                    <div class="small text-muted mb-1">Waktu Cetak:</div>
                                    <div class="fw-bold"><i class="bi bi-calendar-event me-1"></i> <?= date('d M Y', strtotime($row['waktu_cetak'])) ?></div>
                                    <div class="small text-muted"><i class="bi bi-clock me-1"></i> Pukul <?= date('H:i', strtotime($row['waktu_cetak'])) ?> WIT</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                </td>
        </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="5" class="text-center py-5 text-muted">Belum ada data mahasiswa yang masuk.</td></tr>
    <?php endif; ?>
</tbody>
            </table>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>