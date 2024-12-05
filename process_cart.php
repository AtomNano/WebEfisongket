<?php
session_start();
include 'phpconection.php';  // Pastikan koneksi database sudah dilakukan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Cek apakah keranjang sudah ada, jika belum buat array keranjang baru
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }

    // Ambil detail produk dari database untuk menambahkan ke keranjang
    $stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $product = $result->fetch_assoc();

        // Cek apakah produk sudah ada di dalam keranjang
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] += $quantity; // Update quantity jika produk sudah ada
        } else {
            // Jika produk belum ada, tambahkan produk ke dalam keranjang
            $_SESSION['cart'][$product_id] = array(
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => $quantity,
                'image' => $product['image']
            );
        }
    }

    // Redirect untuk menghindari pengiriman ulang form jika page direfresh
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}

// Cek apakah aksi adalah untuk menghapus produk
if (isset($_POST['remove_product'])) {
    if (isset($_POST['product_id'])) {
        $product_id = $_POST['product_id']; // Pastikan $product_id ada

        // Cek apakah produk ada dalam keranjang dan hapus produk
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
            $_SESSION['success_message'] = 'Produk berhasil dihapus dari keranjang.';
        } else {
            // Produk tidak ditemukan di keranjang
            $_SESSION['error_message'] = 'Produk tidak ditemukan dalam keranjang untuk dihapus.';
        }
    }
}

// Redirect kembali ke halaman sebelumnya dengan pesan
header('Location: ' . $_SERVER['HTTP_REFERER']);
exit;
?>
