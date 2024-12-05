<?php
session_start();
include 'phpconection.php'; // Menghubungkan ke database

// Periksa koneksi database
if (!$db) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Ambil data jumlah produk untuk dashboard
$result_products = mysqli_query($db, "SELECT COUNT(*) AS total FROM products");
$total_products = ($result_products && mysqli_num_rows($result_products) > 0) ? mysqli_fetch_assoc($result_products)['total'] : 0;

// // Ambil data jumlah transaksi untuk dashboard
// $result_transactions = mysqli_query($db, "SELECT COUNT(*) AS total FROM transactions");
// $total_transactions = ($result_transactions && mysqli_num_rows($result_transactions) > 0) ? mysqli_fetch_assoc($result_transactions)['total'] : 0;

// // Ambil pendapatan bulanan untuk dashboard
// $result_revenue = mysqli_query($db, "SELECT SUM(amount) AS revenue FROM transactions WHERE MONTH(date) = MONTH(CURRENT_DATE())");
// $monthly_revenue = ($result_revenue && mysqli_num_rows($result_revenue) > 0) ? mysqli_fetch_assoc($result_revenue)['revenue'] : 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Efi Songket</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #FFFFFF; color: black; }
        .sidebar { width: 250px; height: 100vh; background-color: #F1F1F0; position: fixed; top: 0; left: 0; padding: 20px 10px; overflow-y: auto; }
        .sidebar a { text-decoration: none; color: black; font-size: 1rem; display: block; padding: 10px; border-radius: 5px; margin-bottom: 10px; transition: background-color 0.3s; }
        .sidebar a:hover { background-color: #DDD; }
        .sidebar a.active { background-color: #F1C40F; color: black; font-weight: bold; }
        .content { margin-left: 250px; padding: 20px; }
        .card { background-color: #F1F1F0; border: 1px solid #DDD; }
        .card-body { color: black; }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h3 class="text-warning text-center">Admin Panel</h3>
        <hr>
        <?php
        $page = isset($_GET['p']) ? $_GET['p'] : 'dashboard';
        ?>
        <a href="index.php?p=dashboard" class="<?= $page == 'dashboard' ? 'active' : '' ?>"><i class="bi bi-house-door me-2"></i>Dashboard</a>
        <a href="index.php?p=manage_product" class="<?= $page == 'manage_product' ? 'active' : '' ?>"><i class="bi bi-box-seam me-2"></i>Manage Products</a>
        <a href="index.php?p=manage_transactions" class="<?= $page == 'manage_transactions' ? 'active' : '' ?>"><i class="bi bi-cash-stack me-2"></i>Manage Transactions</a>
        <a href="index.php?p=reports" class="<?= $page == 'reports' ? 'active' : '' ?>"><i class="bi bi-graph-up-arrow me-2"></i>Reports</a>
        <a href="../index.php"><i class="bi bi-box-arrow-left me-2"></i>Logout</a>
    </div>

    <!-- Main Content -->
    <div class="content">
        <?php
        switch ($page) {
            case 'dashboard':
                ?>
                <h2 class="text-warning">Dashboard</h2>
                <p>Selamat datang di dashboard admin Efi Songket. Berikut adalah ringkasan informasi toko:</p>
                <div class="row g-4 mt-4">
                    <div class="col-md-4">
                        <div class="card p-4 text-center bg-light">
                            <i class="bi bi-box-seam display-4 text-warning"></i>
                            <h5 class="mt-3 text-dark">Total Produk</h5>
                            <p class="text-muted"><?= $total_products ?> Produk</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card p-4 text-center bg-light">
                            <i class="bi bi-cash-stack display-4 text-warning"></i>
                            <h5 class="mt-3 text-dark">Transaksi</h5>
                            <p class="text-muted"><?= $total_transactions ?> Transaksi</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card p-4 text-center bg-light">
                            <i class="bi bi-graph-up-arrow display-4 text-warning"></i>
                            <h5 class="mt-3 text-dark">Pendapatan Bulanan</h5>
                            <p class="text-muted">Rp <?= number_format($monthly_revenue, 0, ',', '.') ?></p>
                        </div>
                    </div>
                </div>
                <?php
                break;
            case 'manage_product':
                include 'manage_product.php';  // Memasukkan file manage_product.php
                break;
            case 'manage_transactions':
                include 'manage_transactions.php';  // Memasukkan file manage_transactions.php
                break;
            case 'reports':
                include 'reports.php';  // Memasukkan file reports.php
                break;
            default:
                echo "<p class='text-center'>Halaman tidak ditemukan.</p>";
        }
        ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
</body>
</html>
