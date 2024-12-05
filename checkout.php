<?php
include('header.php');
session_start();

// Koneksi ke database
$conn = new mysqli('localhost', 'root', '', 'efisongket');
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil data keranjang dari sesi
$cartItems = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$total = 0;
$success = "";
$error = "";

// Proses form jika data dikirim
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $address = htmlspecialchars($_POST['address']);
    $paymentMethod = htmlspecialchars($_POST['payment_method']);

    // Validasi input
    if (empty($name) || empty($address) || empty($paymentMethod)) {
        $error = "Semua kolom wajib diisi.";
    } else {
        // Simpan data pesanan
        $stmt = $conn->prepare("INSERT INTO orders (name, address, payment_method, total_price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssd", $name, $address, $paymentMethod, $total);
        if ($stmt->execute()) {
            $orderId = $stmt->insert_id; // Ambil ID pesanan terbaru

            // Simpan item pesanan
            $itemStmt = $conn->prepare("INSERT INTO order_items (order_id, product_name, quantity, price) VALUES (?, ?, ?, ?)");
            foreach ($cartItems as $item) {
                $itemStmt->bind_param("isid", $orderId, $item['name'], $item['quantity'], $item['price']);
                $itemStmt->execute();
            }
            $itemStmt->close();

            // Kosongkan keranjang setelah pesanan berhasil
            unset($_SESSION['cart']);
            $success = "Pesanan berhasil disimpan! Terima kasih telah berbelanja di Efi Songket.";
        } else {
            $error = "Gagal menyimpan pesanan: " . $stmt->error;
        }
        $stmt->close();
    }
}

$conn->close();
?>

<div class="container mt-5">
    <h2>Checkout</h2>
    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php elseif (!empty($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="mb-3">
            <label for="name" class="form-label">Nama Lengkap</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="mb-3">
            <label for="address" class="form-label">Alamat Pengiriman</label>
            <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
        </div>
        <div class="mb-3">
            <label for="payment_method" class="form-label">Metode Pembayaran</label>
            <select class="form-select" id="payment_method" name="payment_method" required>
                <option value="Transfer Bank">Transfer Bank</option>
                <option value="COD">Bayar di Tempat (COD)</option>
                <option value="E-Wallet">E-Wallet</option>
            </select>
        </div>
        <h4>Ringkasan Pesanan</h4>
        <ul class="list-group">
            <?php foreach ($cartItems as $item): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?= htmlspecialchars($item["name"]) ?>
                    <span>Rp <?= number_format($item["price"] * $item["quantity"], 0, ',', '.') ?></span>
                </li>
                <?php $total += $item["price"] * $item["quantity"]; ?>
            <?php endforeach; ?>
        </ul>
        <h3 class="mt-3">Total: Rp <?= number_format($total, 0, ',', '.') ?></h3>
        <button type="submit" class="btn btn-primary mt-3">Konfirmasi Pesanan</button>
    </form>
</div>

<?php include('footer.php'); ?>
