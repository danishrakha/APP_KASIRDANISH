<?php
include 'koneksi.php';

// Check if ID is provided
if (!isset($_GET['id'])) {
    header("Location: pelanggan.php");
    exit();
}

 $id = $_GET['id'];

// Get customer data
 $query = "SELECT * FROM kasir_pelanggan WHERE PelangganID = '$id'";
 $result = mysqli_query($conn, $query);
 $customer = mysqli_fetch_assoc($result);

// Process update
if (isset($_POST['update_pelanggan'])) {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $telepon = $_POST['telepon'];
    $alamat = $_POST['alamat'];

    $update_query = "UPDATE kasir_pelanggan SET NamaPelanggan = '$nama', Email = '$email', Telepon = '$telepon', Alamat = '$alamat' WHERE PelangganID = '$id'";
    
    if (mysqli_query($conn, $update_query)) {
        header("Location: pelanggan.php");
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
    <title>Edit Pelanggan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Edit Pelanggan</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama</label>
                                <input type="text" class="form-control" id="nama" name="nama" value="<?php echo $customer['NamaPelanggan']; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo $customer['Email']; ?>">
                            </div>
                            <div class="mb-3">
                                <label for="telepon" class="form-label">Telepon</label>
                                <input type="text" class="form-control" id="telepon" name="telepon" value="<?php echo $customer['Telepon']; ?>">
                            </div>
                            <div class="mb-3">
                                <label for="alamat" class="form-label">Alamat</label>
                                <textarea class="form-control" id="alamat" name="alamat" rows="3"><?php echo $customer['Alamat']; ?></textarea>
                            </div>
                            <div class="d-flex justify-content-between">
                                <a href="pelanggan.php" class="btn btn-secondary">Batal</a>
                                <button type="submit" class="btn btn-primary" name="update_pelanggan">Update Pelanggan</button>
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