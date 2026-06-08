<?php
session_start();
include 'koneksi.php';

// SET ZONA WAKTU KE WIT (Asia/Jayapura) agar sinkron dengan waktu Ambon
date_default_timezone_set('Asia/Jayapura');

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    die("Akses ditolak.");
}

// Mengambil nama admin dari session untuk Riwayat Cetak
$admin_cetak = isset($_SESSION['username']) ? $_SESSION['username'] : "Admin";
$bendahara = $admin_cetak; // Menggunakan nama admin yang login sebagai bendahara
$nip_bendahara = isset($_SESSION['nim']) ? $_SESSION['nim'] : "-"; 

// --- PERUBAHAN JABATAN MENJADI ADMIN KEUANGAN ---
$jabatan = "Admin Keuangan"; 
// ------------------------------------------------

if (!isset($_GET['id'])) { die("ID tidak ditemukan."); }
$id = mysqli_real_escape_string($koneksi, $_GET['id']);

// 1. Ambil data mahasiswa
$query = mysqli_query($koneksi, "SELECT * FROM laporan_mahasiswa WHERE id = '$id'");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    die("Data tidak ditemukan.");
}

/**
 * 2. LOGIKA HISTORY CETAK (DIPERBARUI)
 */
// Kita update setiap kali cetak agar admin_cetak terbaru tercatat
$waktu_sekarang = date('Y-m-d H:i:s');

mysqli_query($koneksi, "UPDATE laporan_mahasiswa SET 
    status_cetak = 'Sudah', 
    waktu_cetak = '$waktu_sekarang',
    admin_cetak = '$admin_cetak' 
    WHERE id = '$id'");

// Update data lokal agar tampilan di kertas sesuai dengan data baru
$data['waktu_cetak'] = $waktu_sekarang;
$data['admin_cetak'] = $admin_cetak;

// --- Fungsi Terbilang dan Tgl_indo tetap sama ---
function terbilang($angka) {
    $angka = abs($angka);
    $bilan = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];
    $temp = "";
    if ($angka < 12) { $temp = " " . $bilan[$angka]; } 
    else if ($angka < 20) { $temp = terbilang($angka - 10) . " Belas"; } 
    else if ($angka < 100) { $temp = terbilang($angka / 10) . " Puluh" . terbilang($angka % 10); } 
    else if ($angka < 200) { $temp = " Seratus" . terbilang($angka - 100); } 
    else if ($angka < 1000) { $temp = terbilang($angka / 100) . " Ratus" . terbilang($angka % 100); } 
    else if ($angka < 2000) { $temp = " Seribu" . terbilang($angka - 1000); } 
    else if ($angka < 1000000) { $temp = terbilang($angka / 1000) . " Ribu" . terbilang($angka % 1000); } 
    return $temp;
}

function tgl_indo($tanggal){
    if(empty($tanggal) || $tanggal == '0000-00-00 00:00:00' || $tanggal == NULL) return "-";
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $pecahkan = explode('-', date('Y-m-d', strtotime($tanggal)));
    return $pecahkan[2] . ' ' . $bulan[ (int)$pecahkan[1] ] . ' ' . $pecahkan[0];
}

$tahun = '2026';
$nomor_urut = sprintf("%03d", $data['id']);
$jenis = strtolower($data['jenis_laporan']);
$periode = isset($data['periode']) ? $data['periode'] : "-";

if ($jenis == 'yudisium') {
    $nomor_dokumen = "$nomor_urut/REG/PL/FKIP/$tahun";
    $untuk_pembayaran = "Biaya Penggelaran Lulusan FKIP Unpatti " . $periode;
} else {
    $nomor_dokumen = "$nomor_urut/REG/$tahun";
    $untuk_pembayaran = "Biaya " . htmlspecialchars($data['jenis_laporan']) . " FKIP Unpatti";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kwitansi_<?= $data['nim'] ?></title>
    <style>
        @media print {
            @page { size: 210mm 140mm; margin: 0; }
            body { margin: 0; padding: 0; }
            .box { border: none !important; box-shadow: none !important; margin: 0 !important; }
            .no-print { display: none; }
        }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 15px; background-color: #f5f5f5; margin: 20px; }
        .box { width: 190mm; height: 125mm; padding: 10mm; background: #fff; border: 1px dashed #999; margin: auto; display: flex; position: relative; box-sizing: border-box; }
        .side { width: 35px; background: #a0c4ff !important; border: 1px solid #777; margin-right: 15px; border-radius: 4px; -webkit-print-color-adjust: exact; }
        .main { flex-grow: 1; }
        .header { text-align: center; font-weight: 900; font-size: 22px; text-decoration: underline; margin-bottom: 25px; letter-spacing: 2px; }
        .row { display: flex; margin-bottom: 12px; z-index: 1; position: relative; align-items: flex-end; }
        .label { width: 170px; font-weight: bold; }
        .dots { border-bottom: 1px dotted #000; flex-grow: 1; font-weight: bold; font-size: 16px; }
        .amount { margin-top: 25px; padding: 10px 20px; border: 3px solid #000; font-weight: 900; font-size: 20px; display: inline-block; -webkit-print-color-adjust: exact; }
        .footer { float: right; text-align: center; margin-top: 5px; width: 280px; font-weight: bold; }
        .watermark { position: absolute; top: 50%; left: 55%; transform: translate(-50%, -50%); width: 280px; opacity: 0.08; z-index: 0; -webkit-print-color-adjust: exact; }
    </style>
</head>
<body onload="window.print()">
    <div class="box">
        <div class="side"></div>
        <img src="unpatti.jpg" class="watermark">
        <div class="main">
            <div class="header">KWITANSI</div>
            <div class="row">
                <div class="label">Nomor</div>
                <div class="dots" style="border:none;">: <b><?= $nomor_dokumen ?></b></div>
            </div>
            <div class="row">
                <div class="label">Sudah diterima dari</div>
                <div class="dots">: <?= htmlspecialchars($data['nama']) ?></div>
            </div>
            <div class="row">
                <div class="label">Terbilang</div>
                <div class="dots" style="background:rgba(238,238,238,0.3); font-style: italic; font-size: 14px;">
                    : ### <?= trim(terbilang($data['jumlah_pembayaran'])) ?> Rupiah ###
                </div>
            </div>
            <div class="row">
                <div class="label">Untuk pembayaran</div>
                <div class="dots">: <?= $untuk_pembayaran ?></div>
            </div>
            <div class="amount">
                Jumlah : Rp. <?= number_format($data['jumlah_pembayaran'], 0, ',', '.') ?>,-
            </div>
            <div class="footer">
                Ambon, <?= tgl_indo($data['waktu_cetak']) ?><br>
                <?= $jabatan ?>,<br><br><br><br><br>
                <u><b><?= htmlspecialchars($bendahara) ?></b></u><br>
                NIP. <?= htmlspecialchars($nip_bendahara) ?>
            </div>
        </div>
    </div>
</body>
</html>