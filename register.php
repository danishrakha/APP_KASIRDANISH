<?php
session_start();
include 'koneksi.php';

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit();
}

// Proses register
if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $nama_lengkap = $_POST['nama_lengkap'];
    
    // Validasi password
    if ($password != $confirm_password) {
        $error = "Password tidak cocok!";
    } else {
        // Cek username sudah ada atau belum
        $query = "SELECT * FROM kasir_admin WHERE Username = '$username'";
        $result = mysqli_query($conn, $query);
        
        if (mysqli_num_rows($result) > 0) {
            $error = "Username sudah digunakan!";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Generate activation code
            $activation_code = md5(uniqid(rand(), true));
            
            // Insert admin baru
            $insert_query = "INSERT INTO kasir_admin (Username, Password, NamaLengkap, Status, ActivationCode) 
                            VALUES ('$username', '$hashed_password', '$nama_lengkap', 'inactive', '$activation_code')";
            
            if (mysqli_query($conn, $insert_query)) {
                // Kirim email aktivasi (simulasi)
                $success = "Registrasi berhasil! Silakan hubungi admin untuk aktivasi akun Anda.";
                // Dalam implementasi nyata, Anda bisa mengirim email dengan link aktivasi
                // misal: http://localhost/nama_folder/activate.php?code=$activation_code
            } else {
                $error = "Registrasi gagal: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .register-container {
            max-width: 500px;
            width: 100%;
            padding: 15px;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #0d6efd;
            color: white;
            text-align: center;
            border-radius: 10px 10px 0 0 !important;
        }
        .password-strength {
            height: 5px;
            margin-top: 5px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="card">
            <div class="card-header py-3">
                <h4 class="mb-0">Register Admin</h4>
            </div>
            <div class="card-body">
                <?php if (isset($error)) { ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php } ?>
                <?php if (isset($success)) { ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php } ?>
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" required>
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <div class="password-strength bg-danger" id="password-strength"></div>
                        <small class="text-muted">Minimal 8 karakter dengan kombinasi huruf dan angka</small>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Konfirmasi Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary" name="register">Register</button>
                    </div>
                </form>
                <div class="text-center mt-3">
                    <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password strength checker
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('password-strength');
            
            let strength = 0;
            
            // Check password length
            if (password.length >= 8) {
                strength += 1;
            }
            
            // Check for letters
            if (/[a-zA-Z]/.test(password)) {
                strength += 1;
            }
            
            // Check for numbers
            if (/[0-9]/.test(password)) {
                strength += 1;
            }
            
            // Check for special characters
            if (/[^a-zA-Z0-9]/.test(password)) {
                strength += 1;
            }
            
            // Update strength bar
            switch(strength) {
                case 0:
                case 1:
                    strengthBar.className = 'password-strength bg-danger';
                    break;
                case 2:
                    strengthBar.className = 'password-strength bg-warning';
                    break;
                case 3:
                    strengthBar.className = 'password-strength bg-info';
                    break;
                case 4:
                    strengthBar.className = 'password-strength bg-success';
                    break;
            }
        });
    </script>
</body>
</html>