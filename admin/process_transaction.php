<?php
include 'phpconection.php'; // Menghubungkan ke database

session_start();

if (isset($_GET['proses'])) {
    $proses = $_GET['proses'];
    $transaction_id = $_GET['id'];

    // Edit Status Pengiriman
    if ($proses == 'update_shipping_status') {
        $status = $_GET['status'];
        $query = "UPDATE transactions SET shipping_status = '$status' WHERE id = $transaction_id";
        if (mysqli_query($db, $query)) {
            $_SESSION['transaction_message'] = [
                'type' => 'success',
                'title' => 'Berhasil!',
                'text' => 'Status pengiriman berhasil diubah.'
            ];
        } else {
            $_SESSION['transaction_message'] = [
                'type' => 'error',
                'title' => 'Gagal!',
                'text' => 'Status pengiriman gagal diubah.'
            ];
        }
        header('Location: index.php?p=manage_transactions');
        exit;
    }

    // Edit Transaksi Konfirmasi Pembayaran
    if ($proses == 'edit') {
        $status = 'Dikonfirmasi';  // Status yang akan diubah, misalnya 'Confirmed'

        $update_query = "UPDATE transactions SET status = ? WHERE id = ?";
        $stmt = mysqli_prepare($db, $update_query);
        mysqli_stmt_bind_param($stmt, 'si', $status, $transaction_id);
        $update_result = mysqli_stmt_execute($stmt);

        if ($update_result) {
            $_SESSION['transaction_message'] = [
                'type' => 'success',
                'title' => 'Berhasil!',
                'text' => 'Status transaksi berhasil diubah!'
            ];
        } else {
            $_SESSION['transaction_message'] = [
                'type' => 'error',
                'title' => 'Gagal!',
                'text' => 'Status transaksi gagal diubah.'
            ];
        }
        header('Location: index.php?p=manage_transactions');
        exit;
    }

    // Hapus Transaksi (tandai sebagai dihapus)
    if ($proses == 'delete') {
        $query = "UPDATE transactions SET deleted = 1 WHERE id = $transaction_id";
        if (mysqli_query($db, $query)) {
            $_SESSION['transaction_message'] = [
                'type' => 'success',
                'title' => 'Berhasil!',
                'text' => 'Transaksi berhasil dihapus sementara.'
            ];
        } else {
            $_SESSION['transaction_message'] = [
                'type' => 'error',
                'title' => 'Gagal!',
                'text' => 'Transaksi gagal dihapus.'
            ];
        }
        header('Location: index.php?p=manage_transactions');
        exit;
    }
}
?>