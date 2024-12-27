<?php
require('library/fpdf.php');
include 'phpconection.php';
include 'functions.php';

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

class PDF extends FPDF
{
    // Page header
    function Header()
    {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'Laporan Transaksi', 0, 1, 'C');
        $this->Ln(10);
    }

    // Page footer
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Halaman ' . $this->PageNo(), 0, 0, 'C');
    }

    // Monthly Transactions Table
    function MonthlyTransactionsTable($header, $data)
    {
        $this->SetFont('Arial', 'B', 12);
        foreach ($header as $col) {
            $this->Cell(60, 7, $col, 1);
        }
        $this->Ln();
        $this->SetFont('Arial', '', 12);
        if (!empty($data)) {
            foreach ($data as $row) {
                $this->Cell(60, 6, isset($row['month']) ? $row['month'] : '', 1);
                $this->Cell(60, 6, isset($row['total_quantity']) ? $row['total_quantity'] : '', 1);
                $this->Cell(60, 6, isset($row['total_revenue']) ? 'Rp ' . number_format($row['total_revenue'], 0, ',', '.') : '', 1);
                $this->Ln();
            }
        } else {
            $this->Cell(180, 6, 'Tidak ada data', 1, 1, 'C');
        }
    }

    // Confirmation Status Table
    function ConfirmationStatusTable($header, $data)
    {
        $this->SetFont('Arial', 'B', 12);
        foreach ($header as $col) {
            $this->Cell(45, 7, $col, 1);
        }
        $this->Ln();
        $this->SetFont('Arial', '', 12);
        if (!empty($data)) {
            foreach ($data as $row) {
                $this->Cell(45, 6, isset($row['status']) ? $row['status'] : '', 1);
                $this->Cell(45, 6, isset($row['total_transactions']) ? $row['total_transactions'] : '', 1);
                $this->Cell(45, 6, isset($row['total_revenue']) ? 'Rp ' . number_format($row['total_revenue'], 0, ',', '.') : '', 1);
                $this->Cell(45, 6, isset($row['total_products_sold']) ? $row['total_products_sold'] : '', 1);
                $this->Ln();
            }
        } else {
            $this->Cell(180, 6, 'Tidak ada data', 1, 1, 'C');
        }
    }

    // Product Report Table
    function ProductReportTable($data)
    {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(24, 7, 'ID Produk', 1);
        $this->Cell(90, 7, 'Nama Produk', 1);
        $this->Cell(30, 7, 'Total Terjual', 1);
        $this->Cell(40, 7, 'Total Pendapatan', 1);
        $this->Ln();
        $this->SetFont('Arial', '', 12);
        if (!empty($data)) {
            foreach ($data as $row) {
                $this->Cell(24, 6, isset($row['product_id']) ? $row['product_id'] : '', 1);
                $productName = isset($row['product_name']) ? $row['product_name'] : '';
                if (strlen($productName) > 50) {
                    $productName = substr($productName, 0, 40) . '...';
                }
                $this->Cell(90, 6, $productName, 1);
                $this->Cell(30, 6, isset($row['total_quantity']) ? $row['total_quantity'] : '', 1);
                $this->Cell(40, 6, isset($row['total_revenue']) ? 'Rp ' . number_format($row['total_revenue'], 0, ',', '.') : '', 1);
                $this->Ln();
            }
        } else {
            $this->Cell(24, 6, 'Tidak ada data', 1, 1, 'C');
        }
    }

    // Top Selling Products Table
    function TopProductsTable($header, $data)
    {
        $this->SetFont('Arial', 'B', 12);
        foreach ($header as $col) {
            $this->Cell(90, 7, $col, 1);
        }
        $this->Ln();
        $this->SetFont('Arial', '', 12);
        if (!empty($data)) {
            foreach ($data as $row) {
                $this->Cell(90, 6, isset($row['product_name']) ? $row['product_name'] : '', 1);
                $this->Cell(90, 6, isset($row['total_sold']) ? $row['total_sold'] : '', 1);
                $this->Ln();
            }
        } else {
            $this->Cell(180, 6, 'Tidak ada data', 1, 1, 'C');
        }
    }

    // Section Title
    function SectionTitle($title)
    {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, $title, 0, 1, 'L');
        $this->Ln(5);
    }
}

$pdf = new PDF();
$pdf->AddPage();

// Penjualan Bulanan
$pdf->SectionTitle('Penjualan Bulanan');
$header = array('Bulan', 'Total Produk Terjual', 'Total Pendapatan');
$pdf->MonthlyTransactionsTable($header, $salesData);

// Status Konfirmasi
$pdf->AddPage();
$pdf->SectionTitle('Status Konfirmasi');
$header = array('Status', 'Total Transaksi', 'Pendapatan', 'Total Produk Terjual');
$pdf->ConfirmationStatusTable($header, $confirmationStatus);

// Laporan Per Item
$pdf->AddPage();
$pdf->SectionTitle('Laporan Per Item');
$pdf->ProductReportTable($productReport);

// Produk Terlaris
$pdf->AddPage();
$pdf->SectionTitle('Produk Terlaris');
$header = array(
    'Nama Produk', 
    'Total Terjual'
);
$pdf->TopProductsTable($header, $topProducts);

$pdf->Output();
?>