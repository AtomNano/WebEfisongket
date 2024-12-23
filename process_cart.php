<?php
session_start();
include 'phpconection.php';


// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    // Jika belum login, arahkan ke halaman login
    header('Location: index.php?p=login');
    exit();
}

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    // Simpan transaksi dengan user_id
}

// Ensure the request is made via POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the action from POST request
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    switch ($action) {
        // Add product to cart
        case 'addToCart':
            $product_id = $_POST['product_id'];
            $quantity = $_POST['quantity'];

            // Validate product exists in the database
            $query = "SELECT * FROM products WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->bind_param('i', $product_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $product = $result->fetch_assoc();

                // Insert or update product in cart database
                $user_id = $_SESSION['user_id'];  // Mengambil user_id dari session
                $cart_query = "
                    INSERT INTO cart (user_id, product_id, quantity)
                    VALUES (?, ?, ?)
                    ON DUPLICATE KEY UPDATE quantity = quantity + ?
                ";
                $cart_stmt = $db->prepare($cart_query);
                $cart_stmt->bind_param('iiii', $user_id, $product_id, $quantity, $quantity);
                $cart_stmt->execute();

                // Add or update the product in session cart
                if (!isset($_SESSION['cart'])) {
                    $_SESSION['cart'] = [];
                }

                if (isset($_SESSION['cart'][$product_id])) {
                    $_SESSION['cart'][$product_id]['quantity'] += $quantity;
                } else {
                    $_SESSION['cart'][$product_id] = [
                        'name' => $product['name'],
                        'price' => $product['price'],
                        'quantity' => $quantity,
                        'image' => $product['image']
                    ];
                }

                // Calculate total price
                $total_price = 0;
                foreach ($_SESSION['cart'] as $item) {
                    $total_price += $item['price'] * $item['quantity'];
                }

                echo json_encode(['status' => 'success', 'message' => 'Product added to cart', 'total_price' => $total_price]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Product not found']);
            }
            break;

        // Update quantity in cart
        case 'updateQuantity':
            $product_id = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;
            $quantity = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 0;

            if ($quantity > 0 && isset($_SESSION['cart'][$product_id])) {
                // Update quantity in session
                $_SESSION['cart'][$product_id]['quantity'] = $quantity;

                // Update quantity in database
                $user_id = $_SESSION['user_id'];  // Mengambil user_id dari session
                $update_query = "UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?";
                $update_stmt = $db->prepare($update_query);
                $update_stmt->bind_param('iii', $quantity, $user_id, $product_id);
                $update_stmt->execute();

                // Calculate total price
                $total_price = 0;
                foreach ($_SESSION['cart'] as $item) {
                    $total_price += $item['price'] * $item['quantity'];
                }

                // Return updated cart count and total price
                $cart_count = count($_SESSION['cart']);
                echo json_encode([
                    'status' => 'success',
                    'cartCount' => $cart_count,
                    'total_price' => $total_price
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Invalid quantity or product not in cart'
                ]);
            }
            break;

        // Remove product from cart
        case 'removeFromCart':
            $product_id = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;

            if ($product_id > 0) {
                // Check if the product exists in the cart
                $user_id = $_SESSION['user_id'];  // Mengambil user_id dari session
                $check_query = "SELECT * FROM cart WHERE user_id = ? AND product_id = ?";
                $check_stmt = $db->prepare($check_query);
                $check_stmt->bind_param('ii', $user_id, $product_id);
                $check_stmt->execute();
                $check_result = $check_stmt->get_result();

                if ($check_result->num_rows > 0) {
                    // Remove product from session
                    unset($_SESSION['cart'][$product_id]);

                    // Remove product from database
                    $delete_query = "DELETE FROM cart WHERE user_id = ? AND product_id = ?";
                    $delete_stmt = $db->prepare($delete_query);
                    $delete_stmt->bind_param('ii', $user_id, $product_id);
                    if ($delete_stmt->execute()) {
                        // Calculate total price
                        $total_price = 0;
                        foreach ($_SESSION['cart'] as $item) {
                            $total_price += $item['price'] * $item['quantity'];
                        }

                        echo json_encode(['status' => 'success', 'message' => 'Product removed from cart', 'total_price' => $total_price]);
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'Failed to remove product from cart']);
                    }
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Product not found in cart']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid product']);
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