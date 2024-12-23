<?php
include 'phpconection.php'; // Menghubungkan ke database


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
            echo "<script>alert('Status transaksi berhasil diubah!'); window.location.href='index.php?p=manage_transactions';</script>";
        } else {
            echo "<script>alert('Gagal mengubah status transaksi!'); window.location.href='index.php?p=manage_transactions';</script>";
        }
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

            echo "<script>alert('Status transaksi berhasil Dihapus!'); window.location.href='index.php?p=manage_transactions';</script>";
        }
    }

    // Jika proses bukan 'edit' atau 'delete'
    else {
        echo "<script>alert('Proses tidak valid!'); window.location.href='index.php?p=manage_transactions';</script>";
    }
}
?>
