<?php
require '../vendor/autoload.php';
include 'phpconection.php';
include 'functions.php';

use Dompdf\Dompdf;
use Dompdf\Options;

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

// Ambil data status konfirmasi
$confirmationStatus = getConfirmationStatus($db, $startDate, $endDate);

// Inisialisasi Dompdf
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

// Buat konten HTML untuk PDF
$html = '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Transaksi</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .section-title { font-size: 14px; font-weight: bold; margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Transaksi</h1>
    </div>
';

// Penjualan Bulanan
$html .= '<div class="section-title">Penjualan Bulanan</div>';
$html .= '<table>';
$html .= '<thead><tr><th>Bulan</th><th>Total Produk Terjual</th><th>Total Pendapatan</th></tr></thead>';
$html .= '<tbody>';
if (!empty($salesData)) {
    foreach ($salesData as $row) {
        $html .= '<tr>';
        $html .= '<td>' . htmlspecialchars($row['month']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['total_quantity']) . '</td>';
        $html .= '<td>Rp ' . number_format($row['total_revenue'], 0, ',', '.') . '</td>';
        $html .= '</tr>';
    }
} else {
    $html .= '<tr><td colspan="3" style="text-align: center;">Tidak ada data</td></tr>';
}
$html .= '</tbody></table>';

// Status Konfirmasi
$html .= '<div class="section-title">Status Konfirmasi</div>';
$html .= '<table>';
$html .= '<thead><tr><th>Status</th><th>Total Transaksi</th><th>Pendapatan</th><th>Total Produk Terjual</th></tr></thead>';
$html .= '<tbody>';
if (!empty($confirmationStatus)) {
    foreach ($confirmationStatus as $row) {
        $html .= '<tr>';
        $html .= '<td>' . htmlspecialchars($row['status']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['total_transactions']) . '</td>';
        $html .= '<td>Rp ' . number_format($row['total_revenue'], 0, ',', '.') . '</td>';
        $html .= '<td>' . htmlspecialchars($row['total_products_sold']) . '</td>';
        $html .= '</tr>';
    }
} else {
    $html .= '<tr><td colspan="4" style="text-align: center;">Tidak ada data</td></tr>';
}
$html .= '</tbody></table>';

// Laporan Per Item
$html .= '<div class="section-title">Laporan Per Item</div>';
$html .= '<table>';
$html .= '<thead><tr><th>ID Produk</th><th>Nama Produk</th><th>Total Terjual</th><th>Total Pendapatan</th></tr></thead>';
$html .= '<tbody>';
if (!empty($productReport)) {
    foreach ($productReport as $row) {
        $html .= '<tr>';
        $html .= '<td>' . htmlspecialchars($row['product_id']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['product_name']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['total_quantity']) . '</td>';
        $html .= '<td>Rp ' . number_format($row['total_revenue'], 0, ',', '.') . '</td>';
        $html .= '</tr>';
    }
} else {
    $html .= '<tr><td colspan="4" style="text-align: center;">Tidak ada data</td></tr>';
}
$html .= '</tbody></table>';
    
// Produk Terlaris
$html .= '<div class="section-title">Produk Terlaris</div>';
$html .= '<table>';
$html .= '<thead><tr><th>Nama Produk</th><th>Total Terjual</th></tr></thead>';
$html .= '<tbody>';
if (!empty($topProducts)) {
    foreach ($topProducts as $row) {
        $html .= '<tr>';
        $html .= '<td>' . htmlspecialchars($row['product_name']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['total_sold']) . '</td>';
        $html .= '</tr>';
    }
} else {
    $html .= '<tr><td colspan="2" style="text-align: center;">Tidak ada data</td></tr>';
}
$html .= '</tbody></table>';

$html .= '</body></html>';

// Load konten HTML ke Dompdf
$dompdf->loadHtml($html);

// (Opsional) Setel ukuran dan orientasi kertas
$dompdf->setPaper('A4', 'portrait');

// Render PDF
$dompdf->render();

// Output PDF ke browser
$dompdf->stream('laporan_transaksi.pdf', array("Attachment" => false));
?>