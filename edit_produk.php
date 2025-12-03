<?php
include 'koneksi.php';

// Check if ID is provided
if (!isset($_GET['id'])) {
    header("Location: stok.php");
    exit();
}

 $id = $_GET['id'];

// Get product data
 $query = "SELECT * FROM kasir_produk WHERE ProdukID = '$id'";
 $result = mysqli_query($conn, $query);
 $product = mysqli_fetch_assoc($result);

// Process update
if (isset($_POST['update_produk'])) {
    $nama = $_POST['nama_produk'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];

    $update_query = "UPDATE kasir_produk SET NamaProduk = '$nama', Harga = '$harga', Stok = '$stok' WHERE ProdukID = '$id'";
    
    if (mysqli_query($conn, $update_query)) {
        header("Location: stok.php");
        exit();
    } else {
        echo "Error: " . $update_query . "<br>" . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Edit Produk</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="nama_produk" class="form-label">Nama Produk</label>
                                <input type="text" class="form-control" id="nama_produk" name="nama_produk" value="<?php echo $product['NamaProduk']; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="harga" class="form-label">Harga</label>
                                <input type="number" class="form-control" id="harga" name="harga" value="<?php echo $product['Harga']; ?>" min="0" step="0.01" required>
                            </div>
                            <div class="mb-3">
                                <label for="stok" class="form-label">Stok</label>
                                <input type="number" class="form-control" id="stok" name="stok" value="<?php echo $product['Stok']; ?>" min="0" required>
                            </div>
                            <div class="d-flex justify-content-between">
                                <a href="stok.php" class="btn btn-secondary">Batal</a>
                                <button type="submit" class="btn btn-primary" name="update_produk">Update Produk</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>