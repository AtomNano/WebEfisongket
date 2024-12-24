<?php
include 'phpconection.php'; // Menghubungkan ke database

session_start();

if (isset($_GET['proses'])) {
    $proses = $_GET['proses'];
    $transaction_id = $_GET['id'];

    // Edit Status Transaksi
    if ($proses == 'edit') {
        $status = 'Confirmed';  // Status yang akan diubah, misalnya 'Confirmed'

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
                'text' => 'Gagal mengubah status transaksi!'
            ];
        }
        header('Location: index.php?p=manage_transactions');
        exit();
    }

    // Hapus Transaksi
    elseif ($proses == 'delete') {
        $delete_order_item_query = "DELETE FROM order_item WHERE transaction_id = ?";
        $stmt_order_item = mysqli_prepare($db, $delete_order_item_query);
        mysqli_stmt_bind_param($stmt_order_item, 'i', $transaction_id);
        $delete_order_item_result = mysqli_stmt_execute($stmt_order_item);

        if ($delete_order_item_result) {
            $delete_query = "DELETE FROM transactions WHERE id = ?";
            $stmt = mysqli_prepare($db, $delete_query);
            mysqli_stmt_bind_param($stmt, 'i', $transaction_id);
            $delete_result = mysqli_stmt_execute($stmt);

            if ($delete_result) {
                $_SESSION['transaction_message'] = [
                    'type' => 'success',
                    'title' => 'Berhasil!',
                    'text' => 'Transaksi berhasil dihapus!'
                ];
            } else {
                $_SESSION['transaction_message'] = [
                    'type' => 'error',
                    'title' => 'Gagal!',
                    'text' => 'Gagal menghapus transaksi!'
                ];
            }
        } else {
            $_SESSION['transaction_message'] = [
                'type' => 'error',
                'title' => 'Gagal!',
                'text' => 'Gagal menghapus item transaksi!'
            ];
        }
        header('Location: index.php?p=manage_transactions');
        exit();
    }

    // Jika proses bukan 'edit' atau 'delete'
    else {
        $_SESSION['transaction_message'] = [
            'type' => 'error',
            'title' => 'Gagal!',
            'text' => 'Proses tidak valid!'
        ];
        header('Location: index.php?p=manage_transactions');
        exit();
    }
}
?>