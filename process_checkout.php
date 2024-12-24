<?php
session_start();
include 'phpconection.php'; // Koneksi ke database


// Pastikan form checkout disubmit dengan metode POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $email = $_POST['email'];
    $name = $_POST['name'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];

    // Pastikan total_price ada di session
    if (isset($_SESSION['total_price']) && $_SESSION['total_price'] > 0) {
        $total_price = $_SESSION['total_price']; // Ambil total harga dari session
    } else {
        // Jika total_price tidak ada atau kosong, tampilkan pesan error
        echo json_encode(['status' => 'error', 'message' => 'Total harga tidak valid!']);
        exit();
    }

    // Proses upload bukti pembayaran
    if (isset($_FILES['payment-proof']) && $_FILES['payment-proof']['error'] === UPLOAD_ERR_OK) {
        $payment_proof = $_FILES['payment-proof']['name'];
        $payment_proof_tmp = $_FILES['payment-proof']['tmp_name'];
        $payment_proof_dir = 'admin/payment_proofs/';

        // Pastikan folder untuk upload bukti pembayaran ada
        if (!is_dir($payment_proof_dir)) {
            mkdir($payment_proof_dir, 0777, true);
        }

        // Menyimpan bukti pembayaran ke server
        $payment_proof_path = $payment_proof_dir . basename($payment_proof);
        if (move_uploaded_file($payment_proof_tmp, $payment_proof_path)) {
            // Bukti pembayaran berhasil diupload

            // Simpan transaksi ke dalam tabel transactions
            $query = "INSERT INTO transactions (email, name, address, phone, total_price, payment_proof, status) 
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($db, $query);
            $status = 'Pending'; // Status transaksi saat ini adalah 'Pending'
            mysqli_stmt_bind_param($stmt, 'ssssdss', $email, $name, $address, $phone, $total_price, $payment_proof_path, $status);
            $result = mysqli_stmt_execute($stmt);

            if ($result) {
                // Ambil ID transaksi yang baru disisipkan
                $transaction_id = mysqli_insert_id($db); // Mengambil ID transaksi yang baru dibuat
                
                // Ambil produk dari tabel cart berdasarkan user_id
                $user_id = $_SESSION['user_id']; // Pastikan user_id ada di session
                $cart_query = "SELECT c.product_id, c.quantity, p.price FROM cart c 
                               JOIN products p ON c.product_id = p.id 
                               WHERE c.user_id = ?";
                $cart_stmt = mysqli_prepare($db, $cart_query);
                mysqli_stmt_bind_param($cart_stmt, 'i', $user_id);
                mysqli_stmt_execute($cart_stmt);
                $cart_result = mysqli_stmt_get_result($cart_stmt);

                // Simpan data item dari keranjang belanja ke dalam tabel order_item
                while ($item = mysqli_fetch_assoc($cart_result)) {
                    $product_id = $item['product_id'];
                    $quantity = $item['quantity'];
                    $price = $item['price']; // Ambil harga produk dari tabel products

                    // Menyimpan item ke dalam order_item
                    $order_item_query = "INSERT INTO order_item (transaction_id, product_id, quantity, price) 
                                         VALUES (?, ?, ?, ?)";
                    $order_stmt = mysqli_prepare($db, $order_item_query);
                    mysqli_stmt_bind_param($order_stmt, 'iiii', $transaction_id, $product_id, $quantity, $price);
                    mysqli_stmt_execute($order_stmt);
                }

                // Hapus item dari keranjang setelah diproses
                $delete_cart_query = "DELETE FROM cart WHERE user_id = ?";
                $delete_cart_stmt = mysqli_prepare($db, $delete_cart_query);
                mysqli_stmt_bind_param($delete_cart_stmt, 'i', $user_id);
                mysqli_stmt_execute($delete_cart_stmt);

                // Ambil tanggal dibuat (created_at) dari transaksi yang baru
                $query = "SELECT created_at FROM transactions WHERE id = ?";
                $stmt = mysqli_prepare($db, $query);
                mysqli_stmt_bind_param($stmt, 'i', $transaction_id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
            
                if ($result && mysqli_num_rows($result) > 0) {
                    $transaction_data = mysqli_fetch_assoc($result);
                    $created_at = $transaction_data['created_at'];
                } else {
                    $created_at = 'Data tidak tersedia';
                }
            
                // Simpan data transaksi ke session
                $_SESSION['transaction'] = [
                    'id_transaksi' => $transaction_id,
                    'email' => $email,
                    'nama' => $name,
                    'alamat' => $address,
                    'no_telp' => $phone,
                    'total_pembayaran' => 'Rp ' . number_format($total_price, 0, ',', '.'), 
                    'status' => $status,
                    'payment_proof' => $payment_proof_path,
                    'created_at' => $created_at
                ];
            
                // Kirim respons sukses
                echo json_encode(['status' => 'success', 'message' => 'Checkout berhasil!', 'transaction_id' => $transaction_id]);
            } else {
                // Kirim respons error jika gagal menyimpan transaksi
                echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan transaksi.']);
            }
        } else {
            // Kirim respons error jika gagal mengupload bukti pembayaran
            echo json_encode(['status' => 'error', 'message' => 'Gagal mengupload bukti pembayaran.']);
        }
    } else {
        // Kirim respons error jika tidak ada bukti pembayaran yang diupload
        echo json_encode(['status' => 'error', 'message' => 'Bukti pembayaran tidak valid.']);
    }
} else {
    // Kirim respons error jika request method bukan POST
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>