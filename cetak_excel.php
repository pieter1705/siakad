<?php
include 'koneksi.php';
session_start();

// Proteksi Admin
if(!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// 1. Identifikasi User Terbatas (Jelien & Christian)
$username_session = strtolower(trim($_SESSION['username']));
$is_restricted = (
    $username_session == 'jelien' || 
    $username_session == 'jelien b. noya, sh' || 
    $username_session == 'jelie' || 
    strpos($username_session, 'christian') !== false
);

// 2. Ambil data filter
$search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';
$prodi  = isset($_GET['prodi']) ? mysqli_real_escape_string($koneksi, $_GET['prodi']) : '';

// 3. Query untuk mengambil data
$query_str = "SELECT * FROM laporan_mahasiswa WHERE 1=1";

// LOGIKA PEMBATASAN: Jika user terbatas, paksa hanya jenis Yudisium
if ($is_restricted) {
    $query_str .= " AND jenis_laporan LIKE '%Yudisium%'";
}

if ($search != '') { 
    $query_str .= " AND (nama LIKE '%$search%' OR nim LIKE '%$search%')"; 
}
if ($prodi != '') { 
    $query_str .= " AND prodi = '$prodi'"; 
}

$query_str .= " ORDER BY id DESC";
$laporan = mysqli_query($koneksi, $query_str);

// 4. Update Status Cetak di Database
$waktu_sekarang = date('Y-m-d H:i:s');
$data_cetak = []; 

if(mysqli_num_rows($laporan) > 0) {
    while($row = mysqli_fetch_assoc($laporan)) {
        $id_row = $row['id'];
        
        // HANYA Update jika status belum 'Sudah'
        if ($row['status_cetak'] !== 'Sudah' || empty($row['waktu_cetak'])) {
            mysqli_query($koneksi, "UPDATE laporan_mahasiswa SET 
                status_cetak = 'Sudah', 
                waktu_cetak = '$waktu_sekarang' 
                WHERE id = '$id_row'");
            
            $row['status_cetak'] = 'Sudah';
            $row['waktu_cetak'] = $waktu_sekarang;
        }
        $data_cetak[] = $row;
    }
}

// 5. Header Excel
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Rekap_Laporan_FKIP_".date('d-m-Y').".xls");
header("Pragma: no-cache");
header("Expires: 0");
?>

<style>
    .str { mso-number-format:\@; } 
    .num { mso-number-format:"\#\,\#\#0"; } 
    .header-table { background-color: #4e73df; color: #ffffff; font-weight: bold; text-align: center; border: 1px solid #000; }
    td { border: 1px solid #000; padding: 5px; }
</style>

<table>
    <tr><td colspan="9" style="font-size: 16pt; font-weight: bold; text-align: center;">UNIVERSITAS PATTIMURA</td></tr>
    <tr><td colspan="9" style="text-align: center; font-weight: bold;">FAKULTAS KEGURUAN DAN ILMU PENDIDIKAN</td></tr>
    <tr><td colspan="9" style="text-align: left;">Waktu Download: <?= date('d-m-Y H:i') ?></td></tr>
    <tr><td colspan="9" style="text-align: left;">Petugas: <?= $_SESSION['username'] ?> <?= $is_restricted ? '(Yudisium)' : '' ?></td></tr>
    <tr><td colspan="9"></td></tr> 
</table>

<table border="1">
    <thead>
        <tr>
            <th class="header-table">No</th>
            <th class="header-table">NIM</th>
            <th class="header-table">Nama Lengkap</th>
            <th class="header-table">Program Studi</th>
            <th class="header-table">Semester</th>
            <th class="header-table">Jalur Masuk</th>
            <th class="header-table">Jenis Laporan</th>
            <th class="header-table">Jumlah Pembayaran</th>
            <th class="header-table" style="background-color: #27ae60;">Status Cetak</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        $total_bayar = 0;
        if(!empty($data_cetak)):
            foreach($data_cetak as $row):
                $total_bayar += $row['jumlah_pembayaran'];
        ?>
        <tr>
            <td style="text-align: center;"><?= $no++; ?></td>
            <td class="str"><?= $row['nim'] ?></td>
            <td><?= htmlspecialchars(strtoupper($row['nama'])) ?></td>
            <td><?= htmlspecialchars($row['prodi']) ?></td>
            <td style="text-align: center;"><?= $row['semester'] ?></td>
            <td style="text-align: center;"><?= $row['jalur_masuk'] ?></td>
            <td style="text-align: center;"><?= $row['jenis_laporan'] ?></td>
            <td class="num" style="text-align: right;"><?= $row['jumlah_pembayaran'] ?></td>
            <td style="text-align: center; font-style: italic;">
                Dicetak pada: <?= $row['waktu_cetak'] ?>
            </td>
        </tr>
        <?php 
            endforeach; 
        else:
        ?>
        <tr><td colspan="9" style="text-align: center;">Tidak ada data yudisium ditemukan.</td></tr>
        <?php endif; ?>
    </tbody>
    <tfoot>
        <tr style="background-color: #f2f2f2; font-weight: bold;">
            <td colspan="7" style="text-align: right;">TOTAL :</td>
            <td class="num" style="text-align: right;"><?= $total_bayar ?></td>
            <td></td>
        </tr>
    </tfoot>
</table>