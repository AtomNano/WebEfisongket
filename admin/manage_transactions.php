<?php
include 'phpconection.php'; // Menghubungkan ke database

// Ambil semua transaksi yang tidak dihapus dari database, mengurutkan berdasarkan created_at
$result = mysqli_query($db, "SELECT * FROM transactions WHERE deleted = 0 ORDER BY created_at DESC");

// Periksa apakah query berhasil
if (!$result) {
    die("Query gagal: " . mysqli_error($db));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Transaksi - Efi Songket</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Roboto', sans-serif;
        }
        .container {
            margin-top: 50px;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            color: white;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }
        .table {
            margin-top: 20px;
            border-collapse: separate;
            border-spacing: 0 15px;
        }
        .table th, .table td {
            vertical-align: middle;
            padding: 12px 15px;
            background: #fff;
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .table th {
            background-color: #f8f9fa;
            color: #333;
            font-weight: bold;
        }
        .table tbody tr:hover {
            background-color: #f1f1f1;
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
        .btn {
            border-radius: 50px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header text-center">
                <h2 class="mb-0">Status Transaksi</h2>
            </div>
            <div class="card-body">
                <table id="transactionsTable" class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID Transaksi</th>
                            <th>Email</th>
                            <th>Total Pembayaran</th>
                            <th>Status</th>
                            <th>Status Pengiriman</th>
                            <th>Bukti Pembayaran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo $row['email']; ?></td>
                                <td>Rp <?php echo number_format($row['total_price'], 0, ',', '.'); ?></td>
                                <td>
                                    <span class="badge <?php echo ($row['status'] == 'Tertunda') ? 'badge-warning' : 'badge-success'; ?>">
                                        <?php echo $row['status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge <?php echo ($row['shipping_status'] == 'Tertunda') ? 'badge-warning' : ($row['shipping_status'] == 'Dikirim' ? 'badge-success' : 'badge-secondary'); ?>">
                                        <?php echo $row['shipping_status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($row['payment_proof']): ?>
                                        <a href="http://localhost/WEBEFISONGKET/<?php echo $row['payment_proof']; ?>" target="_blank" class="btn btn-primary btn-sm">
                                            Lihat Bukti
                                        </a>
                                    <?php else: ?>
                                        Tidak ada bukti
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <!-- Tombol Edit untuk status transaksi -->
                                    <a href="process_transaction.php?proses=edit&id=<?php echo $row['id']; ?>" class="btn btn-info btn-sm">
                                        Konfirmasi
                                    </a>
                                    <!-- Tombol Hapus untuk menghapus transaksi -->
                                    <button class="btn btn-danger btn-sm" onclick="confirmDelete(<?php echo $row['id']; ?>)">
                                        Hapus
                                    </button>
                                    <!-- Tombol Lihat Detail -->
                                    <a href="index.php?p=detail_transaksi&id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">
                                        Lihat Detail
                                    </a>
                                    <!-- Tombol Ubah Status Pengiriman -->
                                    <button class="btn btn-secondary btn-sm m-1" onclick="updateShippingStatus(<?php echo $row['id']; ?>, '<?php echo $row['shipping_status']; ?>')">
                                        Ubah Status Pengiriman
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    $(document).ready(function() {
        $('#transactionsTable').DataTable();
    });

    function confirmDelete(transactionId) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: 'Transaksi akan dihapus sementara dan tidak akan muncul di web!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'process_transaction.php?proses=delete&id=' + transactionId;
            }
        });
    }

    function updateShippingStatus(transactionId, currentStatus) {
        const newStatus = currentStatus === 'Tertunda' ? 'Dikirim' : 'Tertunda';
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: 'Anda akan mengubah status pengiriman!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, ubah!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'process_transaction.php?proses=update_shipping_status&id=' + transactionId + '&status=' + newStatus;
            }
        });
    }

    // Menampilkan pesan sukses atau gagal setelah mengedit atau menghapus transaksi
    <?php if (isset($_SESSION['transaction_message'])): ?>
        Swal.fire({
            icon: '<?php echo $_SESSION['transaction_message']['type']; ?>',
            title: '<?php echo $_SESSION['transaction_message']['title']; ?>',
            text: '<?php echo $_SESSION['transaction_message']['text']; ?>',
            showConfirmButton: false,
            timer: 1500
        });
        <?php unset($_SESSION['transaction_message']); ?>
    <?php endif; ?>
    </script>
</body>
</html>