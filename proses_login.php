<?php
session_start();
include 'config.php';

if (isset($_POST['login'])) {
    // Mengambil input dan mencegah SQL Injection sederhana
    $user_input = mysqli_real_escape_string($conn, $_POST['username']);
    $pass_input = $_POST['password'];

    // Mencari user berdasarkan username ATAU nim sesuai struktur tabel Anda
    $query = "SELECT * FROM users WHERE username = '$user_input' OR nim = '$user_input'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        
        // Verifikasi password hash
        if (password_verify($pass_input, $row['password'])) {
            // Set session data
            $_SESSION['login'] = true;
            $_SESSION['id_user'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['nim'] = $row['nim'];

            // Redirect berdasarkan role
            if ($row['role'] == 'admin') {
                header("Location: admin.php");
            } else {
                header("Location: index.php");
            }
            exit;
        } else {
            // Password salah
            header("Location: login.php?pesan=gagal");
            exit;
        }
    } else {
        // User tidak ditemukan
        header("Location: login.php?pesan=gagal");
        exit;
    }
} else {
    // Jika diakses tanpa submit form
    header("Location: login.php");
    exit;
}
?>