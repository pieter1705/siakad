<?php
session_start();
include 'koneksi.php'; // Pastikan variabel koneksi Anda sesuai, misal: $db atau $koneksi

if (isset($_POST['login'])) {
    $user_input = mysqli_real_escape_string($koneksi, $_POST['username']);
    $pass_input = $_POST['password'];

    $stmt = $koneksi->prepare("SELECT id, nim, username, password, role, prodi FROM users WHERE username = ? OR nim = ?");
    $stmt->bind_param("ss", $user_input, $user_input);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        
        if (password_verify($pass_input, $row['password'])) {
            $_SESSION['login'] = true;
            $_SESSION['id_user'] = $row['id'];
            $_SESSION['role'] = trim($row['role']); 
            $_SESSION['username'] = $row['username'];
            $_SESSION['nim'] = $row['nim'];
            $_SESSION['prodi'] = $row['prodi']; 

            if ($row['role'] == 'admin') {
                header("Location: admin.php");
                exit;
            } elseif ($row['role'] == 'operator') {
                header("Location: operator.php");
                exit;
            } else {
                header("Location: index.php");
                exit;
            }
        }
    }
    $error = true;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Akademik FKIP</title>
    <link rel="shortcut icon" href="unpatti.jpg" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-purple: #4a148c;
            --medium-purple: #7b1fa2;
            --light-purple: #f3e5f5;
            --accent-gold: #ffd600;
        }

        body { 
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #4a148c 0%, #1a237e 100%);
            min-height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center;
        }

        .login-card { 
            border: none; 
            border-radius: 25px; 
            box-shadow: 0 20px 40px rgba(0,0,0,0.4); 
            background: rgba(255, 255, 255, 0.98);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
        }

        .login-header {
            background: var(--primary-purple);
            color: white;
            padding: 30px;
            text-align: center;
            border-bottom: 5px solid var(--accent-gold);
        }

        .login-header img {
            width: 70px;
            background: white;
            padding: 5px;
            border-radius: 50%;
            margin-bottom: 10px;
        }

        .form-control {
            border-radius: 12px;
            padding: 12px 15px 12px 45px;
            border: 1.5px solid #e1e1e1;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(123, 31, 162, 0.2);
            border-color: var(--medium-purple);
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
            color: var(--primary-purple);
            font-size: 1.1rem;
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
            cursor: pointer;
            color: #aaa;
            transition: 0.3s;
        }

        .toggle-password:hover {
            color: var(--medium-purple);
        }

        .btn-login {
            background: var(--primary-purple);
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-weight: 600;
            letter-spacing: 1px;
            color: white;
            transition: all 0.3s;
        }

        .btn-login:hover {
            background: var(--medium-purple);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(74, 20, 140, 0.3);
            color: white;
        }

        .register-link {
            color: var(--primary-purple);
            text-decoration: none;
            font-weight: 700;
        }

        .register-link:hover {
            color: var(--medium-purple);
            text-decoration: underline;
        }

        .alert-custom {
            border-radius: 10px;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>

<div class="container d-flex justify-content-center">
    <div class="card login-card shadow-lg">
        <div class="login-header">
            <img src="unpatti.jpg" alt="Logo Unpatti">
            <h4 class="mb-0 fw-bold">Selamat Datang</h4>
            <p class="small mb-0 opacity-75">Portal Akademik FKIP Unpatti</p>
        </div>

        <div class="card-body p-4 p-md-5">
            <?php if(isset($error)): ?>
                <div class="alert alert-danger alert-custom py-2 text-center mb-4">
                    <i class="fas fa-exclamation-circle me-2"></i> NIM/Username atau Password salah!
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="mb-3 position-relative">
                    <span class="input-icon"><i class="fas fa-user-graduate"></i></span>
                    <input type="text" name="username" class="form-control" placeholder="Username / NIM" required>
                </div>
                
                <div class="mb-3 position-relative">
                    <span class="input-icon"><i class="fas fa-key"></i></span>
                    <input type="password" name="password" id="passwordField" class="form-control" placeholder="Password" required>
                    <span class="toggle-password" onclick="togglePassword()">
                        <i class="fas fa-eye" id="eyeIcon"></i>
                    </span>
                </div>

                <div class="text-end mb-4">
                    <a href="lupa_password.php" class="text-muted small text-decoration-none">Lupa Password?</a>
                </div>
                
                <button type="submit" name="login" class="btn btn-login w-100 mb-3">
                    MASUK SEKARANG
                </button>
                
                <div class="text-center mt-3">
                    <p class="mb-0 text-muted small">Belum memiliki akun?</p>
                    <a href="register.php" class="register-link small text-uppercase">Buat Akun Baru</a>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="position-absolute bottom-0 mb-3">
    <p class="text-center text-white-50 small">&copy; 2026 Sistem Akademik FKIP Unpatti</p>
</div>

<script>
    function togglePassword() {
        const passwordField = document.getElementById('passwordField');
        const eyeIcon = document.getElementById('eyeIcon');
        
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = 'password';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>