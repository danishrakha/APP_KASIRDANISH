<?php
session_start();
include 'koneksi.php';

// Cek login
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Proses tambah admin
if (isset($_POST['tambah_admin'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $nama_lengkap = $_POST['nama_lengkap'];
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert admin baru
    $insert_query = "INSERT INTO kasir_admin (Username, Password, NamaLengkap, Status) 
                    VALUES ('$username', '$hashed_password', '$nama_lengkap', 'active')";
    
    if (mysqli_query($conn, $insert_query)) {
        header("Location: kelola_admin.php");
        exit();
    } else {
        $error = "Gagal menambah admin: " . mysqli_error($conn);
    }
}

// Proses hapus admin
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Cegah hapus diri sendiri
    if ($id != $_SESSION['admin_id']) {
        $delete_query = "DELETE FROM kasir_admin WHERE AdminID = '$id'";
        
        if (mysqli_query($conn, $delete_query)) {
            header("Location: kelola_admin.php");
            exit();
        } else {
            $error = "Gagal menghapus admin: " . mysqli_error($conn);
        }
    } else {
        $error = "Anda tidak dapat menghapus akun Anda sendiri!";
    }
}

// Ambil data admin
 $query = "SELECT * FROM kasir_admin";
 $result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
        }
        .sidebar .nav-link:hover {
            color: white;
        }
        .sidebar .nav-link.active {
            color: white;
            background-color: #0d6efd;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 p-0 sidebar">
                <div class="d-flex flex-column p-3 text-white">
                    <h4 class="mb-4">POS Admin</h4>
                    <ul class="nav nav-pills flex-column mb-auto">
                        <li class="nav-item">
                            <a href="dashboard.php" class="nav-link">
                                <i class="bi bi-speedometer2 me-2"></i> Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="index.php" class="nav-link">
                                <i class="bi bi-cart me-2"></i> Kasir
                            </a>
                        </li>
                        <li>
                            <a href="stok.php" class="nav-link">
                                <i class="bi bi-box-seam me-2"></i> Stok
                            </a>
                        </li>
                        <li>
                            <a href="pelanggan.php" class="nav-link">
                                <i class="bi bi-people me-2"></i> Pelanggan
                            </a>
                        </li>
                        <li>
                            <a href="laporan.php" class="nav-link">
                                <i class="bi bi-file-earmark-text me-2"></i> Laporan
                            </a>
                        </li>
                        <li>
                            <a href="kelola_admin.php" class="nav-link active">
                                <i class="bi bi-person-gear me-2"></i> Kelola Admin
                            </a>
                        </li>
                    </ul>
                    <hr>
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle me-2"></i>
                            <strong><?php echo $_SESSION['admin_name']; ?></strong>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
                            <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 p-4">
                <h2 class="mb-4">Kelola Admin</h2>
                
                <?php if (isset($error)) { ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php } ?>
                
                <div class="row">
                    <div class="col-md-5">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Tambah Admin Baru</h5>
                            </div>
                            <div class="card-body">
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
                                    </div>
                                    <button type="submit" class="btn btn-primary" name="tambah_admin">Tambah Admin</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Daftar Admin</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Nama</th>
                                                <th>Username</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($admin = mysqli_fetch_assoc($result)) { ?>
                                                <tr>
                                                    <td><?php echo $admin['AdminID']; ?></td>
                                                    <td><?php echo $admin['NamaLengkap']; ?></td>
                                                    <td><?php echo $admin['Username']; ?></td>
                                                    <td>
                                                        <?php if ($admin['Status'] == 'active') { ?>
                                                            <span class="badge bg-success">Aktif</span>
                                                        <?php } else { ?>
                                                            <span class="badge bg-warning">Tidak Aktif</span>
                                                        <?php } ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($admin['AdminID'] != $_SESSION['admin_id']) { ?>
                                                            <a href="kelola_admin.php?delete=<?php echo $admin['AdminID']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus admin ini?')">
                                                                <i class="bi bi-trash"></i>
                                                            </a>
                                                        <?php } else { ?>
                                                            <span class="text-muted">Anda</span>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>