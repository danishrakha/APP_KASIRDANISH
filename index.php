<?php
// Mulai session
session_start();
date_default_timezone_set('Asia/Jakarta');
// Cek apakah user sudah login sebagai kasir
if (!isset($_SESSION['kasir'])) {
    // Jika belum login, redirect ke halaman login kasir
    header("Location: login_kasir.php");
    exit();
}

include 'koneksi.php';

// Ambil data produk
 $produk = mysqli_query($conn, "SELECT * FROM kasir_produk");
// Ambil data pelanggan
 $pelanggan = mysqli_query($conn, "SELECT * FROM kasir_pelanggan");

// Ambil data toko dengan pengecekan error
 $tokoData = [];
 $tokoQuery = mysqli_query($conn, "SELECT * FROM kasir_toko LIMIT 1");
if ($tokoQuery) {
    $tokoData = mysqli_fetch_assoc($tokoQuery);
} else {
    // Data default jika tabel tidak ada atau query gagal
    $tokoData = [
        'NamaToko' => 'TOKO KU',
        'Alamat' => 'Jl. Contoh No. 123',
        'Telepon' => '0812-3456-7890'
    ];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi Kasir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .product-card {
            cursor: pointer;
            transition: transform 0.2s;
        }
        .product-card:hover {
            transform: scale(1.05);
        }
        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px;
            border-bottom: 1px solid #eee;
        }
        .cart-item:last-child {
            border-bottom: none;
        }
        .payment-section {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 15px;
            margin-top: 15px;
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
        .low-stock {
            color: red;
            font-weight: bold;
        }
        .out-of-stock {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .success-message {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            max-width: 350px;
        }
        .user-info {
            color: white;
            margin-right: 15px;
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
                            <a class="nav-link" href="dashboard.php">Admin</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="index.php">Kasir</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="stok.php">Stok</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="pelanggan.php">Pelanggan</a>
                        </li>
                    </ul>
                    <div class="d-flex">
                        <span class="user-info">Kasir: <?php echo $_SESSION['kasir']['nama']; ?></span>
                        <a href="logout_kasir.php" class="btn btn-outline-light">Logout</a>
                    </div>
                </div>
            </div>
        </nav>

        <div class="row mt-3">
            <div class="col-md-8">
                <h4>Daftar Produk</h4>
                <div class="row">
                    <?php while ($row = mysqli_fetch_assoc($produk)) { ?>
                    <div class="col-md-4 mb-3">
                        <div class="card product-card <?php echo ($row['Stok'] <= 0) ? 'out-of-stock' : ''; ?>" 
                             onclick="<?php echo ($row['Stok'] > 0) ? "addToCart({$row['ProdukID']}, '{$row['NamaProduk']}', {$row['Harga']}, {$row['Stok']})" : ''; ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $row['NamaProduk']; ?></h5>
                                <p class="card-text">Harga: Rp. <?php echo number_format($row['Harga'], 2, ',', '.'); ?></p>
                                <p class="card-text <?php echo ($row['Stok'] <= 5) ? 'low-stock' : ''; ?>">
                                    Stok: <?php echo $row['Stok']; ?> 
                                    <?php if ($row['Stok'] <= 0) echo '<span class="badge bg-danger">Habis</span>'; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">Pilih Pelanggan</h5>
                    </div>
                    <div class="card-body">
                        <select class="form-select" id="pelanggan">
                            <option value="0">Pelanggan Umum</option>
                            <?php 
                            mysqli_data_seek($pelanggan, 0);
                            while ($row = mysqli_fetch_assoc($pelanggan)) { ?>
                            <option value="<?php echo $row['PelangganID']; ?>"><?php echo $row['NamaPelanggan']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Keranjang</h5>
                        <button class="btn btn-sm btn-danger" onclick="clearCart()">Kosongkan</button>
                    </div>
                    <div class="card-body">
                        <div id="cart-items">
                            <p class="text-center text-muted">Keranjang kosong</p>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <h5>Total:</h5>
                            <h5 id="cart-total">Rp. 0,00</h5>
                        </div>
                    </div>
                </div>

                <div class="payment-section">
                    <div class="mb-3">
                        <label for="uang-diberikan" class="form-label">Uang Diberikan</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp.</span>
                            <input type="number" class="form-control" id="uang-diberikan" placeholder="0" min="0" step="100">
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <h5>Kembalian:</h5>
                            <h5 id="kembalian">Rp. 0,00</h5>
                        </div>
                    </div>
                    <button class="btn btn-success w-100" id="bayar-btn" onclick="processPayment()" disabled>Bayar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Struk Pembayaran -->
    <div class="modal fade" id="strukModal" tabindex="-1" aria-labelledby="strukModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="strukModalLabel">Struk Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="struk-container" id="struk-content">
                        <!-- Struk content akan di-generate oleh JavaScript -->
                    </div>
                </div>
                <div class="modal-footer no-print">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" onclick="printStruk()">
                        <i class="fas fa-print"></i> Cetak Struk
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Container untuk pesan sukses -->
    <div class="success-message" id="success-message"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let cart = [];
        let strukModal;
        let transactionData = null;
        let productStock = {}; // Menyimpan data stok produk terbaru

        document.addEventListener('DOMContentLoaded', function() {
            strukModal = new bootstrap.Modal(document.getElementById('strukModal'));
            
            // Simpan data stok produk awal
            <?php 
            mysqli_data_seek($produk, 0);
            while ($row = mysqli_fetch_assoc($produk)) { ?>
            productStock[<?php echo $row['ProdukID']; ?>] = <?php echo $row['Stok']; ?>;
            <?php } ?>
        });

        function addToCart(id, name, price, stock) {
            // Cek stok terbaru
            if (productStock[id] <= 0) {
                alert('Stok habis!');
                return;
            }

            // Check if product already in cart
            const existingItem = cart.find(item => item.id === id);
            if (existingItem) {
                if (existingItem.quantity < productStock[id]) {
                    existingItem.quantity++;
                } else {
                    alert('Stok tidak mencukupi!');
                    return;
                }
            } else {
                cart.push({
                    id: id,
                    name: name,
                    price: price,
                    quantity: 1
                });
            }

            updateCartDisplay();
        }

        function updateCartDisplay() {
            const cartItems = document.getElementById('cart-items');
            const cartTotal = document.getElementById('cart-total');
            const bayarBtn = document.getElementById('bayar-btn');
            const uangDiberikan = document.getElementById('uang-diberikan');

            if (cart.length === 0) {
                cartItems.innerHTML = '<p class="text-center text-muted">Keranjang kosong</p>';
                cartTotal.textContent = 'Rp. 0,00';
                bayarBtn.disabled = true;
                updateKembalian();
                return;
            }

            let html = '';
            let total = 0;

            cart.forEach(item => {
                const subtotal = item.price * item.quantity;
                total += subtotal;

                html += `
                    <div class="cart-item">
                        <div>
                            <strong>${item.name}</strong><br>
                            <small>Rp. ${item.price.toLocaleString('id-ID')} x ${item.quantity}</small>
                        </div>
                        <div>
                            <button class="btn btn-sm btn-outline-primary" onclick="changeQuantity(${item.id}, -1)">-</button>
                            <span class="mx-2">${item.quantity}</span>
                            <button class="btn btn-sm btn-outline-primary" onclick="changeQuantity(${item.id}, 1)">+</button>
                            <button class="btn btn-sm btn-outline-danger ms-2" onclick="removeFromCart(${item.id})">Hapus</button>
                        </div>
                    </div>
                `;
            });

            cartItems.innerHTML = html;
            cartTotal.textContent = `Rp. ${total.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
            
            // Enable payment button if there are items in cart
            bayarBtn.disabled = false;
            
            // Update kembalian when cart changes
            updateKembalian();
        }

        function changeQuantity(id, change) {
            const item = cart.find(item => item.id === id);
            if (item) {
                const newQuantity = item.quantity + change;
                
                // Cek stok tersedia
                if (change > 0 && newQuantity > productStock[id]) {
                    alert('Stok tidak mencukupi!');
                    return;
                }
                
                if (newQuantity <= 0) {
                    removeFromCart(id);
                } else {
                    item.quantity = newQuantity;
                    updateCartDisplay();
                }
            }
        }

        function removeFromCart(id) {
            cart = cart.filter(item => item.id !== id);
            updateCartDisplay();
        }

        function clearCart() {
            cart = [];
            updateCartDisplay();
        }

        function updateKembalian() {
            const cartTotalText = document.getElementById('cart-total').textContent;
            const uangDiberikan = parseFloat(document.getElementById('uang-diberikan').value) || 0;
            
            // Extract numeric value from cart total (remove "Rp. " and commas)
            const cartTotal = parseFloat(cartTotalText.replace('Rp. ', '').replace(/\./g, '').replace(',', '.')) || 0;
            
            const kembalian = uangDiberikan - cartTotal;
            const kembalianElement = document.getElementById('kembalian');
            
            if (kembalian < 0) {
                kembalianElement.textContent = 'Rp. 0,00';
                kembalianElement.style.color = 'red';
            } else {
                kembalianElement.textContent = `Rp. ${kembalian.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                kembalianElement.style.color = 'black';
            }
        }

        // Add event listener for uang diberikan input
        document.getElementById('uang-diberikan').addEventListener('input', updateKembalian);

        function processPayment() {
            const pelangganId = document.getElementById('pelanggan').value;
            const uangDiberikan = parseFloat(document.getElementById('uang-diberikan').value) || 0;
            const cartTotalText = document.getElementById('cart-total').textContent;
            const cartTotal = parseFloat(cartTotalText.replace('Rp. ', '').replace(/\./g, '').replace(',', '.')) || 0;
            
            if (cart.length === 0) {
                alert('Keranjang belanja kosong!');
                return;
            }
            
            if (uangDiberikan < cartTotal) {
                alert('Uang yang diberikan kurang!');
                return;
            }

            // Cek stok lagi sebelum memproses pembayaran
            for (const item of cart) {
                if (item.quantity > productStock[item.id]) {
                    alert(`Stok untuk ${item.name} tidak mencukupi!`);
                    return;
                }
            }

            // Send data to server
            fetch('proses_transaksi.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    pelanggan_id: pelangganId,
                    cart: cart,
                    uang_diberikan: uangDiberikan,
                    kasir_id: <?php echo $_SESSION['kasir']['id']; ?>
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Simpan data transaksi untuk struk
                    transactionData = {
                        id: data.transaction_id,
                        date: data.transaction_date,
                        pelanggan: data.pelanggan,
                        kasir: '<?php echo $_SESSION['kasir']['nama']; ?>',
                        items: cart,
                        total: cartTotal,
                        uang_diberikan: uangDiberikan,
                        kembalian: uangDiberikan - cartTotal
                    };
                    
                    // Update stok lokal
                    cart.forEach(item => {
                        if (productStock[item.id]) {
                            productStock[item.id] -= item.quantity;
                        }
                    });
                    
                    // Generate struk
                    generateStruk();
                    
                    // Tampilkan modal struk
                    strukModal.show();
                    
                    // Reset form
                    clearCart();
                    document.getElementById('uang-diberikan').value = '';
                    updateKembalian();
                    
                    // Tampilkan pesan sukses
                    showSuccessMessage('Transaksi berhasil! Stok produk telah diperbarui.');
                    
                    // Reload halaman setelah menutup modal untuk memastikan data stok terbaru
                    document.getElementById('strukModal').addEventListener('hidden.bs.modal', function () {
                        location.reload();
                    }, { once: true });
                } else {
                    alert('Transaksi gagal: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat memproses transaksi.');
            });
        }

        function generateStruk() {
            if (!transactionData) return;
            
            const strukContent = document.getElementById('struk-content');
            const tokoName = '<?php echo isset($tokoData['NamaToko']) ? $tokoData['NamaToko'] : "TOKO KU"; ?>';
            const tokoAddress = '<?php echo isset($tokoData['Alamat']) ? $tokoData['Alamat'] : "Jl. Contoh No. 123"; ?>';
            const tokoPhone = '<?php echo isset($tokoData['Telepon']) ? $tokoData['Telepon'] : "0812-3456-7890"; ?>';
            
            let itemsHtml = '';
            let subtotal = 0;
            
            transactionData.items.forEach(item => {
                const itemSubtotal = item.price * item.quantity;
                subtotal += itemSubtotal;
                
                itemsHtml += `
                    <div class="struk-item">
                        <div class="struk-item-name">${item.name}</div>
                        <div class="struk-item-qty">${item.quantity}</div>
                        <div class="struk-item-price">Rp ${itemSubtotal.toLocaleString('id-ID')}</div>
                    </div>
                `;
            });
            
            strukContent.innerHTML = `
                <div class="struk-watermark">${tokoName}</div>
                <div class="struk-header">
                    <div class="struk-logo">
                        <i class="fas fa-store"></i>
                    </div>
                    <h3>${tokoName}</h3>
                    <p>${tokoAddress}</p>
                    <p>Telp: ${tokoPhone}</p>
                </div>
                
                <div class="struk-info">
                    <div class="struk-info-item">
                        <div class="struk-info-label">Tanggal:</div>
                        <div class="struk-info-value">${transactionData.date}</div>
                    </div>
                    <div class="struk-info-item">
                        <div class="struk-info-label">No. Transaksi:</div>
                        <div class="struk-info-value">#${transactionData.id}</div>
                    </div>
                    <div class="struk-info-item">
                        <div class="struk-info-label">Kasir:</div>
                        <div class="struk-info-value">${transactionData.kasir}</div>
                    </div>
                    <div class="struk-info-item">
                        <div class="struk-info-label">Pelanggan:</div>
                        <div class="struk-info-value">${transactionData.pelanggan}</div>
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
                        <div>Rp ${transactionData.total.toLocaleString('id-ID')}</div>
                    </div>
                </div>
                
                <div class="struk-payment">
                    <div class="struk-payment-item">
                        <div>Metode Pembayaran:</div>
                        <div>Tunai</div>
                    </div>
                    <div class="struk-payment-item">
                        <div>Jumlah Dibayar:</div>
                        <div>Rp ${transactionData.uang_diberikan.toLocaleString('id-ID')}</div>
                    </div>
                    <div class="struk-payment-item total">
                        <div>Kembalian:</div>
                        <div>Rp ${transactionData.kembalian.toLocaleString('id-ID')}</div>
                    </div>
                </div>
                
                <div class="struk-footer">
                    <p class="thank-you">TERIMA KASIH</p>
                    <p>Atas Kunjungan Anda</p>
                    <p>Barang yang sudah dibeli tidak dapat dikembalikan</p>
                    <p>${new Date().getFullYear()} © ${tokoName}</p>
                </div>
            `;
        }

        function printStruk() {
            window.print();
        }
            
        function showSuccessMessage(message) {
            const successContainer = document.getElementById('success-message');
            const alert = document.createElement('div');
            alert.className = 'alert alert-success alert-dismissible fade show';
            alert.innerHTML = `
                <i class="fas fa-check-circle me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            successContainer.appendChild(alert);
            
            // Auto close after 5 seconds
            setTimeout(() => {
                alert.classList.remove('show'); 
                setTimeout(() => {
                    if (successContainer.contains(alert)) {
                        successContainer.removeChild(alert);
                    }
                }, 150);
            }, 5000);
        }
        // Update waktu real-time
function updateTime() {
    const now = new Date();
    const options = { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    };
    document.getElementById('current-time').textContent = now.toLocaleDateString('id-ID', options);
}

// Update waktu setiap detik
setInterval(updateTime, 1000);
updateTime(); // Panggil sekali saat halaman dimuat
    </script>
</body>
</html>