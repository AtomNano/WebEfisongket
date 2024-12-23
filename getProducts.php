<?php
// Data produk
$products = [
    'songket' => [
        ['name' => 'Baju Songket Tradisional', 'image' => 'gambarEfi/Songket Balapak Banyak Motif Benang Dua (3.800.000).jpg', 'price' => 'Rp 1.800.000'],
        ['name' => 'Kain Songket Premium', 'image' => 'gambarEfi/Songket Tabur Pandai Sikek Abu (2.500.000).jpg', 'price' => 'Rp 1.200.000'],
    ],
    'bordir' => [
        ['name' => 'Selendang Bordir Hitam', 'image' => 'gambarEfi/Songket Tabur Pandai Sikek Abu (2.500.000).jpg', 'price' => 'Rp 600.000'],
        ['name' => 'Bordir Modern', 'image' => 'gambarEfi/bordir2.jpg', 'price' => 'Rp 750.000'],
    ],
    'batik' => [
        ['name' => 'Batik Tulis', 'image' => 'gambarEfi/batik1.jpg', 'price' => 'Rp 1.000.000'],
        ['name' => 'Kain Batik Kombinasi', 'image' => 'gambarEfi/batik2.jpg', 'price' => 'Rp 850.000'],
    ],
];

// Menentukan kategori yang diminta
$category = isset($_GET['category']) ? $_GET['category'] : 'songket';
$selected_products = isset($products[$category]) ? $products[$category] : [];

// Mengembalikan data dalam format JSON
header('Content-Type: application/json');
echo json_encode($selected_products);

?>
