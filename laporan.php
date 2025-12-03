<?php
session_start();
include 'koneksi.php';

// Cek login
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Filter berdasarkan tanggal
 $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
 $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Query untuk laporan penjualan
 $query = "SELECT p.PenjualanID, p.TanggalPenjualan, p.TotalHarga, pl.NamaPelanggan 
          FROM kasir_penjualan p
          LEFT JOIN kasir_pelanggan pl ON p.PelangganID = pl.PelangganID
          WHERE DATE(p.TanggalPenjualan) BETWEEN '$start_date' AND '$end_date'
          ORDER BY p.TanggalPenjualan DESC";
 $result = mysqli_query($conn, $query);

// Hitung total penjualan
 $query_total = "SELECT SUM(TotalHarga) as total FROM kasir_penjualan 
                WHERE DATE(TanggalPenjualan) BETWEEN '$start_date' AND '$end_date'";
 $result_total = mysqli_query($conn, $query_total);
 $total_data = mysqli_fetch_assoc($result_total);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan</title>
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
                            <a href="laporan.php" class="nav-link active">
                                <i class="bi bi-file-earmark-text me-2"></i> Laporan
                            </a>
                        </li>
                    </ul>
                    <li>
    <a href="kelola_admin.php" class="nav-link">
        <i class="bi bi-person-gear me-2"></i> Kelola Admin
    </a>
</li>
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
                <h2 class="mb-4">Laporan Penjualan</h2>
                
                <!-- Filter -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" action="">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="start_date" class="form-label">Tanggal Mulai</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $start_date; ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="end_date" class="form-label">Tanggal Selesai</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $end_date; ?>">
                                </div>
                                <div class="col-md-4 mb-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary me-2">Filter</button>
                                    <a href="laporan.php" class="btn btn-secondary">Reset</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Summary -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Penjualan</h5>
                                <h3>Rp. <?php echo number_format($total_data['total'], 0, ',', '.'); ?></h3>
                                <p class="mb-0">Periode: <?php echo date('d/m/Y', strtotime($start_date)); ?> - <?php echo date('d/m/Y', strtotime($end_date)); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Jumlah Transaksi</h5>
                                <h3><?php echo mysqli_num_rows($result); ?></h3>
                                <p class="mb-0">Rata-rata: Rp. <?php echo mysqli_num_rows($result) > 0 ? number_format($total_data['total'] / mysqli_num_rows($result), 0, ',', '.') : '0'; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Transaction Table -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Daftar Transaksi</h5>
                        <a href="cetak_laporan.php?start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>" class="btn btn-sm btn-outline-primary" target="_blank">
                            <i class="bi bi-printer me-1"></i> Cetak
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID Transaksi</th>
                                        <th>Tanggal</th>
                                        <th>Pelanggan</th>
                                        <th>Total</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (mysqli_num_rows($result) > 0) { ?>
                                        <?php while ($transaction = mysqli_fetch_assoc($result)) { ?>
                                            <tr>
                                                <td>#<?php echo $transaction['PenjualanID']; ?></td>
                                                <td><?php echo date('d/m/Y H:i', strtotime($transaction['TanggalPenjualan'])); ?></td>
                                                <td><?php echo $transaction['NamaPelanggan']; ?></td>
                                                <td>Rp. <?php echo number_format($transaction['TotalHarga'], 0, ',', '.'); ?></td>
                                                <td>
                                                    <a href="detail_transaksi.php?id=<?php echo $transaction['PenjualanID']; ?>" class="btn btn-sm btn-info">Detail</a>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <tr>
                                            <td colspan="5" class="text-center">Tidak ada data transaksi</td>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>