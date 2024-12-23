<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


// Konfigurasi database
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'efisongket';

// Koneksi menggunakan OOP mysqli
$db = new mysqli($host, $username, $password, $database);

// Periksa koneksi
if ($db->connect_error) {
    die('Koneksi ke database gagal: ' . $db->connect_error);
}
?>
