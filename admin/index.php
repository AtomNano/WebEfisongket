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

 //Ambil data jumlah transaksi untuk dashboard
 $result_transactions = mysqli_query($db, "SELECT COUNT(*) AS total FROM transactions");
 $total_transactions = ($result_transactions && mysqli_num_rows($result_transactions) > 0) ? mysqli_fetch_assoc($result_transactions)['total'] : 0;

 // Pastikan include 'functions.php' ada di atas
include 'functions.php';


// Ambil bulan dan tahun saat ini
$month = date('m');
$year = date('Y');

// Query untuk menghitung total pendapatan bulan ini
$query = "SELECT SUM(total_price) AS total_revenue FROM transactions WHERE MONTH(created_at) = '$month' AND YEAR(created_at) = '$year'";
$result = mysqli_query($db, $query);
$monthly_revenue = 0;

// Jika query berhasil, ambil hasilnya
if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $monthly_revenue = $row['total_revenue'];
}

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
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap');
        
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(120deg, #fdfbfb, #ebedee);
            color: #333;
            transition: margin-left 0.3s;
        }

        /* Sidebar */
        .sidebar {
            width: 100px; /* Initial small width */
            height: 100vh;
            background-color: #343A40;
            position: fixed;
            top: 0;
            left: 0;
            padding: 20px;
            overflow-y: auto;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            transition: width 0.3s ease; /* Animasi ketika sidebar diubah lebarnya */
        }

        /* When Sidebar is hovered or active */
        .sidebar:hover {
            width: 250px;
        }

        /* Sidebar Item */
        .sidebar-item {
            text-decoration: none;
            color: #D1D1D1;
            font-size: 1rem;
            padding: 12px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            transition: all 0.3s;
            margin-bottom: 15px;
        }

        /* Icon in Sidebar Item */
        .sidebar-item i {
            font-size: 1.5rem;
            margin-right: 10px;
        }

        /* When Sidebar Item is hovered */
        .sidebar-item:hover {
            background-color: #495057;
            color: #FFF;
        }

        /* Active Sidebar Item */
        .sidebar-item.active {
            background-color: #FFC107;
            color: #343A40;
            font-weight: bold;
        }

        /* Text in Sidebar Item */
        .sidebar-text {
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0s 0.3s;
        }

        /* Show text when sidebar is hovered */
        .sidebar:hover .sidebar-text {
            opacity: 1;
            visibility: visible;
            transition: opacity 0.3s ease, visibility 0s 0s;
        }

        /* Content */
        .content {
            margin-left: 100px;
            padding: 40px;
            transition: margin-left 0.3s;
        }

        /* When Sidebar is active */
        .sidebar:hover ~ .content {
            margin-left: 250px;
        }

        /* Card Styles */
        .card {
            border: none;
            border-radius: 15px;
            background: linear-gradient(135deg, #e9efff, #e0f7fa);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.25);
        }

        .card-icon {
            font-size: 3rem;
            margin-bottom: 15px;
        }

        .bg-products {
            background: linear-gradient(135deg, #FFD54F, #FFC107);
            color: #333;
        }

        .bg-transactions {
            background: linear-gradient(135deg, #81C784, #4CAF50);
            color: white;
        }

        .bg-revenue {
            background: linear-gradient(135deg, #64B5F6, #2196F3);
            color: white;
        }

        .card-title {
            font-weight: bold;
            font-size: 1.4rem;
        }

        .card-text {
            font-size: 1.2rem;
            font-weight: 500;
        }

    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <h3 class="text-center text-warning" style="font-size: 1.25rem; text-transform: uppercase;">Admin Panel</h3>
    <?php $page = isset($_GET['p']) ? $_GET['p'] : 'dashboard'; ?>
    
    <a href="index.php?p=dashboard" class="sidebar-item d-flex align-items-center <?= $page == 'dashboard' ? 'active' : '' ?>">
        <i class="bi bi-house-door"></i>
        <span class="sidebar-text">Dashboard</span>
    </a>
    
    <a href="index.php?p=manage_product" class="sidebar-item d-flex align-items-center <?= $page == 'manage_product' ? 'active' : '' ?>">
        <i class="bi bi-box-seam"></i>
        <span class="sidebar-text">Manage Products</span>
    </a>
    
    <a href="index.php?p=manage_transactions" class="sidebar-item d-flex align-items-center <?= $page == 'manage_transactions' ? 'active' : '' ?>">
        <i class="bi bi-cash-stack"></i>
        <span class="sidebar-text">Manage Transactions</span>
    </a>
    
    <a href="index.php?p=reports" class="sidebar-item d-flex align-items-center <?= $page == 'reports' ? 'active' : '' ?>">
        <i class="bi bi-graph-up-arrow"></i>
        <span class="sidebar-text">Reports</span>
    </a>
    
    <a href="../index.php" class="sidebar-item d-flex align-items-center">
        <i class="bi bi-box-arrow-left"></i>
        <span class="sidebar-text">Logout</span>
    </a>
</div>

<!-- Main Content -->
<div class="content">
    <?php
    switch ($page) {
        case 'dashboard':
            ?>
            <div class="text-center">
                <h2 class="text-warning fw-bold display-4 mb-4" style="font-size: 3rem;">Dashboard</h2>
                <p class="lead text-muted" style="font-size: 1.25rem;">Selamat datang di dashboard admin Efi Songket. Berikut adalah ringkasan informasi toko :</p>
            </div>

            <div class="row g-4 mt-4">
                <div class="col-md-4">
                    <div class="card p-4 text-center bg-products">
                        <i class="bi bi-box-seam card-icon"></i>
                        <h5 class="card-title">Total Produk</h5>
                        <p class="card-text"><?= $total_products ?> Produk</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card p-4 text-center bg-transactions">
                        <i class="bi bi-cash-stack card-icon"></i>
                        <h5 class="card-title">Total Transaksi</h5>
                        <p class="card-text"><?= $total_transactions ?> Transaksi</p>
                    </div>
                </div>
                <div class="col-md-4">
    <div class="card p-4 text-center bg-revenue">
        <i class="bi bi-graph-up-arrow card-icon"></i>
        <h5 class="card-title">Pendapatan Bulanan</h5>
        <p class="card-text">
            <?php 
            if (isset($monthly_revenue) && is_numeric($monthly_revenue)) {
                echo 'Rp ' . number_format($monthly_revenue, 0, ',', '.');
            } else {
                echo 'Data tidak tersedia';
            }
            ?>
        </p>
    </div>
</div>
            </div>
            <?php
            break;
        case 'manage_product':
            include 'manage_product.php';
            break;
        case 'manage_transactions':
            include 'manage_transactions.php';
            break;
        case 'reports':
            include 'reports.php';
            break;
        default:
            echo "<p class='text-center text-danger'>Halaman tidak ditemukan.</p>";
    }
    ?>
</div>

<!-- Optional JavaScript (Bootstrap and jQuery) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
</body>
</html>