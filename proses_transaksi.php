<?php
date_default_timezone_set('Asia/Jakarta');
include 'koneksi.php';
header('Content-Type: application/json');

// Mendapatkan data dari request
 $data = json_decode(file_get_contents('php://input'), true);

// Validasi data
if (!isset($data['pelanggan_id']) || !isset($data['cart']) || !isset($data['uang_diberikan'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Data tidak lengkap'
    ]);
    exit();
}

 $pelangganId = $data['pelanggan_id'];
 // Tambahkan kode ini:
if ($pelangganId == 0) {
    // Ambil ID pelanggan pertama yang ada di database
    $queryPelanggan = "SELECT PelangganID FROM kasir_pelanggan LIMIT 1";
    $result = mysqli_query($conn, $queryPelanggan);
    if ($result && mysqli_num_rows($result) > 0) {
        $dataPelanggan = mysqli_fetch_assoc($result);
        $pelangganId = $dataPelanggan['PelangganID'];
    } else {
        // Jika tidak ada pelanggan sama sekali, buat pelanggan default
        $buatPelanggan = "INSERT INTO kasir_pelanggan (NamaPelanggan) VALUES ('Pelanggan Umum')";
        mysqli_query($conn, $buatPelanggan);
        $pelangganId = mysqli_insert_id($conn);
    }
}

 $cart = $data['cart'];
 $uangDiberikan = $data['uang_diberikan'];

// Mulai transaksi
mysqli_begin_transaction($conn);

try {
    // Hitung total pembelian
    $total = 0;
    foreach ($cart as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    
    // Validasi uang yang diberikan
    if ($uangDiberikan < $total) {
        throw new Exception('Uang yang diberikan kurang');
    }
    
    // Buat transaksi baru
    $tanggal = date('Y-m-d H:i:s');
    $queryTransaksi = "INSERT INTO kasir_penjualan (TanggalPenjualan, TotalHarga, PelangganID) VALUES ('$tanggal', '$total', '$pelangganId')";
    
    if (!mysqli_query($conn, $queryTransaksi)) {
        throw new Exception('Gagal membuat transaksi: ' . mysqli_error($conn));
    }
    
    $penjualanID = mysqli_insert_id($conn);
    
    // Proses setiap item di keranjang
    foreach ($cart as $item) {
        $produkID = $item['id'];
        $jumlah = $item['quantity'];
        $harga = $item['price'];
        $subtotal = $harga * $jumlah;
        
        // Cek stok produk
        $queryStok = "SELECT Stok FROM kasir_produk WHERE ProdukID = '$produkID'";
        $resultStok = mysqli_query($conn, $queryStok);
        
        if (!$resultStok || mysqli_num_rows($resultStok) == 0) {
            throw new Exception("Produk tidak ditemukan");
        }
        
        $dataStok = mysqli_fetch_assoc($resultStok);
        $stokTersedia = $dataStok['Stok'];
        
        if ($stokTersedia < $jumlah) {
            throw new Exception("Stok tidak mencukupi untuk produk ID: $produkID");
        }
        
        // Update stok produk
        $stokBaru = $stokTersedia - $jumlah;
        $queryUpdateStok = "UPDATE kasir_produk SET Stok = '$stokBaru' WHERE ProdukID = '$produkID'";
        
        if (!mysqli_query($conn, $queryUpdateStok)) {
            throw new Exception('Gagal update stok: ' . mysqli_error($conn));
        }
        
        // Tambah detail penjualan
        $queryDetail = "INSERT INTO kasir_detailpenjualan (PenjualanID, ProdukID, Jumlah, Subtotal) VALUES ('$penjualanID', '$produkID', '$jumlah', '$subtotal')";
        
        if (!mysqli_query($conn, $queryDetail)) {
            throw new Exception('Gagal menambah detail penjualan: ' . mysqli_error($conn));
        }
    }
    
    // Commit transaksi
    mysqli_commit($conn);
    
    // Ambil nama pelanggan
    $namaPelanggan = "Pelanggan Umum";
    if ($pelangganId != 0) {
        $queryPelanggan = "SELECT NamaPelanggan FROM kasir_pelanggan WHERE PelangganID = '$pelangganId'";
        $resultPelanggan = mysqli_query($conn, $queryPelanggan);
        if ($resultPelanggan && mysqli_num_rows($resultPelanggan) > 0) {
            $dataPelanggan = mysqli_fetch_assoc($resultPelanggan);
            $namaPelanggan = $dataPelanggan['NamaPelanggan'];
        }
    }
    
    // Ambil nama kasir (dari session, asumsikan sudah ada)
    $namaKasir = isset($_SESSION['username']) ? $_SESSION['username'] : 'Kasir';
    
    echo json_encode([
        'success' => true,
        'transaction_id' => $penjualanID,
        'transaction_date' => date('d/m/Y H:i:s', strtotime($tanggal)),
        'pelanggan' => $namaPelanggan,
        'kasir' => $namaKasir
    ]);
    
} catch (Exception $e) {
    // Rollback transaksi jika ada error
    mysqli_rollback($conn);
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>