<?php
session_start();
include 'koneksi.php';

if (isset($_GET['code'])) {
    $activation_code = $_GET['code'];
    
    // Cek kode aktivasi
    $query = "SELECT * FROM kasir_admin WHERE ActivationCode = '$activation_code' AND Status = 'inactive'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) == 1) {
        // Aktifkan akun
        $update_query = "UPDATE kasir_admin SET Status = 'active', ActivationCode = NULL WHERE ActivationCode = '$activation_code'";
        
        if (mysqli_query($conn, $update_query)) {
            $success = "Akun Anda telah berhasil diaktifkan! Silakan login.";
        } else {
            $error = "Aktivasi gagal: " . mysqli_error($conn);
        }
    } else {
        $error = "Kode aktivasi tidak valid atau akun sudah aktif.";
    }
} else {
    $error = "Kode aktivasi tidak ditemukan.";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aktivasi Akun</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .activation-container {
            max-width: 400px;
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
    </style>
</head>
<body>
    <div class="activation-container">
        <div class="card">
            <div class="card-header py-3">
                <h4 class="mb-0">Aktivasi Akun</h4>
            </div>
            <div class="card-body text-center">
                <?php if (isset($error)) { ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php } ?>
                <?php if (isset($success)) { ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                    <a href="login.php" class="btn btn-primary mt-3">Login</a>
                <?php } ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>