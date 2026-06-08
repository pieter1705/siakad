<?php
// Koneksi ke database
include 'koneksi.php'; 

// Pastikan variabel di bawah ini sama dengan yang ada di koneksi.php
// Jika di koneksi.php menggunakan $koneksi, maka gunakan $koneksi di sini.
$db_conn = (isset($koneksi)) ? $koneksi : $db; 

if (isset($_POST['register'])) {
    $nim      = mysqli_real_escape_string($db_conn, trim($_POST['nim']));
    $username = mysqli_real_escape_string($db_conn, trim($_POST['username']));
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $prodi    = mysqli_real_escape_string($db_conn, $_POST['prodi']);
    $no_hp    = mysqli_real_escape_string($db_conn, $_POST['no_hp']);
    $role     = mysqli_real_escape_string($db_conn, $_POST['role']);

    // 1. Validasi NIM
    if (strlen($nim) !== 9 || !is_numeric($nim)) {
        echo "<script>alert('Gagal: NIM harus tepat 9 digit angka!'); window.history.back();</script>";
        exit;
    }

    // 2. Cek apakah NIM sudah ada
    $cek_nim = mysqli_query($db_conn, "SELECT nim FROM users WHERE nim = '$nim'");
    if (mysqli_num_rows($cek_nim) > 0) {
        echo "<script>alert('Gagal: NIM $nim sudah terdaftar!'); window.history.back();</script>";
        exit;
    }

    // 3. Proses Insert
    $query = "INSERT INTO users (nim, username, password, prodi, no_hp, role) 
              VALUES ('$nim', '$username', '$password', '$prodi', '$no_hp', '$role')";
    
    if (mysqli_query($db_conn, $query)) {
        echo "<script>alert('Registrasi Berhasil! Silakan Login.'); window.location.href = 'login.php';</script>";
        exit;
    } else {
        // Tampilkan error jika gagal insert
        die("Kesalahan Database: " . mysqli_error($db_conn));
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun FKIP - Sistem Akademik</title>
    <link rel="shortcut icon" href="unpatti.jpg" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-purple: #4a148c;
            --medium-purple: #7b1fa2;
            --light-purple: #f3e5f5;
            --accent-color: #ffd600;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #4a148c 0%, #1a237e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 40px 0;
        }

        .reg-card {
            background: rgba(255, 255, 255, 0.98);
            border: none;
            border-radius: 25px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
            overflow: hidden;
        }

        .card-header-custom {
            background: var(--primary-purple);
            color: white;
            padding: 35px;
            text-align: center;
            border-bottom: 5px solid var(--accent-color);
        }

        .card-header-custom img {
            width: 80px;
            background: white;
            padding: 5px;
            border-radius: 50%;
            margin-bottom: 15px;
        }

        .form-label {
            font-weight: 600;
            color: var(--primary-purple);
            font-size: 0.85rem;
        }

        .form-control, .form-select {
            border: 1.5px solid #e1e1e1;
            padding: 12px;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .input-group-text {
            background-color: var(--light-purple);
            border: 1.5px solid #e1e1e1;
            border-right: none;
            color: var(--primary-purple);
            width: 45px;
            justify-content: center;
        }

        .btn-register {
            background: var(--primary-purple);
            border: none;
            padding: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-radius: 12px;
            color: white;
            transition: all 0.3s;
        }

        .btn-register:hover {
            background: var(--medium-purple);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(74, 20, 140, 0.3);
        }

        .login-link {
            color: var(--primary-purple);
            text-decoration: none;
            font-weight: 700;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-8">
            <div class="card reg-card shadow-lg">
                <div class="card-header-custom text-center">
                    <img src="unpatti.jpg" alt="Logo Unpatti">
                    <h4 class="mb-1 fw-bold">REGISTRASI FKIP</h4>
                    <p class="small mb-0 opacity-75">Fakultas Keguruan dan Ilmu Pendidikan</p>
                </div>
                
                <div class="card-body p-4 p-md-5">
                    <form action="" method="POST">
                        
                        <div class="mb-3">
                            <label class="form-label text-uppercase small">NIM Mahasiswa</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-address-card"></i></span>
                                <input type="text" name="nim" class="form-control" placeholder="Contoh: 202301001" 
                                       required pattern="[0-9]{9}" maxlength="9" minlength="9">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-uppercase small">Nama Lengkap</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                                <input type="text" name="username" class="form-control" required placeholder="Nama Lengkap">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-uppercase small">Kata Sandi</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-key"></i></span>
                                <input type="password" name="password" class="form-control" required placeholder="Minimal 6 Karakter">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-uppercase small">Program Studi</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-university"></i></span>
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
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-uppercase small">No. WhatsApp</label>
                            <div class="input-group">
                                <span class="input-group-text">+62</span>
                                <input type="number" name="no_hp" class="form-control" placeholder="812xxxxxxx" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-uppercase small">Role / Hak Akses</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-user-shield"></i></span>
                                <select name="role" class="form-select" required>
                                    <option value="mahasiswa" selected>Mahasiswa</option>
                                    <!--<option value="admin">Admin (Bendahara)</option>-->
                                    <!--<option value="operator">Operator Prodi</option>-->
                                </select>
                            </div>
                        </div>

                        <button type="submit" name="register" class="btn btn-register w-100 mb-4 shadow">
                            <i class="fa-solid fa-paper-plane me-2"></i>Daftar Sekarang
                        </button>
                        
                        <div class="text-center">
                            <p class="small mb-0 text-muted">Sudah punya akun? <a href="login.php" class="login-link">MASUK DISINI</a></p>
                        </div>
                    </form>
                </div>
            </div>
            
            <p class="text-center text-white-50 mt-4 small">
                &copy; 2026 FKIP UNIVERSITAS PATTIMURA
            </p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>