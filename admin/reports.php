<?php
// Koneksi ke database
include 'phpconection.php'; // Pastikan koneksi ke database sudah benar di file ini
include 'functions.php'; // Sertakan file functions.php

// Ambil tanggal mulai dan akhir dari parameter GET
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');

// Ambil data transaksi bulanan
$monthlyTransactions = getMonthlyTransactionsByDateRange($startDate, $endDate, $db);

// Ambil data laporan per produk
$productReport = getTransactionReport($db, $startDate, $endDate);
$topProducts = getTopSellingProducts($db);

// Ambil data penjualan bulanan
$salesData = getMonthlySalesDataByDateRange($startDate, $endDate, $db);
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @media print {
            .no-print {
                display: none;
            }
            .print-container {
                margin: 0;
                padding: 0;
                border: none;
                box-shadow: none;
            }
            .print-container h1, .print-container h5 {
                text-align: center;
            }
            .print-container table {
                width: 100%;
                border-collapse: collapse;
            }
            .print-container table, .print-container th, .print-container td {
                border: 1px solid black;
            }
            .print-container th, .print-container td {
                padding: 8px;
                text-align: left;
            }
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
    </style>
</head>
<body>
<div class="container mt-5 print-container">
    <h1 class="text-center mb-4">Laporan Transaksi</h1> 

    <!-- Filter Form -->
    <div class="card mb-4 shadow-sm no-print">
        <div class="card-body">
            <form method="GET" action="reports.php" class="row">
                <div class="col-md-4">
                    <label for="start_date" class="form-label">Tanggal Mulai</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="<?= $startDate ?>">
                </div>
                <div class="col-md-4">
                    <label for="end_date" class="form-label">Tanggal Akhir</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="<?= $endDate ?>">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Tampilkan Laporan</button>
                </div>
            </form>
        </div>
    </div>

    <div class="text-end mb-4 no-print">
    <a href="print_report.php?start_date=<?= $startDate ?>&end_date=<?= $endDate ?>" class="btn btn-success" target="_blank">
        <i class="bi bi-printer"></i> Cetak Laporan
    </a>
    </div>

    <!-- Monthly Sales Section -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="card-title text-center mb-4">Penjualan Bulanan</h5>
            <div class="row align-items-center">
                <!-- Chart for Total Products Sold -->
                <div class="col-md-6">
                    <canvas id="totalProductsSoldChart" style="max-height: 300px;"></canvas>
                </div>
                <!-- Chart for Total Revenue -->
                <div class="col-md-6">
                    <canvas id="totalRevenueChart" style="max-height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Status Section -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="card-title text-center mb-4">Status Konfirmasi</h5>
            <div class="row align-items-center">
                <!-- Chart -->
                <div class="col-md-6">
                    <canvas id="confirmationStatusChart" style="max-height: 300px;"></canvas>
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
    </div>

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
</div>

<script>
// Monthly Sales Data
const salesData = <?php echo json_encode($salesData); ?>;
const salesLabels = salesData.map(data => data.month);
const salesQuantities = salesData.map(data => data.total_quantity);
const salesRevenues = salesData.map(data => data.total_revenue);

// Total Products Sold Chart
const ctxTotalProductsSold = document.getElementById('totalProductsSoldChart').getContext('2d');
new Chart(ctxTotalProductsSold, {
    type: 'bar',
    data: {
        labels: salesLabels,
        datasets: [{
            label: 'Total Produk Terjual',
            data: salesQuantities,
            backgroundColor: 'rgba(75, 192, 192, 0.6)',
            borderColor: 'rgba(75, 192, 192, 1)',
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

// Total Revenue Chart
const ctxTotalRevenue = document.getElementById('totalRevenueChart').getContext('2d');
new Chart(ctxTotalRevenue, {
    type: 'bar',
    data: {
        labels: salesLabels,
        datasets: [{
            label: 'Total Pendapatan',
            data: salesRevenues,
            backgroundColor: 'rgba(153, 102, 255, 0.6)',
            borderColor: 'rgba(153, 102, 255, 1)',
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

// Confirmation Status Chart
const monthlyTransactionsData = <?php echo json_encode($monthlyTransactions); ?>;
const confirmationLabels = monthlyTransactionsData.map(transaction => transaction.status);
const totalTransactions = monthlyTransactionsData.map(transaction => transaction.total_transactions);

const ctxConfirmation = document.getElementById('confirmationStatusChart').getContext('2d');
new Chart(ctxConfirmation, {
    type: 'pie',
    data: {
        labels: confirmationLabels,
        datasets: [{
            data: totalTransactions,
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