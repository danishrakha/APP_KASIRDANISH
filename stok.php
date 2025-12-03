<?php
include 'koneksi.php';

// Proses tambah produk
if (isset($_POST['tambah_produk'])) {
    $nama = $_POST['nama_produk'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];

    $query = "INSERT INTO kasir_produk (NamaProduk, Harga, Stok) VALUES ('$nama', '$harga', '$stok')";
    if (mysqli_query($conn, $query)) {
        header("Location: stok.php?status=success&message=" . urlencode("Produk berhasil ditambahkan!"));
        exit();
    } else {
        header("Location: stok.php?status=error&message=" . urlencode("Error: " . mysqli_error($conn)));
        exit();
    }
}

// Proses ubah stok
if (isset($_POST['ubah_stok'])) {
    $id_produk = $_POST['id_produk'];
    $stok_baru = $_POST['stok_baru'];

    $query = "UPDATE kasir_produk SET Stok = '$stok_baru' WHERE ProdukID = '$id_produk'";
    if (mysqli_query($conn, $query)) {
        header("Location: stok.php?status=success&message=" . urlencode("Stok berhasil diperbarui!"));
        exit();
    } else {
        header("Location: stok.php?status=error&message=" . urlencode("Error: " . mysqli_error($conn)));
        exit();
    }
}

// Proses hapus produk
if (isset($_GET['hapus'])) {
    $id_produk = $_GET['hapus'];
    
    // Cek apakah produk ada
    $cek_query = "SELECT * FROM kasir_produk WHERE ProdukID = '$id_produk'";
    $result = mysqli_query($conn, $cek_query);
    
    if (mysqli_num_rows($result) > 0) {
        $produk_data = mysqli_fetch_assoc($result);
        
        // Cek apakah produk masih digunakan dalam transaksi
        $cek_transaksi = "SELECT COUNT(*) as total FROM kasir_detailpenjualan WHERE ProdukID = '$id_produk'";
        $result_transaksi = mysqli_query($conn, $cek_transaksi);
        $data_transaksi = mysqli_fetch_assoc($result_transaksi);
        
        if ($data_transaksi['total'] > 0) {
            // Produk masih digunakan dalam transaksi, hapus data terkait terlebih dahulu
            
            // Mulai transaksi
            mysqli_begin_transaction($conn);
            try {
                // Hapus detail penjualan yang terkait dengan produk ini
                $hapus_detail = "DELETE FROM kasir_detailpenjualan WHERE ProdukID = '$id_produk'";
                if (!mysqli_query($conn, $hapus_detail)) {
                    throw new Exception("Gagal menghapus detail penjualan: " . mysqli_error($conn));
                }
                
                // Hapus produk
                $hapus_produk = "DELETE FROM kasir_produk WHERE ProdukID = '$id_produk'";
                if (!mysqli_query($conn, $hapus_produk)) {
                    throw new Exception("Gagal menghapus produk: " . mysqli_error($conn));
                }
                
                // Commit transaksi
                mysqli_commit($conn);
                header("Location: stok.php?status=success&message=" . urlencode("Produk '{$produk_data['NamaProduk']}' dan data terkait berhasil dihapus!"));
                exit();
                
            } catch (Exception $e) {
                // Rollback transaksi jika ada error
                mysqli_rollback($conn);
                header("Location: stok.php?status=error&message=" . urlencode($e->getMessage()));
                exit();
            }
        } else {
            // Hapus produk langsung jika tidak ada transaksi terkait
            $delete_query = "DELETE FROM kasir_produk WHERE ProdukID = '$id_produk'";
            if (mysqli_query($conn, $delete_query)) {
                header("Location: stok.php?status=success&message=" . urlencode("Produk '{$produk_data['NamaProduk']}' berhasil dihapus!"));
                exit();
            } else {
                $error_msg = mysqli_error($conn);
                header("Location: stok.php?status=error&message=" . urlencode("Gagal menghapus produk: $error_msg"));
                exit();
            }
        }
    } else {
        header("Location: stok.php?status=error&message=" . urlencode("Produk tidak ditemukan!"));
        exit();
    }
}

// Ambil data produk
 $produk = mysqli_query($conn, "SELECT * FROM kasir_produk");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Stok</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .btn-hapus {
            transition: all 0.3s;
        }
        .btn-hapus:hover {
            transform: scale(1.05);
        }
        .alert-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            max-width: 350px;
        }
        .produk-terkait {
            opacity: 0.6;
        }
        .table th {
            background-color: #f8f9fa;
        }
        .force-delete {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .force-delete:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Aplikasi Kasir</a>
                <div class="collapse navbar-collapse">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php">admin</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">Kasir</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="stok.php">Stok</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="pelanggan.php">Pelanggan</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="row mt-3">
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Tambah Produk Baru</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="nama_produk" class="form-label">Nama Produk</label>
                                <input type="text" class="form-control" id="nama_produk" name="nama_produk" required>
                            </div>
                            <div class="mb-3">
                                <label for="harga" class="form-label">Harga</label>
                                <input type="number" class="form-control" id="harga" name="harga" min="0" step="0.01" required>
                            </div>
                            <div class="mb-3">
                                <label for="stok" class="form-label">Stok</label>
                                <input type="number" class="form-control" id="stok" name="stok" min="0" required>
                            </div>
                            <button type="submit" class="btn btn-primary" name="tambah_produk">Tambah Produk</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-7">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Daftar Produk</h5>
                        <span class="badge bg-primary"><?php echo mysqli_num_rows($produk); ?> Produk</span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nama Produk</th>
                                        <th>Harga</th>
                                        <th>Stok</th>
                                        <th>Ubah Stok</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (mysqli_num_rows($produk) > 0) { ?>
                                        <?php while ($row = mysqli_fetch_assoc($produk)) { ?>
                                            <?php 
                                            // Cek apakah produk masih digunakan dalam transaksi
                                            $cek_transaksi = "SELECT COUNT(*) as total FROM kasir_detailpenjualan WHERE ProdukID = '" . $row['ProdukID'] . "'";
                                            $result_transaksi = mysqli_query($conn, $cek_transaksi);
                                            $data_transaksi = mysqli_fetch_assoc($result_transaksi);
                                            $hasTransaction = $data_transaksi['total'] > 0;
                                            ?>
                                            <tr <?php echo $hasTransaction ? 'class="produk-terkait"' : ''; ?>>
                                                <td><?php echo $row['ProdukID']; ?></td>
                                                <td><?php echo $row['NamaProduk']; ?></td>
                                                <td>Rp. <?php echo number_format($row['Harga'], 2, ',', '.'); ?></td>
                                                <td><?php echo $row['Stok']; ?></td>
                                                <td>
                                                    <form method="POST" action="" class="d-flex">
                                                        <input type="hidden" name="id_produk" value="<?php echo $row['ProdukID']; ?>">
                                                        <input type="number" class="form-control form-control-sm me-2" name="stok_baru" value="<?php echo $row['Stok']; ?>" min="0" required>
                                                        <button type="submit" class="btn btn-sm btn-primary" name="ubah_stok">Update</button>
                                                    </form>
                                                </td>
                                                <td>
                                                    <a href="edit_produk.php?id=<?php echo $row['ProdukID']; ?>" class="btn btn-sm btn-warning me-1">
                                                        <i class="fas fa-edit"></i> Sunting
                                                    </a>
                                                    <?php if ($hasTransaction) { ?>
                                                        <button type="button" class="btn btn-sm btn-danger force-delete" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#hapusModal<?php echo $row['ProdukID']; ?>"
                                                                title="Hapus produk dan semua data terkait">
                                                            <i class="fas fa-trash-alt"></i> Hapus
                                                        </button>
                                                        
                                                        <!-- Modal Hapus Konfirmasi -->
                                                        <div class="modal fade" id="hapusModal<?php echo $row['ProdukID']; ?>" tabindex="-1" aria-labelledby="hapusModalLabel<?php echo $row['ProdukID']; ?>" aria-hidden="true">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="hapusModalLabel<?php echo $row['ProdukID']; ?>">Konfirmasi Hapus</h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="alert alert-danger">
                                                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                                                            <strong>Peringatan:</strong> Produk ini terkait dengan <?php echo $data_transaksi['total']; ?> transaksi!
                                                                        </div>
                                                                        <p>Apakah Anda yakin ingin menghapus produk "<strong><?php echo $row['NamaProduk']; ?></strong>" dan semua data terkait?</p>
                                                                        <p class="text-muted small">ID Produk: <?php echo $row['ProdukID']; ?></p>
                                                                        <p class="text-danger small"><strong>Tindakan ini tidak dapat dibatalkan!</strong></p>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                        <a href="stok.php?hapus=<?php echo $row['ProdukID']; ?>" class="btn btn-danger">
                                                                            <i class="fas fa-trash-alt"></i> Ya, Hapus Semua
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php } else { ?>
                                                        <button type="button" class="btn btn-sm btn-danger btn-hapus" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#hapusModal<?php echo $row['ProdukID']; ?>"
                                                                title="Hapus produk">
                                                            <i class="fas fa-trash-alt"></i> Hapus
                                                        </button>
                                                        
                                                        <!-- Modal Hapus Konfirmasi -->
                                                        <div class="modal fade" id="hapusModal<?php echo $row['ProdukID']; ?>" tabindex="-1" aria-labelledby="hapusModalLabel<?php echo $row['ProdukID']; ?>" aria-hidden="true">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="hapusModalLabel<?php echo $row['ProdukID']; ?>">Konfirmasi Hapus</h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="alert alert-warning">
                                                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                                                            <strong>Peringatan:</strong> Tindakan ini tidak dapat dibatalkan!
                                                                        </div>
                                                                        <p>Apakah Anda yakin ingin menghapus produk "<strong><?php echo $row['NamaProduk']; ?></strong>"?</p>
                                                                        <p class="text-muted small">ID Produk: <?php echo $row['ProdukID']; ?></p>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                        <a href="stok.php?hapus=<?php echo $row['ProdukID']; ?>" class="btn btn-danger">
                                                                            <i class="fas fa-trash-alt"></i> Ya, Hapus
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-3">Tidak ada data produk</td>
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

    <!-- Container untuk alert -->
    <div class="alert-container" id="alert-container"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fungsi untuk menampilkan alert
        function showAlert(message, type) {
            const alertContainer = document.getElementById('alert-container');
            const alert = document.createElement('div');
            alert.className = `alert alert-${type} alert-dismissible fade show`;
            alert.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            alertContainer.appendChild(alert);
            
            // Auto close after 5 seconds
            setTimeout(() => {
                alert.classList.remove('show');
                setTimeout(() => {
                    if (alertContainer.contains(alert)) {
                        alertContainer.removeChild(alert);
                    }
                }, 150);
            }, 5000);
        }
        
        // Cek parameter URL untuk pesan sukses/error
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('status') === 'error') {
            const message = decodeURIComponent(urlParams.get('message') || 'Terjadi kesalahan!');
            showAlert(message, 'danger');
            
            // Hapus parameter dari URL
            const newUrl = window.location.pathname;
            window.history.replaceState({}, document.title, newUrl);
        } else if (urlParams.get('status') === 'success') {
            const message = decodeURIComponent(urlParams.get('message') || 'Operasi berhasil!');
            showAlert(message, 'success');
            
            // Hapus parameter dari URL
            const newUrl = window.location.pathname;
            window.history.replaceState({}, document.title, newUrl);
        }
    </script>
</body>
</html>