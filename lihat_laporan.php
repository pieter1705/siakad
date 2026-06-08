<?php
session_start(); 
include 'koneksi.php';

// Proteksi: Pastikan user sudah login
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

// Tentukan link kembali berdasarkan role
$role = $_SESSION['role'];
if ($role == 'admin') {
    $back_link = 'admin.php';
} elseif ($role == 'operator') {
    $back_link = 'operator_prodi.php';
} else {
    $back_link = 'index.php';
}

if (!isset($_GET['id'])) {
    die("ID Laporan tidak ditemukan.");
}

$id = mysqli_real_escape_string($koneksi, $_GET['id']);

// QUERY: Mengambil data laporan
$query = mysqli_query($koneksi, "SELECT * FROM laporan_mahasiswa WHERE id = '$id'");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    die("Data tidak ditemukan di database.");
}

// Fungsi Format Tanggal Indonesia
function tgl_indo($tanggal){
    if(empty($tanggal) || $tanggal == '0000-00-00' || $tanggal == '0000-00-00 00:00:00') return "-";
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $pecahkan = explode('-', date('Y-m-d', strtotime($tanggal)));
    return $pecahkan[2] . ' ' . $bulan[ (int)$pecahkan[1] ] . ' ' . $pecahkan[0];
}

$jenis_laporan = strtolower($data['jenis_laporan']);
// Cek apakah ini kategori yudisium
$is_yudisium = (strpos($jenis_laporan, 'yudisium') !== false);
// Cek apakah ini kategori yang memiliki pembayaran manual/tambahan
$is_pembayaran_manual = ($is_yudisium || strpos($jenis_laporan, 'ujian') !== false);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Laporan - <?= htmlspecialchars($data['nim']) ?></title>
    <link rel="shortcut icon" href="unpatti.jpg" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap');
        body { background-color: #f0f2f5; font-family: 'Inter', sans-serif; color: #334155; }
        .document-canvas {
            background-color: white;
            background-image: url('unpatti.jpg');
            background-repeat: no-repeat;
            background-position: center center;
            background-size: 400px;
            max-width: 850px;
            margin: 40px auto;
            padding: 60px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            border-radius: 12px;
            position: relative;
            overflow: hidden;
        }
        .document-canvas::after {
            content: "";
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(255, 255, 255, 0.85); 
            z-index: 0;
        }
        .kop-surat, .modern-table, .attachment-card, .row, .text-center { position: relative; z-index: 1; }
        .kop-surat { border-bottom: 2px solid #e2e8f0; padding-bottom: 25px; margin-bottom: 40px; }
        .modern-table { width: 100%; border-collapse: separate; border-spacing: 0; border: 1px solid #f1f5f9; border-radius: 12px; overflow: hidden; background-color: rgba(255, 255, 255, 0.5); }
        .modern-table td { padding: 16px 20px; border-bottom: 1px solid #f1f5f9; }
        .label-cell { width: 35%; font-weight: 600; color: #64748b; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; }
        .value-cell { font-weight: 500; color: #1e293b; }
        .bi-icon-label { margin-right: 10px; color: #6366f1; font-size: 1.1rem; }
        .attachment-card { border: 2px dashed #cbd5e1; border-radius: 16px; padding: 30px; background: rgba(248, 250, 252, 0.6); margin-top: 50px; }
        .img-preview { max-width: 100%; height: auto; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; }
        .action-bar { position: fixed; bottom: 30px; right: 30px; z-index: 1000; }
        
        @media print {
            body { background: white; }
            .document-canvas { margin: 0; padding: 30px; box-shadow: none; width: 100%; max-width: 100%; }
            .no-print { display: none !important; }
            .action-bar { display: none; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="action-bar no-print">
        <a href="<?= $back_link ?>" class="btn btn-secondary shadow me-2 rounded-pill px-4">
            <i class="bi bi-arrow-left me-2"></i> Kembali
        </a>
        <button onclick="window.print()" class="btn btn-primary shadow px-4 rounded-pill">
            <i class="bi bi-printer me-2"></i> Cetak Dokumen
        </button>
    </div>

    <div class="document-canvas">
        <div class="kop-surat d-flex align-items-center">
            <div class="me-4 text-center">
                <img src="unpatti.jpg" alt="Logo" width="70" height="70">
            </div>
            <div class="flex-grow-1 text-center">
                <h3 class="mb-0 fw-bold" style="color: #1e293b; letter-spacing: -0.5px;">UNIVERSITAS PATTIMURA</h3>
                <h5 class="mb-1 text-secondary fw-normal">Fakultas Keguruan dan Ilmu Pendidikan (FKIP)</h5>
                <p class="mb-0 small text-muted">Jl. Ir. M. Putuhena, Kampus Poka, Ambon, Maluku</p>
            </div>
        </div>

        <div class="text-center mb-5">
            <h4 class="fw-bold mb-1" style="color: #1e293b;">SURAT PERNYATAAN PENGAJUAN</h4>
            <span class="badge bg-primary rounded-pill mb-2 px-3 text-uppercase"><?= htmlspecialchars($data['jenis_laporan']) ?></span>
            
            <?php 
            $tahun = date('Y', strtotime($data['tanggal_input'])); 
            $nomor_urut = sprintf("%03d", $data['id']); 
            
            if ($is_yudisium) {
                $nomor_dokumen = "$nomor_urut/REG/PL/FKIP/$tahun";
            } elseif (strpos($jenis_laporan, 'ujian') !== false) {
                $nomor_dokumen = "$nomor_urut/REG/US/FKIP/$tahun";
            } else {
                $nomor_dokumen = "$nomor_urut/REG/FKIP/$tahun";
            }
            ?>
            <p class="text-muted small">Nomor Dokumen: <span class="fw-bold text-dark"><?= $nomor_dokumen ?></span></p> 
        </div>

        <table class="modern-table">
            <tr>
                <td class="label-cell"><i class="bi bi-person bi-icon-label"></i>Nama Lengkap</td>
                <td class="value-cell text-uppercase"><?= htmlspecialchars($data['nama']) ?></td>
            </tr>
            <tr>
                <td class="label-cell"><i class="bi bi-card-text bi-icon-label"></i>NIM</td>
                <td class="value-cell fw-bold text-primary"><?= htmlspecialchars($data['nim']) ?></td>
            </tr>
            <tr>
                <td class="label-cell"><i class="bi bi-mortarboard bi-icon-label"></i>Program Studi</td>
                <td class="value-cell"><?= htmlspecialchars($data['prodi']) ?></td>
            </tr>

            <?php if (!empty($data['periode'])): ?>
            <tr>
                <td class="label-cell"><i class="bi bi-calendar-event bi-icon-label"></i>Periode</td>
                <td class="value-cell fw-bold"><?= htmlspecialchars($data['periode']) ?></td>
            </tr>
            <?php endif; ?>

            <?php if (!$is_yudisium): ?>
            <tr>
                <td class="label-cell"><i class="bi bi-calendar3 bi-icon-label"></i>Semester</td>
                <td class="value-cell">Semester <?= htmlspecialchars($data['semester']) ?></td>
            </tr>
            <?php endif; ?>

            <tr>
                <td class="label-cell"><i class="bi bi-signpost-split bi-icon-label"></i>Jalur Masuk</td>
                <td class="value-cell"><?= htmlspecialchars($data['jalur_masuk']) ?></td>
            </tr>

            <?php if (!empty($data['tanggal_pembayaran']) && $data['tanggal_pembayaran'] != '0000-00-00'): ?>
            <tr>
                <td class="label-cell"><i class="bi bi-calendar-check bi-icon-label"></i>Tanggal Pembayaran</td>
                <td class="value-cell fw-bold text-dark"><?= tgl_indo($data['tanggal_pembayaran']) ?></td>
            </tr>
            <?php endif; ?>
            
            <?php if ($is_pembayaran_manual && $data['jumlah_pembayaran'] > 0): ?>
            <tr style="background-color: rgba(99, 102, 241, 0.05) !important;">
                <td class="label-cell" style="color: #4f46e5;"><i class="bi bi-cash-coin bi-icon-label" style="color: #4f46e5;"></i>Total Bayar</td>
                <td class="value-cell fs-5 fw-bold text-primary">
                    Rp <?= number_format($data['jumlah_pembayaran'], 0, ',', '.') ?>
                </td>
            </tr>
            <?php endif; ?>
        </table>

        <?php if (!$is_yudisium): ?>
        <div class="attachment-card text-center">
            <h6 class="text-secondary mb-4 text-uppercase fw-bold">
                <i class="bi bi-paperclip me-2"></i>Lampiran Berkas / Bukti Pembayaran
            </h6>
            <?php if(!empty($data['slip'])): ?>
                <img src="uploads/<?= htmlspecialchars($data['slip']) ?>" class="img-preview" alt="Lampiran">
            <?php else: ?>
                <p class="text-muted italic small">Tidak ada lampiran file.</p>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="row mt-5 pt-4">
            <div class="col-7"></div>
            <div class="col-5 text-center">
                <p class="mb-5 fw-medium">Ambon, <?= tgl_indo($data['tanggal_input']) ?></p>
                <div style="height: 60px;"></div>
                <p class="fw-bold mb-0 text-decoration-underline text-uppercase"><?= htmlspecialchars($data['nama']) ?></p>
                <p class="small text-muted">NIM. <?= htmlspecialchars($data['nim']) ?></p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>