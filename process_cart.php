<?php
session_start();
include 'phpconection.php';

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    // Jika belum login, arahkan ke halaman login
    header('Location: index.php?p=login');
    exit();
}

$user_id = $_SESSION['user_id'];

// Hitung total jumlah barang dalam keranjang
$total_items = 0;
if (isset($_SESSION['cart'][$user_id])) {
    foreach ($_SESSION['cart'][$user_id] as $item) {
        $total_items += $item['quantity'];
    }
}

// Ensure the request is made via POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the action from POST request
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    switch ($action) {
        // Add product to cart
        case 'addToCart':
            $product_id = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;
            $quantity = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 0;

            if ($product_id > 0 && $quantity > 0) {
                $query = "SELECT * FROM products WHERE id = ?";
                $stmt = $db->prepare($query);
                $stmt->bind_param('i', $product_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $product = $result->fetch_assoc();

                    // Validasi stok
                    if ($quantity > $product['stock']) {
                        echo json_encode(['status' => 'error', 'message' => 'Stok tidak mencukupi.']);
                        exit;
                    }

                    // Tambahkan atau perbarui di session
                    if (!isset($_SESSION['cart'][$user_id])) {
                        $_SESSION['cart'][$user_id] = [];
                    }

                    if (isset($_SESSION['cart'][$user_id][$product_id])) {
                        $_SESSION['cart'][$user_id][$product_id]['quantity'] += $quantity;
                    } else {
                        $_SESSION['cart'][$user_id][$product_id] = [
                            'name' => $product['name'],
                            'price' => $product['price'],
                            'quantity' => $quantity,
                            'image' => $product['image']
                        ];
                    }

                    // Tambahkan atau perbarui di database
                    $query = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE quantity = quantity + ?";
                    $stmt = $db->prepare($query);
                    $stmt->bind_param('iiii', $user_id, $product_id, $quantity, $quantity);
                    $stmt->execute();

                    echo json_encode(['status' => 'success', 'message' => 'Produk berhasil ditambahkan ke keranjang!', 'total_items' => $total_items]);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Produk tidak ditemukan.']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Data tidak valid.']);
            }
            break;

        // Update quantity in cart
        case 'updateQuantity':
            $product_id = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;
            $quantity = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 0;

            if ($quantity > 0 && $product_id > 0) {
                $query = "SELECT stock FROM products WHERE id = ?";
                $stmt = $db->prepare($query);
                $stmt->bind_param('i', $product_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $product = $result->fetch_assoc();

                if ($quantity > $product['stock']) {
                    echo json_encode(['status' => 'error', 'message' => 'Stok tidak mencukupi.']);
                    exit;
                }

                // Update quantity in session
                if (isset($_SESSION['cart'][$user_id][$product_id])) {
                    $_SESSION['cart'][$user_id][$product_id]['quantity'] = $quantity;
                }

                // Update quantity in database
                $query = "UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?";
                $stmt = $db->prepare($query);
                $stmt->bind_param('iii', $quantity, $user_id, $product_id);
                $stmt->execute();

                // Calculate total price
                $total_price = 0;
                foreach ($_SESSION['cart'][$user_id] as $item) {
                    $total_price += $item['price'] * $item['quantity'];
                }

                echo json_encode(['status' => 'success', 'message' => 'Kuantitas berhasil diperbarui.', 'total_price' => $total_price]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Data tidak valid.']);
            }
            break;

        // Remove product from cart
        case 'removeFromCart':
            $product_id = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;

            if ($product_id > 0) {
                // Remove product from session
                unset($_SESSION['cart'][$user_id][$product_id]);

                // Remove product from database
                $query = "DELETE FROM cart WHERE user_id = ? AND product_id = ?";
                $stmt = $db->prepare($query);
                $stmt->bind_param('ii', $user_id, $product_id);
                $stmt->execute();

                // Calculate total price
                $total_price = 0;
                foreach ($_SESSION['cart'][$user_id] as $item) {
                    $total_price += $item['price'] * $item['quantity'];
                }

                echo json_encode(['status' => 'success', 'message' => 'Produk berhasil dihapus.', 'total_price' => $total_price]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Produk tidak valid.']);
            }
            break;

        // Default case for invalid actions
        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
            break;
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>