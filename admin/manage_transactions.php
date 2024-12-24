<?php
include 'phpconection.php'; // Menghubungkan ke database

// Ambil semua transaksi dari database, mengurutkan berdasarkan created_at
$result = mysqli_query($db, "SELECT * FROM transactions ORDER BY created_at DESC");

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
    <title>Manage Transactions - Efi Songket</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Daftar Transaksi</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID Transaksi</th>
                    <th>Email</th>
                    <th>Total Pembayaran</th>
                    <th>Status</th>
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
                            <span class="badge <?php echo ($row['status'] == 'Pending') ? 'bg-warning' : 'bg-success'; ?>">
                                <?php echo $row['status']; ?>
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
                            <a href="process_transaction.php?proses=delete&id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?');">
                                Hapus
                            </a>
                            <!-- Tombol Lihat Detail -->
                            <a href="index.php?p=detail_transaksi&id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">
                                Lihat Detail
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
