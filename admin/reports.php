<?php
// Koneksi ke database
include 'phpconection.php'; // Pastikan koneksi ke database sudah benar di file ini
include 'functions.php'; // Sertakan file functions.php

// Ambil transaksi bulan ini
$month = isset($_GET['month']) ? $_GET['month'] : date('m');
$year = isset($_GET['year']) ? $_GET['year'] : date('Y');

// Ambil data transaksi bulanan
$monthlyTransactions = getMonthlyTransactions($month, $year, $db);

// Periode laporan per produk
$startDate = '2024-01-01'; // Sesuaikan
$endDate = '2024-12-31';   // Sesuaikan

// Ambil data laporan
$productReport = getTransactionReport($db, $startDate, $endDate);
$topProducts = getTopSellingProducts($db);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Transaksi</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="dashboard.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center mb-4">Laporan Transaksi</h1> 

    <!-- Monthly Transactions Section -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="card-title text-center mb-4">Transaksi Bulanan</h5>
            <div class="row align-items-center">
                <!-- Chart -->
                <div class="col-md-6">
                    <canvas id="monthlyTransactionsChart" style="max-height: 300px;"></canvas>
                </div>
                <!-- Table -->
                <div class="col-md-6">
                    <table class="table table-striped table-hover">
                        <thead class="table-primary">
                            <tr>
                                <th>Status</th>
                                <th>Total Transaksi</th>
                                <th>Pendapatan</th>
                                <th>Total Produk Terjual</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        if (is_array($monthlyTransactions) && !empty($monthlyTransactions)) {
                            usort($monthlyTransactions, function($a, $b) {
                                return $b['total_revenue'] - $a['total_revenue']; // Mengurutkan berdasarkan pendapatan
                            });

                            foreach ($monthlyTransactions as $transaction): ?>
                                <tr>
                                    <td><?= htmlspecialchars($transaction['status']) ?></td>
                                    <td><?= $transaction['total_transactions'] ?></td>
                                    <td>Rp <?= number_format($transaction['total_revenue'], 0, ',', '.') ?></td>
                                    <td><?= $transaction['total_products_sold'] ?></td>
                                </tr>
                            <?php endforeach;
                        } else {
                            echo '<tr><td colspan="4">Tidak ada data transaksi untuk bulan ini.</td></tr>';
                        }
                        ?>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Filter Form Below Table and Chart -->
<div class="card-footer">
    <form method="GET" action="reports.php" class="row">
        <div class="col-md-4">
            <label for="month" class="form-label">Pilih Bulan</label>
            <select name="month" id="month" class="form-select">
                <?php for ($i = 1; $i <= 12; $i++): ?>
                    <option value="<?= $i ?>" <?= $month == $i ? 'selected' : '' ?>>
                        <?= date("F", mktime(0, 0, 0, $i, 10)) ?>
                    </option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label for="year" class="form-label">Pilih Tahun</label>
            <select name="year" id="year" class="form-select">
                <option value="2024" <?= $year == 2024 ? 'selected' : '' ?>>2024</option>
                <option value="2023" <?= $year == 2023 ? 'selected' : '' ?>>2023</option>
                <option value="2022" <?= $year == 2022 ? 'selected' : '' ?>>2022</option>
            </select>
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Tampilkan Laporan</button>
        </div>
    </form>
</div>
    </div>
</div>

<script>
// Monthly Transactions Chart
const monthlyTransactionsData = <?php echo json_encode($monthlyTransactions); ?>;
const labels = monthlyTransactionsData.map(transaction => transaction.status);
const totalTransactions = monthlyTransactionsData.map(transaction => transaction.total_transactions);

const ctxMonthly = document.getElementById('monthlyTransactionsChart').getContext('2d');
new Chart(ctxMonthly, {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'Total Transaksi',
            data: totalTransactions,
            backgroundColor: 'rgba(54, 162, 235, 0.6)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            }
        }
    }
});
</script>

<!-- Report Per Item Section -->
<div class="card mb-4 shadow-sm">
    <div class="card-body">
        <h5 class="card-title text-center mb-4">Laporan Per Item</h5>
        <table class="table table-bordered table-sm">
            <thead class="table-info">
            <tr>
                <th>ID Produk</th>
                <th>Nama Produk</th>
                <th>Total Terjual</th>
                <th>Total Pendapatan</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($productReport as $product): ?>
                <tr>
                    <td><?= htmlspecialchars($product['product_id']) ?></td>
                    <td><?= htmlspecialchars($product['product_name']) ?></td>
                    <td><?= $product['total_quantity'] ?></td>
                    <td>Rp <?= number_format($product['total_revenue'], 0, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Top Selling Products Section -->
<div class="card mb-4 shadow-sm">
    <div class="card-body">
        <h5 class="card-title text-center mb-4">Produk Terlaris</h5>
        <div class="row">
            <!-- Chart -->
            <div class="col-md-6">
                <div style="max-width: 400px; margin: 0 auto;">
                    <canvas id="topSellingProductsChart" style="max-height: 250px;"></canvas>
                </div>
            </div>
            <!-- Table -->
            <div class="col-md-6">
                <table class="table table-bordered table-striped">
                    <thead class="table-warning text-center">
                        <tr>
                            <th>Nama Produk</th>
                            <th>Total Terjual</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($topProducts as $product): ?>
                            <tr>
                                <td><?= htmlspecialchars($product['product_name']) ?></td>
                                <td class="text-center"><?= number_format($product['total_sold'], 0, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
// Top Selling Products Chart
const topProductsData = <?php echo json_encode($topProducts); ?>;
const productLabels = topProductsData.map(product => product['product_name']);
const productSales = topProductsData.map(product => product['total_sold']);

const ctxTopProducts = document.getElementById('topSellingProductsChart').getContext('2d');
new Chart(ctxTopProducts, {
    type: 'pie',
    data: {
        labels: productLabels,
        datasets: [{
            data: productSales,
            backgroundColor: [
                'rgba(255, 99, 132, 0.6)',
                'rgba(54, 162, 235, 0.6)',
                'rgba(255, 206, 86, 0.6)',
                'rgba(75, 192, 192, 0.6)',
                'rgba(153, 102, 255, 0.6)',
                'rgba(255, 159, 64, 0.6)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'right',
            },
            tooltip: {
                callbacks: {
                    label: function(tooltipItem) {
                        return `${tooltipItem.label}: ${tooltipItem.raw.toLocaleString()} pcs`;
                    }
                }
            }
        }
    }
});
</script>
</body>
</html>