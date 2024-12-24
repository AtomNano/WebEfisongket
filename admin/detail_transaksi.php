<?php
include 'phpconection.php'; // Menghubungkan ke database

// Ambil ID transaksi dari query parameter
if (isset($_GET['id'])) {
    $transactionId = $_GET['id'];
} else {
    echo "Error: ID transaksi tidak ditemukan.";
    exit();
}

// Ambil detail transaksi berdasarkan ID dari database
$query = "SELECT * FROM transactions WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param('i', $transactionId);
$stmt->execute();
$result = $stmt->get_result();

// Cek apakah transaksi ditemukan
if ($result->num_rows == 0) {
    echo "Transaksi dengan ID $transactionId tidak ditemukan.";
    exit();
}

$transaction = $result->fetch_assoc(); // Ambil hasil transaksi sebagai array

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Transaksi - Efi Songket</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(120deg, #fdfbfb, #ebedee);
            font-family: 'Roboto', sans-serif;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }
        .card-header {
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            color: white;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }
        .card-footer {
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            border-bottom-left-radius: 15px;
            border-bottom-right-radius: 15px;
        }
        .badge-warning {
            background-color: #ffc107;
        }
        .badge-success {
            background-color: #28a745;
        }
        .badge-danger {
            background-color: #dc3545;
        }
        .badge-secondary {
            background-color: #6c757d;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .table th {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="card shadow-lg rounded">
        <div class="card-header text-center">
            <h4 class="mb-0">Detail Transaksi</h4>
        </div>
        <div class="card-body">
            <!-- Tabel untuk menampilkan detail transaksi -->
            <table class="table table-bordered table-striped table-hover rounded">
                <tr>
                    <th>ID Transaksi</th>
                    <td><?php echo htmlspecialchars($transaction['id']); ?></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><?php echo htmlspecialchars($transaction['email']); ?></td>
                </tr>
                <tr>
                    <th>Nama</th>
                    <td><?php echo htmlspecialchars($transaction['name']); ?></td>
                </tr>
                <tr>
                    <th>Alamat</th>
                    <td><?php echo htmlspecialchars($transaction['address']) ?: '-'; ?></td>
                </tr>
                <tr>
                    <th>No. Telp</th>
                    <td><?php echo htmlspecialchars($transaction['phone']); ?></td>
                </tr>
                <tr>
                    <th>Total Pembayaran</th>
                    <td>Rp <?php 
                        // Menghapus format desimal dan hanya menampilkan angka bulat
                        $total = str_replace(['Rp', ''], '', $transaction['total_price']);
                        echo number_format((float)$total, 0, ',', '.'); // Menampilkan tanpa 2 angka desimal
                    ?></td>
                </tr>
                <tr>
                    <th>Bukti Pembayaran</th>
                    <td>
                    <?php if (!empty($transaction['payment_proof'])): ?>
                            <img src="http://localhost/WEBEFISONGKET/<?php echo htmlspecialchars($transaction['payment_proof']); ?>" alt="Bukti Pembayaran" style="max-width: 200px;">
                        <?php else: ?>
                            - 
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                    <span class="badge 
                        <?php 
                            if ($transaction['status'] == 'Pending') {
                                echo 'badge-warning'; 
                            } elseif ($transaction['status'] == 'Completed') {
                                echo 'badge-success'; 
                            } elseif ($transaction['status'] == 'Cancelled') {
                                echo 'badge-danger'; 
                            } else {
                                echo 'badge-secondary'; // Default for other statuses
                            }
                        ?>">
                        <?php echo htmlspecialchars($transaction['status']); ?>
                    </span>
                    </td>
                </tr>
                <tr>
                    <th>Tanggal Dibuat</th>
                    <td><?php echo htmlspecialchars($transaction['created_at']); ?></td>
                </tr>
            </table>
        
            <!-- Tabel untuk menampilkan order items -->
            <h5 class="mt-4">Order Items</h5>
            <table class="table table-bordered table-striped table-hover rounded">
                <thead>
                    <tr>
                        <th>Nama Produk</th>
                        <th>Jumlah</th>
                        <th>Harga</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Ambil data order item berdasarkan transaction_id
                    $order_query = "SELECT oi.product_id, oi.quantity, oi.price, p.name FROM order_item oi 
                                    JOIN products p ON oi.product_id = p.id 
                                    WHERE oi.transaction_id = ?";
                    $stmt_order = $db->prepare($order_query);
                    $stmt_order->bind_param('i', $transaction['id']);
                    $stmt_order->execute();
                    $result_order = $stmt_order->get_result();

                    while ($item = $result_order->fetch_assoc()) {
                        echo "<tr>
                                <td>" . htmlspecialchars($item['name']) . "</td>
                                <td>" . htmlspecialchars($item['quantity']) . "</td>
                                <td>Rp " . number_format($item['price'], 0, ',', '.') . "</td>
                                <td>Rp " . number_format($item['quantity'] * $item['price'], 0, ',', '.') . "</td>
                            </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer text-center">
            <a href="index.php?p=manage_transactions" class="btn btn-primary">Kembali ke Beranda</a>
        </div>
    </div>
</div>
</body>
</html>