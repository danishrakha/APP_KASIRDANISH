<?php
session_start();
include 'koneksi.php';

// Cek login
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Ambil data statistik
// Total penjualan hari ini
 $today = date('Y-m-d');
 // KODE YANG BENAR:
  $query_today = "SELECT COUNT(*) as total_transaksi, SUM(TotalHarga) as total_pendapatan FROM kasir_penjualan WHERE DATE(TanggalPenjualan) = '$today'";
 $result_today = mysqli_query($conn, $query_today);
 $today_stats = mysqli_fetch_assoc($result_today);

// Total produk
 $query_products = "SELECT COUNT(*) as total_produk FROM kasir_produk";
 $result_products = mysqli_query($conn, $query_products);
 $products_stats = mysqli_fetch_assoc($result_products);

// Total pelanggan
 $query_customers = "SELECT COUNT(*) as total_pelanggan FROM kasir_pelanggan";
 $result_customers = mysqli_query($conn, $query_customers);
 $customers_stats = mysqli_fetch_assoc($result_customers);

// Produk terlaris
// KODE YANG BENAR:
 $query_top_products = "SELECT p.NamaProduk, SUM(dp.Jumlah) AS total_terjual 
    FROM kasir_detailpenjualan dp
    JOIN kasir_produk p ON dp.ProdukID = p.ProdukID
    JOIN kasir_penjualan j ON dp.PenjualanID = j.PenjualanID
    WHERE DATE(j.TanggalPenjualan) = '$today'
    GROUP BY p.ProdukID
    ORDER BY total_terjual DESC
    LIMIT 5";
$result_top_products = mysqli_query($conn, $query_top_products);

if (!$result_top_products) {
    die('Error top products: ' . mysqli_error($conn));
}

// Penjualan 7 hari terakhir
 $last_7_days = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $last_7_days[] = $date;
    
    $query_sales = "SELECT SUM(TotalHarga) as total FROM kasir_penjualan WHERE DATE(TanggalPenjualan) = '$date'";
    $result_sales = mysqli_query($conn, $query_sales);
    $sales_data = mysqli_fetch_assoc($result_sales);
    $sales[$date] = $sales_data['total'] ? $sales_data['total'] : 0;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        .stat-card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-card .card-body {
            padding: 1.5rem;
        }
        .stat-card i {
            font-size: 2rem;
        }
        .chart-container {
            position: relative;
            height: 300px;
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
                            <a href="dashboard.php" class="nav-link active">
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
    <a href="kelola_admin.php" class="nav-link">
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
                <h2 class="mb-4">Dashboard</h2>
                
                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card stat-card bg-primary text-white">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title">Transaksi Hari Ini</h6>
                                    <h3 class="mb-0"><?php echo $today_stats['total_transaksi']; ?></h3>
                                </div>
                                <i class="bi bi-cart-check"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stat-card bg-success text-white">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title">Pendapatan Hari Ini</h6>
                                    <h3 class="mb-0">Rp. <?php echo number_format($today_stats['total_pendapatan'], 0, ',', '.'); ?></h3>
                                </div>
                                <i class="bi bi-cash-stack"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stat-card bg-info text-white">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title">Total Produk</h6>
                                    <h3 class="mb-0"><?php echo $products_stats['total_produk']; ?></h3>
                                </div>
                                <i class="bi bi-box-seam"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stat-card bg-warning text-white">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title">Total Pelanggan</h6>
                                    <h3 class="mb-0"><?php echo $customers_stats['total_pelanggan']; ?></h3>
                                </div>
                                <i class="bi bi-people"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts -->
                <div class="row mb-4">
                    <div class="col-md-8 mb-3">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Penjualan 7 Hari Terakhir</h5>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="salesChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Produk Terlaris Hari Ini</h5>
                            </div>
                            <div class="card-body">
                                <?php if (mysqli_num_rows($result_top_products) > 0) { ?>
                                    <ul class="list-group list-group-flush">
                                        <?php while ($product = mysqli_fetch_assoc($result_top_products)) { ?>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <?php echo $product['NamaProduk']; ?>
                                                <span class="badge bg-primary rounded-pill"><?php echo $product['total_terjual']; ?></span>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                <?php } else { ?>
                                    <p class="text-center text-muted">Belum ada penjualan hari ini</p>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Transactions -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Transaksi Terbaru</h5>
                                <a href="laporan.php" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
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
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                           // KODE YANG BENAR:

// KODE YANG BENAR:
 $query_recent = "SELECT p.PenjualanID, p.TanggalPenjualan, p.TotalHarga, pl.NamaPelanggan 
    FROM kasir_penjualan p
    LEFT JOIN kasir_pelanggan pl ON p.PelangganID = pl.PelangganID
    ORDER BY p.TanggalPenjualan DESC
    LIMIT 5";

$result_recent = mysqli_query($conn, $query_recent);

if (!$result_recent) {
    die('Error query recent: ' . mysqli_error($conn));
}

if (!$result_recent) {
    die("Error query recent: " . mysqli_error($conn));
}

if (mysqli_num_rows($result_recent) > 0) {
    while ($transaction = mysqli_fetch_assoc($result_recent)) {
        echo "<tr>
            <td>#{$transaction['PenjualanID']}</td>
            <td>" . date('d/m/Y H:i', strtotime($transaction['TanggalPenjualan'])) . "</td>
            <td>{$transaction['NamaPelanggan']}</td>
            <td>Rp. " . number_format($transaction['TotalHarga'], 0, ',', '.') . "</td>
            <td><span class='badge bg-success'>Selesai</span></td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='5' class='text-center'>Belum ada transaksi</td></tr>";
}

                                            $result_recent = mysqli_query($conn, $query_recent);
                                            
                                            if (mysqli_num_rows($result_recent) > 0) {
                                                while ($transaction = mysqli_fetch_assoc($result_recent)) {
                                                    echo "<tr>
                                                        <td>#{$transaction['PenjualanID']}</td>
                                                        <td>" . date('d/m/Y H:i', strtotime($transaction['TanggalPenjualan'])) . "</td>
                                                        <td>{$transaction['NamaPelanggan']}</td>
                                                        <td>Rp. " . number_format($transaction['TotalHarga'], 0, ',', '.') . "</td>
                                                        <td><span class='badge bg-success'>Selesai</span></td>
                                                    </tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='5' class='text-center'>Belum ada transaksi</td></tr>";
                                            }
                                            ?>
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
    <script>
        // Chart for sales
        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [
                    '<?php echo date('d/m', strtotime($last_7_days[0])); ?>',
                    '<?php echo date('d/m', strtotime($last_7_days[1])); ?>',
                    '<?php echo date('d/m', strtotime($last_7_days[2])); ?>',
                    '<?php echo date('d/m', strtotime($last_7_days[3])); ?>',
                    '<?php echo date('d/m', strtotime($last_7_days[4])); ?>',
                    '<?php echo date('d/m', strtotime($last_7_days[5])); ?>',
                    '<?php echo date('d/m', strtotime($last_7_days[6])); ?>'
                ],
                datasets: [{
                    label: 'Penjualan (Rp)',
                    data: [
                        <?php echo $sales[$last_7_days[0]]; ?>,
                        <?php echo $sales[$last_7_days[1]]; ?>,
                        <?php echo $sales[$last_7_days[2]]; ?>,
                        <?php echo $sales[$last_7_days[3]]; ?>,
                        <?php echo $sales[$last_7_days[4]]; ?>,
                        <?php echo $sales[$last_7_days[5]]; ?>,
                        <?php echo $sales[$last_7_days[6]]; ?>
                    ],
                    backgroundColor: 'rgba(13, 110, 253, 0.2)',
                    borderColor: 'rgba(13, 110, 253, 1)',
                    borderWidth: 2,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });

        
    </script>
</body>
</html>