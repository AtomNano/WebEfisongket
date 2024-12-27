<?php
include 'phpconection.php'; // Menghubungkan ke database

// Ambil ID transaksi dari URL
$transaction_id = $_GET['id'] ?? null;
if (!$transaction_id) {
    die('ID Transaksi tidak valid.');
}

// Ambil detail transaksi
$transaction_query = "SELECT * FROM transactions WHERE id = ?";
$transaction_stmt = $db->prepare($transaction_query);
$transaction_stmt->bind_param('i', $transaction_id);
$transaction_stmt->execute();
$transaction_result = $transaction_stmt->get_result();
$transaction = $transaction_result->fetch_assoc();

// Ambil order items
$order_items_query = "SELECT oi.*, p.name AS product_name, p.image AS product_image FROM order_item oi
                      JOIN products p ON oi.product_id = p.id WHERE oi.transaction_id = ?";
$order_items_stmt = $db->prepare($order_items_query);
$order_items_stmt->bind_param('i', $transaction_id);
$order_items_stmt->execute();
$order_items_result = $order_items_stmt->get_result();
$order_items = $order_items_result->fetch_all(MYSQLI_ASSOC);

// Ambil riwayat transaksi
$email = $transaction['email'] ?? null;
$transactions = [];
if ($email) {
    $history_query = "SELECT t.created_at, oi.product_id, p.name AS product_name, oi.quantity, oi.price, t.status, t.shipping_status
                      FROM transactions t
                      JOIN order_item oi ON t.id = oi.transaction_id
                      JOIN products p ON oi.product_id = p.id
                      WHERE t.email = ? ORDER BY t.created_at DESC";
    $history_stmt = $db->prepare($history_query);
    $history_stmt->bind_param('s', $email);
    $history_stmt->execute();
    $history_result = $history_stmt->get_result();
    $transactions = $history_result->fetch_all(MYSQLI_ASSOC);
}

?>

<style>
    .transaction-container {
        display: flex;
        justify-content: space-between;
        gap: 20px;
    }
    .transaction-detail, .order-items {
        flex: 1;
        border: 1px solid #ddd;
        padding: 20px;
        border-radius: 8px;
    }
    .transaction-history {
        margin-top: 30px;
    }
    .transaction-table {
        width: 100%;
        border-collapse: collapse;
    }
    .transaction-table th, .transaction-table td {
        border: 1px solid #ddd;
        padding: 8px;
    }
    .product-image {
        width: 50px;
        height: 50px;
        object-fit: cover;
    }
    .badge-status {
        font-size: 1rem;
        padding: 10px 15px;
        border-radius: 50px;
    }
    .badge-dikonfirmasi {
        background-color: #28a745;
        color: white;
    }
    .badge-tertunda {
        background-color: #ffc107;
        color: black;
    }
    .badge-gagal {
        background-color: #dc3545;
        color: white;
    }
    .badge-dikirim {
        background-color: #007bff;
        color: white;
    }
</style>

<div class="container mt-5">
    <div class="transaction-container">
        <!-- Detail Transaksi -->
        <div class="transaction-detail">
            <h2>Detail Transaksi</h2>
            <p><strong>ID Transaksi:</strong> <?php echo htmlspecialchars($transaction['id']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($transaction['email']); ?></p>
            <p><strong>Nama:</strong> <?php echo htmlspecialchars($transaction['name']); ?></p>
            <p><strong>Alamat:</strong> <?php echo htmlspecialchars($transaction['address']); ?></p>
            <p><strong>Nomor Telepon:</strong> <?php echo htmlspecialchars($transaction['phone']); ?></p>
            <p><strong>Status:</strong> 
                <span class="badge-status sm <?php 
                    if (strcasecmp($transaction['status'], 'Dikonfirmasi') === 0) echo 'badge-dikonfirmasi';
                    elseif (strcasecmp($transaction['status'], 'Tertunda') === 0) echo 'badge-tertunda';
                    elseif (strcasecmp($transaction['status'], 'Gagal') === 0) echo 'badge-gagal';
                ?>">
                    <?php echo htmlspecialchars($transaction['status']); ?>
                </span>
            </p>
            <p><strong>Status Pengiriman:</strong> 
                <span class="badge-status sm <?php 
                    if (strcasecmp($transaction['shipping_status'], 'Dikirim') === 0) echo 'badge-dikirim';
                    elseif (strcasecmp($transaction['shipping_status'], 'Tertunda') === 0) echo 'badge-tertunda';
                ?>">
                    <?php echo htmlspecialchars($transaction['shipping_status']); ?>
                </span>
            </p>
            <p><strong>Total Harga:</strong> Rp <?php echo number_format($transaction['total_price'], 0, ',', '.'); ?></p>
        </div>

        <!-- Item Pesanan -->
        <div class="order-items">
            <h2>Item Pesanan</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Gambar</th>
                        <th>Nama Produk</th>
                        <th>Jumlah</th>
                        <th>Harga</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order_items as $item): ?>
                        <tr>
                            <td><img src="./admin/uploads/<?php echo htmlspecialchars($item['product_image']); ?>" alt="Gambar Produk" class="product-image"></td>
                            <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                            <td>Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Riwayat Transaksi -->
    <div class="transaction-history">
        <h2>Riwayat Transaksi</h2>
        <?php if (!empty($transactions)): ?>
            <table class="transaction-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Produk</th>
                        <th>Jumlah</th>
                        <th>Harga</th>
                        <th>Status</th>
                        <th>Status Pengiriman</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $transaction): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($transaction['created_at']); ?></td>
                            <td><?php echo htmlspecialchars($transaction['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($transaction['quantity']); ?></td>
                            <td>Rp <?php echo number_format($transaction['price'], 0, ',', '.'); ?></td>
                            <td><?php echo htmlspecialchars($transaction['status']); ?></td>
                            <td><?php echo htmlspecialchars($transaction['shipping_status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Belum ada transaksi.</p>
        <?php endif; ?>
    </div>
</div>