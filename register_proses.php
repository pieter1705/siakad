<?php
include 'koneksi.php';

if (isset($_POST['register'])) {
    $nim      = mysqli_real_escape_string($koneksi, $_POST['nim']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Enkripsi password
    $no_hp    = mysqli_real_escape_string($koneksi, $_POST['no_hp']);
    $prodi    = mysqli_real_escape_string($koneksi, $_POST['prodi']);
    $role     = mysqli_real_escape_string($koneksi, $_POST['role']);

    // Cek apakah username sudah ada
    $cek_user = mysqli_query($koneksi, "SELECT * FROM users WHERE username = '$username'");
    if (mysqli_num_rows($cek_user) > 0) {
        echo "<script>alert('Username sudah digunakan!'); window.history.back();</script>";
        exit;
    }

    // Insert ke tabel users
    $query = "INSERT INTO users (nim, username, password, role, prodi, no_hp) 
              VALUES ('$nim', '$username', '$password', '$role', '$prodi', '$no_hp')";

    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Registrasi Berhasil! Silahkan Login.'); window.location='login.php';</script>";
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>