<?php
include 'phpconection.php'; // Menghubungkan ke database

// Ambil ID transaksi dari URL
$transaction_id = $_GET['id'] ?? null;
if (!$transaction_id) {
    die('ID Transaksi tidak valid.');
}

// Tangani konfirmasi penerimaan barang
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'confirmReceipt') {
    $transaction_id = $_POST['transaction_id'] ?? 0;
    if ($transaction_id > 0) {
        $query = "UPDATE transactions SET shipping_status = 'Sudah Sampai' WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param('i', $transaction_id);
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Status pengiriman berhasil diperbarui.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui status pengiriman.']);
        }
        exit;
    } else {
        echo json_encode(['status' => 'error', 'message' => 'ID transaksi tidak valid.']);
        exit;
    }
}

// Ambil detail transaksi
$transaction_query = "SELECT * FROM transactions WHERE id = ?";
$transaction_stmt = $db->prepare($transaction_query);
$transaction_stmt->bind_param('i', $transaction_id);
$transaction_stmt->execute();
$transaction_result = $transaction_stmt->get_result();
$transaction = $transaction_result->fetch_assoc();

if (!$transaction) {
    die('Transaksi tidak ditemukan.');
}

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
    $history_query = "SELECT t.id, t.created_at, oi.product_id, p.name AS product_name, oi.quantity, oi.price, t.status, t.shipping_status
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

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Transaksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .product-image {
            width: 100px;
            height: 150px;
            object-fit: cover;
        }
        .badge {
                font-size: 1rem;
                padding: 10px 15px;
                border-radius: 50px;
            }

            .badge-success {
                background-color: #28a745;
                color: white;
            }

            .badge-warning {
                background-color: #ffc107;
                color: black;
            }

            .badge-danger {
                background-color: #dc3545;
                color: white;
            }

            .badge-primary {
                background-color: #007bff;
                color: white;
            }

            .badge-secondary {
                background-color:rgb(9, 143, 32);
                color: white;
            }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="row">
        <!-- Detail Transaksi -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h2>Detail Transaksi</h2>
                </div>
                <div class="card-body">
                    <p><strong>ID Transaksi:</strong> <?php echo htmlspecialchars($transaction['id']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($transaction['email']); ?></p>
                    <p><strong>Nama:</strong> <?php echo htmlspecialchars($transaction['name']); ?></p>
                    <p><strong>Alamat:</strong> <?php echo htmlspecialchars($transaction['address']); ?></p>
                    <p><strong>Nomor Telepon:</strong> <?php echo htmlspecialchars($transaction['phone']); ?></p>
                    <p><strong>Status:</strong> 
                        <span class="badge <?php 
                            if (strcasecmp($transaction['status'], 'Dikonfirmasi') === 0) echo 'badge-success';
                            elseif (strcasecmp($transaction['status'], 'Tertunda') === 0) echo 'badge-warning';
                            elseif (strcasecmp($transaction['status'], 'Dibatalkan') === 0) echo 'badge-danger';
                            else echo 'badge-secondary';
                        ?>">
                            <?php echo htmlspecialchars($transaction['status']); ?>
                        </span>
                    </p>
                    <p><strong>Status Pengiriman:</strong> 
                        <span class="badge <?php 
                            if (strcasecmp($transaction['shipping_status'], 'Dikirim') === 0) echo 'badge-primary';
                            elseif (strcasecmp($transaction['shipping_status'], 'Sudah Sampai') === 0) echo 'badge-success';
                            elseif (strcasecmp($transaction['shipping_status'], 'Tertunda') === 0) echo 'badge-warning';
                            elseif (strcasecmp($transaction['shipping_status'], 'Dibatalkan') === 0) echo 'badge-danger';
                            else echo 'badge-secondary';
                        ?>">
                            <?php echo htmlspecialchars($transaction['shipping_status']); ?>
                        </span>
                    </p>
                    <p><strong>Total Harga:</strong> Rp <?php echo number_format($transaction['total_price'], 0, ',', '.'); ?></p>
                    <p><strong>Hubungi Owner:</strong> 
                        <a href="https://api.whatsapp.com/send?phone=6285261093463&text=Hai%2C%20saya%20mau%20bertanya%20tentang%20transaksi%20dengan%20ID%20<?php echo urlencode($transaction['id']); ?>." 
                        class="btn btn-success" 
                        target="_blank">
                            <i class="bi bi-whatsapp"></i> Chat via WhatsApp
                        </a>
                    </p>
                </div>
            </div>
        </div>

        <!-- Item Pesanan -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h2>Item Pesanan</h2>
                </div>
                <div class="card-body">
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
        </div>
    </div>

    <!-- Riwayat Transaksi -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h2>Riwayat Transaksi</h2>
                </div>
                <div class="card-body">
                    <?php if (!empty($transactions)): ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Produk</th>
                                    <th>Jumlah</th>
                                    <th>Harga</th>
                                    <th>Status</th>
                                    <th>Status Pengiriman</th>
                                    <th>Konfirmasi Barang Sudah Sampai</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($transactions as $trans): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($trans['created_at']); ?></td>
                                        <td><?php echo htmlspecialchars($trans['product_name']); ?></td>
                                        <td><?php echo htmlspecialchars($trans['quantity']); ?></td>
                                        <td>Rp <?php echo number_format($trans['price'], 0, ',', '.'); ?></td>
                                        <td><?php echo htmlspecialchars($trans['status']); ?></td>
                                        <td><?php echo htmlspecialchars($trans['shipping_status']); ?></td>
                                        <td>
                                            <?php if ($trans['shipping_status'] == 'Dikirim'): ?>
                                                <button class="btn btn-success confirm-receipt" data-id="<?php echo $trans['id']; ?>">Konfirmasi Terima</button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>Belum ada transaksi.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('.confirm-receipt').on('click', function () {
            const transactionId = $(this).data('id');

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Anda tidak dapat mengembalikan ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, konfirmasi!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: window.location.href,
                        type: 'POST',
                        data: { action: 'confirmReceipt', transaction_id: transactionId },
                        success: function (response) {
                            const result = JSON.parse(response);
                            if (result.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: result.message,
                                    showConfirmButton: false,
                                    timer: 1500
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: result.message,
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                            }
                        },
                        error: function () {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Terjadi kesalahan saat menghubungi server.',
                                showConfirmButton: false,
                                timer: 1500
                            });
                        }
                    });
                }
            });
        });
    });
</script>
</body>
</html>