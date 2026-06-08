<?php
include 'koneksi.php';
session_start();

// Proteksi Admin
if(!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Ambil data laporan
$laporan = mysqli_query($koneksi, "SELECT * FROM laporan_mahasiswa ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap_Laporan_Mahasiswa_<?= date('dmy') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { 
            font-family: 'Times New Roman', Times, serif; 
            background-color: #f4f7f6;
        }
        /* Kanvas Dokumen agar terlihat seperti kertas di layar */
        .paper {
            background: white;
            width: 210mm; /* Standar A4 */
            margin: 30px auto;
            padding: 20px 40px;
            box-shadow: 0 0 15px rgba(0,0,0,0.2);
            min-height: 297mm;
        }
        .kop-surat { 
            border-bottom: 4px double #000; 
            padding-bottom: 10px; 
            margin-bottom: 20px; 
        }
        .table thead { 
            background-color: #4e73df !important; 
            color: white !important; 
            -webkit-print-color-adjust: exact; 
        }
        @media print {
            .no-print { display: none !important; }
            body { background-color: white; }
            .paper { 
                width: 100%; 
                margin: 0; 
                box-shadow: none; 
                padding: 0;
            }
            @page { margin: 1.5cm; }
        }
    </style>
</head>
<body>

    <div class="no-print sticky-top bg-dark p-3 d-flex justify-content-center shadow">
        <a href="admin.php" class="btn btn-outline-light me-3">
            <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
        </a>
        <button onclick="window.print()" class="btn btn-danger px-4">
            <i class="bi bi-file-earmark-pdf-fill"></i> DOWNLOAD / CETAK PDF
        </button>
    </div>

    <div class="paper">
        <div class="kop-surat d-flex align-items-center">
            <div style="width: 80px;" class="me-3 text-center">
                <img src="assets/unpatti.jpg" style="width: 80px;">
            </div>
            <div class="text-center flex-grow-1">
                <h4 class="mb-0 fw-bold">UNIVERSITAS PATTIMURA</h4>
                <h5 class="mb-0">FAKULTAS KEGURUAN DAN ILMU PENDIDIKAN</h5>
                <p class="mb-0 small italic">Jl. Ir. M. Putuhena, Kampus Poka, Ambon, Maluku</p>
                <h6 class="mt-2 fw-bold text-decoration-underline">REKAPITULASI LAPORAN AKADEMIK MAHASISWA</h6>
            </div>
        </div>

        <div class="d-flex justify-content-between mb-3 mt-4">
            <small>Dicetak oleh: <strong><?= $_SESSION['username'] ?></strong></small>
            <small>Tanggal: <?= date('d/m/Y H:i') ?></small>
        </div>

        <table class="table table-bordered table-striped align-middle" style="font-size: 11px;">
            <thead class="table-primary text-center">
                <tr>
                    <th>No</th>
                    <th>NIM</th>
                    <th>Nama Mahasiswa</th>
                    <th>Prodi</th>
                    <th>Sem.</th>
                    <th>Jenis Laporan</th>
                    <th>Nominal</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                $total_uang = 0;
                while($row = mysqli_fetch_assoc($laporan)):
                    $total_uang += $row['jumlah_pembayaran'];
                ?>
                <tr>
                    <td class="text-center"><?= $no++; ?></td>
                    <td class="text-center"><?= $row['nim'] ?></td>
                    <td><?= strtoupper($row['nama']) ?></td>
                    <td><?= $row['prodi'] ?></td>
                    <td class="text-center"><?= $row['semester'] ?></td>
                    <td class="text-center"><?= $row['jenis_laporan'] ?></td>
                    <td class="text-end">Rp <?= number_format($row['jumlah_pembayaran'], 0, ',', '.') ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
            <tfoot>
                <tr class="fw-bold">
                    <td colspan="6" class="text-end bg-light">TOTAL KESELURUHAN</td>
                    <td class="text-end bg-light text-primary">Rp <?= number_format($total_uang, 0, ',', '.') ?></td>
                </tr>
            </tfoot>
        </table>

        <div class="mt-5 d-flex justify-content-end">
            <div class="text-center" style="width: 250px;">
                <p class="mb-0">Ambon, <?= date('d F Y') ?></p>
                <p class="mb-5">Admin Akademik FKIP,</p>
                <br><br>
                <p class="fw-bold text-decoration-underline mb-0"><?= strtoupper($_SESSION['username']) ?></p>
                <p class="small text-muted">NIP.<?= strtoupper($_SESSION['nim']) ?></p>
            </div>
        </div>
    </div>

    <script>
        // window.print(); // Aktifkan jika ingin otomatis terbuka dialog cetaknya
    </script>
</body>
</html>