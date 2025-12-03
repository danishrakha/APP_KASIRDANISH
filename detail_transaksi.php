<?php
session_start();
include 'koneksi.php';

// Cek login
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Ambil ID transaksi dari URL
if (!isset($_GET['id'])) {
    header("Location: laporan.php");
    exit();
}

 $transaction_id = $_GET['id'];

// Query untuk mendapatkan data transaksi
 $query = "SELECT p.PenjualanID, p.TanggalPenjualan, p.TotalHarga, 
                 pl.NamaPelanggan
          FROM kasir_penjualan p
          LEFT JOIN kasir_pelanggan pl ON p.PelangganID = pl.PelangganID
          WHERE p.PenjualanID = $transaction_id";

 $result = mysqli_query($conn, $query);

// Cek apakah query berhasil
if (!$result) {
    die("Query error: " . mysqli_error($conn));
}

if (mysqli_num_rows($result) == 0) {
    header("Location: laporan.php");
    exit();
}

 $transaction = mysqli_fetch_assoc($result);

// Coba beberapa kemungkinan nama kolom untuk harga dan jumlah
 $harga_columns = ['Harga', 'HargaSatuan', 'Price', 'Subtotal', 'Total'];
 $jumlah_columns = ['Jumlah', 'Qty', 'Kuantitas', 'Quantity'];

 $found_query = null;
 $result_detail = null;

foreach ($harga_columns as $harga_col) {
    foreach ($jumlah_columns as $jumlah_col) {
        $query_detail = "SELECT dp.$jumlah_col, dp.$harga_col, pr.NamaProduk
                        FROM kasir_detailpenjualan dp
                        JOIN kasir_produk pr ON dp.ProdukID = pr.ProdukID
                        WHERE dp.PenjualanID = $transaction_id";
        
        $result_detail = mysqli_query($conn, $query_detail);
        
        if ($result_detail) {
            $found_query = $query_detail;
            break 2;
        }
    }
}

if (!$found_query) {
    die("Tidak dapat menemukan kombinasi kolom yang tepat untuk tabel detail penjualan.");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Transaksi<?php echo $transaction_id; ?></title>
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
        .struk-container {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            max-width: 300px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
        }
        .struk-container::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 8px;
            background: linear-gradient(90deg, #4e73df, #1cc88a);
        }
        .struk-header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px dashed #333;
            position: relative;
        }
        .struk-header h3 {
            margin: 0;
            font-weight: bold;
            font-size: 20px;
            color: #333;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .struk-header p {
            margin: 5px 0;
            font-size: 11px;
            color: #555;
        }
        .struk-logo {
            width: 70px;
            height: 70px;
            margin: 0 auto 15px;
            background: linear-gradient(135deg, #4e73df, #1cc88a);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            color: white;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .struk-info {
            margin-bottom: 20px;
        }
        .struk-info-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
            font-size: 11px;
        }
        .struk-info-label {
            color: #555;
        }
        .struk-info-value {
            font-weight: bold;
        }
        .struk-items {
            margin-bottom: 20px;
        }
        .struk-item-header {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            margin-bottom: 8px;
            font-size: 11px;
            border-bottom: 1px dotted #ccc;
            padding-bottom: 5px;
        }
        .struk-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 11px;
        }
        .struk-item-name {
            flex: 1;
            padding-right: 5px;
        }
        .struk-item-qty {
            width: 30px;
            text-align: right;
        }
        .struk-item-price {
            width: 70px;
            text-align: right;
            font-weight: bold;
        }
        .struk-summary {
            border-top: 1px solid #ddd;
            padding-top: 15px;
            margin-bottom: 20px;
        }
        .struk-summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 11px;
        }
        .struk-summary-item.total {
            font-weight: bold;
            font-size: 13px;
            border-top: 1px solid #ddd;
            padding-top: 8px;
            margin-top: 8px;
            color: #333;
        }
        .struk-payment {
            border-top: 1px solid #ddd;
            padding-top: 15px;
            margin-bottom: 20px;
        }
        .struk-payment-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 11px;
        }
        .struk-payment-item.total {
            font-weight: bold;
            font-size: 12px;
        }
        .struk-footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 2px dashed #333;
            position: relative;
        }
        .struk-footer p {
            margin: 5px 0;
            font-size: 11px;
            color: #555;
        }
        .struk-footer .thank-you {
            font-weight: bold;
            font-size: 14px;
            color: #333;
            margin-bottom: 10px;
        }
        .struk-watermark {
            position: absolute;
            bottom: 20px;
            right: 10px;
            opacity: 0.1;
            font-size: 40px;
            transform: rotate(-30deg);
            color: #333;
            z-index: 0;
        }
        @media print {
            body * {
                visibility: hidden;
            }
            .struk-container, .struk-container * {
                visibility: visible;
            }
            .struk-container {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                box-shadow: none;
                border: none;
            }
            .no-print {
                display: none;
            }
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
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Detail Transaksi #<?php echo $transaction_id; ?></h2>
                    <div>
                        <a href="laporan.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </a>
                        <button class="btn btn-primary" onclick="printStruk()">
                            <i class="bi bi-printer me-1"></i> Cetak Struk
                        </button>
                    </div>
                </div>

                <!-- Transaction Info -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Informasi Transaksi</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="150">ID Transaksi</td>
                                        <td>: #<?php echo $transaction['PenjualanID']; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Tanggal</td>
                                        <td>: <?php echo date('d/m/Y H:i', strtotime($transaction['TanggalPenjualan'])); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Kasir</td>
                                        <td>: Admin</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="150">Pelanggan</td>
                                        <td>: <?php echo $transaction['NamaPelanggan'] ? $transaction['NamaPelanggan'] : 'Pelanggan Umum'; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Total Pembayaran</td>
                                        <td>: Rp. <?php echo number_format($transaction['TotalHarga'], 0, ',', '.'); ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Details -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Detail Produk</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Produk</th>
                                        <th>Harga</th>
                                        <th>Jumlah</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $no = 1;
                                    $total = 0;
                                    
                                    // Ambil nama kolom yang berhasil dari query
                                    $result_fields = mysqli_fetch_fields($result_detail);
                                    $jumlah_field = $result_fields[0]->name;
                                    $harga_field = $result_fields[1]->name;
                                    
                                    mysqli_data_seek($result_detail, 0);
                                    while ($item = mysqli_fetch_assoc($result_detail)) { 
                                        $subtotal = $item[$harga_field] * $item[$jumlah_field];
                                        $total += $subtotal;
                                    ?>
                                        <tr>
                                            <td><?php echo $no++; ?></td>
                                            <td><?php echo $item['NamaProduk']; ?></td>
                                            <td>Rp. <?php echo number_format($item[$harga_field], 0, ',', '.'); ?></td>
                                            <td><?php echo $item[$jumlah_field]; ?></td>
                                            <td>Rp. <?php echo number_format($subtotal, 0, ',', '.'); ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" class="text-end fw-bold">Total</td>
                                        <td class="fw-bold">Rp. <?php echo number_format($total, 0, ',', '.'); ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Struk Preview (Hidden) -->
                <div class="struk-container no-print" id="struk-preview" style="display: none;">
                    <!-- Struk content akan di-generate oleh JavaScript -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Generate struk untuk dicetak
        function generateStruk() {
            const strukPreview = document.getElementById('struk-preview');
            const transactionId = '<?php echo $transaction['PenjualanID']; ?>';
            const transactionDate = '<?php echo date('d/m/Y H:i', strtotime($transaction['TanggalPenjualan'])); ?>';
            const kasir = 'Admin';
            const pelanggan = '<?php echo $transaction['NamaPelanggan'] ? $transaction['NamaPelanggan'] : 'Pelanggan Umum'; ?>';
            const total = <?php echo $transaction['TotalHarga']; ?>;
            
            let itemsHtml = '';
            let subtotal = 0;
            
            <?php 
            mysqli_data_seek($result_detail, 0);
            $result_fields = mysqli_fetch_fields($result_detail);
            $jumlah_field = $result_fields[0]->name;
            $harga_field = $result_fields[1]->name;
            
            mysqli_data_seek($result_detail, 0);
            while ($item = mysqli_fetch_assoc($result_detail)) { 
                $itemSubtotal = $item[$harga_field] * $item[$jumlah_field];
            ?>
                itemsHtml += `
                    <div class="struk-item">
                        <div class="struk-item-name"><?php echo $item['NamaProduk']; ?></div>
                        <div class="struk-item-qty"><?php echo $item[$jumlah_field]; ?></div>
                        <div class="struk-item-price">Rp <?php echo number_format($itemSubtotal, 0, ',', '.'); ?></div>
                    </div>
                `;
                subtotal += <?php echo $itemSubtotal; ?>;
            <?php } ?>
            
            strukPreview.innerHTML = `
                <div class="struk-watermark">TOKO KU</div>
                <div class="struk-header">
                    <div class="struk-logo">
                        <i class="bi bi-shop"></i>
                    </div>
                    <h3>TOKO KU</h3>
                    <p>Jl. Contoh No. 123</p>
                    <p>Telp: 0812-3456-7890</p>
                </div>
                
                <div class="struk-info">
                    <div class="struk-info-item">
                        <div class="struk-info-label">Tanggal:</div>
                        <div class="struk-info-value">${transactionDate}</div>
                    </div>
                    <div class="struk-info-item">
                        <div class="struk-info-label">No. Transaksi:</div>
                        <div class="struk-info-value">#${transactionId}</div>
                    </div>
                    <div class="struk-info-item">
                        <div class="struk-info-label">Kasir:</div>
                        <div class="struk-info-value">${kasir}</div>
                    </div>
                    <div class="struk-info-item">
                        <div class="struk-info-label">Pelanggan:</div>
                        <div class="struk-info-value">${pelanggan}</div>
                    </div>
                </div>
                
                <div class="struk-items">
                    <div class="struk-item-header">
                        <div>PRODUK</div>
                        <div>QTY</div>
                        <div>HARGA</div>
                    </div>
                    ${itemsHtml}
                </div>
                
                <div class="struk-summary">
                    <div class="struk-summary-item">
                        <div>Subtotal:</div>
                        <div>Rp ${subtotal.toLocaleString('id-ID')}</div>
                    </div>
                    <div class="struk-summary-item">
                        <div>PPN (10%):</div>
                        <div>Rp ${(subtotal * 0.1).toLocaleString('id-ID')}</div>
                    </div>
                    <div class="struk-summary-item total">
                        <div>TOTAL:</div>
                        <div>Rp ${total.toLocaleString('id-ID')}</div>
                    </div>
                </div>
                
                <div class="struk-payment">
                    <div class="struk-payment-item">
                        <div>Metode Pembayaran:</div>
                        <div>Tunai</div>
                    </div>
                    <div class="struk-payment-item">
                        <div>Jumlah Dibayar:</div>
                        <div>Rp ${total.toLocaleString('id-ID')}</div>
                    </div>
                    <div class="struk-payment-item total">
                        <div>Kembalian:</div>
                        <div>Rp 0</div>
                    </div>
                </div>
                
                <div class="struk-footer">
                    <p class="thank-you">TERIMA KASIH</p>
                    <p>Atas Kunjungan Anda</p>
                    <p>Barang yang sudah dibeli tidak dapat dikembalikan</p>
                    <p>${new Date().getFullYear()} © TOKO KU</p>
                </div>
            `;
        }

        function printStruk() {
            generateStruk();
            window.print();
        }
    </script>
</body>
</html>