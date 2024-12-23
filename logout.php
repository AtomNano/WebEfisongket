<?php
session_start();
include 'phpconection.php';

// Cek apakah pengguna sudah login
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Simpan waktu logout ke database
    $current_time = date('Y-m-d H:i:s');
    $update_query = "UPDATE user SET last_logout='$current_time' WHERE id=$user_id";
    mysqli_query($db, $update_query);

    session_start();  // Pastikan session dimulai jika belum ada
    session_unset();  // Menghapus semua data sesi
    session_destroy();  // Menghancurkan sesi
    header("Location: index.php?p=home");  // Arahkan kembali ke halaman utama
    exit;
}
?>
