<?php
// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    // Jika belum login, arahkan ke halaman login
    header('Location: index.php?p=login');
    exit();
}

include 'phpconection.php'; // Koneksi ke database

// Ambil ID produk dari parameter URL
$product_id = isset($_GET['id']) ? $_GET['id'] : 0;

// Cek apakah ID produk valid
if ($product_id > 0) {
    // Ambil detail produk dari database
    $query = "SELECT * FROM products WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $product = $result->fetch_assoc();

        // Ambil ulasan produk dari database untuk rating
        $reviews_query = "SELECT * FROM reviews WHERE product_id = $product_id ORDER BY created_at DESC";
        $reviews_result = mysqli_query($db, $reviews_query);
        $reviews = mysqli_fetch_all($reviews_result, MYSQLI_ASSOC);

        // Hitung rating rata-rata produk
        $rating_query = "SELECT AVG(rating) as avg_rating FROM reviews WHERE product_id = $product_id";
        $rating_result = mysqli_query($db, $rating_query);
        $rating = mysqli_fetch_assoc($rating_result);
        $average_rating = round($rating['avg_rating'], 1);  // Rata-rata rating (misalnya 4.5)
    } else {
        die("Product not found!");
    }
}

// Proses menambahkan ke keranjang
if (isset($_POST['add_to_cart'])) {
    $quantity = $_POST['quantity'];
    $user_id = $_SESSION['user_id'];  // Mengambil user_id dari session

    // Ambil data produk dari database untuk ditambahkan ke keranjang
    $query = "SELECT * FROM products WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param('i', $_POST['product_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    // Cek apakah cart sudah ada untuk user ini
    // Cek apakah cart sudah ada untuk user ini
    if (!isset($_SESSION['cart'][$user_id])) {
        $_SESSION['cart'][$user_id] = [];
    }

    // Cek apakah produk sudah ada di keranjang
    if (isset($_SESSION['cart'][$user_id][$product['id']])) {
        $_SESSION['cart'][$user_id][$product['id']]['quantity'] += $quantity;
    } else {
        $_SESSION['cart'][$user_id][$product['id']] = [
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => $quantity,
            'image' => $product['image']
        ];
    }

    // Redirect ke halaman yang sama untuk menghindari pengiriman ulang form
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}

?>


<style>
        .rating {
            color: #ffcc00;
        }
        .rating span {
            font-size: 1.2rem;
        }
        .review-section {
            margin-top: 40px;
        }

        .offcanvas-backdrop {
        background-color: rgba(0, 0, 0, 0.5) !important; /* Kurangi tingkat kegelapan */
    }
    </style>


    <div class="container mt-5 justify-content-center">
        <!-- Section Detail Produk -->
<section id="product-detail" class="container my-5 justify-content-center">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <!-- Gambar Produk -->
            <img src="./admin/uploads/<?= !empty($product['image']) ? $product['image'] : 'default.png' ?>" 
            alt="Product Image" 
            class="img-fluid product-image rounded" 
            style="width: 650px; height: 550px; object-fit: cover; object-position: center;">
        </div>
        <div class="col-md-5">
            <h3 class="text-warning"><?= htmlspecialchars($product['name']) ?></h3>
            <h4>Harga: Rp <?= number_format($product['price'], 0, ',', '.') ?></h4>
            
            <p><strong>Deskripsi:</strong></p>
            <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
            <p><strong>Stok:</strong> <?= $product['stock'] ?> item tersedia</p>
            
            <!-- Form untuk menambahkan produk ke keranjang -->
            <form method="POST" id="addToCartForm">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                <input type="number" name="quantity" value="1" min="1" class="form-control mb-3" required>
                <div class="d-flex mx-2 align-items-center">
                    <button type="submit" name="add_to_cart" class="btn btn-primary rounded-pill mx-2">Tambah Keranjang</button>
                    <a href="index.php?p=toko" class="btn btn-outline-secondary rounded-pill">Kembali ke Produk</a>
                </div>
            </form>
        </div>
    </div>
</section>


        

<!-- Offcanvas Cart Section -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasCart" aria-labelledby="offcanvasCartLabel">
    <div class="offcanvas-header justify-content-between">
        <h5 class="offcanvas-title text-primary" id="offcanvasCartLabel" style="font-size: 1.25rem; font-weight: bold;">Keranjang Anda</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body" style="background-color: #f9f9f9; border-radius: 10px;">
        <!-- Header Keranjang -->
        <h4 class="d-flex justify-content-between align-items-center mb-3">
    <span class="text-primary" style="font-size: 1.1rem;">Keranjang Anda</span>
    <span class="badge bg-primary rounded-pill" style="font-size: 1.1rem;" id="cartCount">
        <?php 
        // Menampilkan jumlah produk dalam keranjang
        echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
        ?>
    </span>
</h4>


        <!-- Cart Items List -->
        <ul class="list-group mb-3" style="list-style-type: none; padding-left: 0;">
            <?php 
            $user_id = $_SESSION['user_id'];  // Mengambil user_id dari session
            $cart_query = "SELECT c.quantity, p.id as product_id, p.name, p.price, p.image
                FROM cart c
                JOIN products p ON c.product_id = p.id
                WHERE c.user_id = ?";
            $stmt = $db->prepare($cart_query);
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $cart_items = [];
            $total_price = 0;

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $total_price += $row['price'] * $row['quantity'];
                    echo "<li class='list-group-item d-flex  align-items-center lh-sm' style='border-radius: 10px; margin-bottom: 10px; background-color: #ffffff; box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);'>
                            <div class='d-flex align-items-center'>
                                <img src='./admin/uploads/" . htmlspecialchars($row['image']) . "' alt='" . htmlspecialchars($row['name']) . "' class='img-thumbnail' style='width: 75px; height: 100px; margin-right: 10px;'>
                                <div>
                                    <h6 class='my-0' style='font-size: 1.1rem; font-weight: 500;'>" . htmlspecialchars($row['name']) . "</h6>
                                    <small class='text-muted'>Kuantitas: </small>
                                    <div class='d-flex align-items-center'>
                                        
                                        <input type='number' class='form-control d-inline text-center quantity-input' data-product-id='" . $row['product_id'] . "' value='" . $row['quantity'] . "' min='1' style='width: 50px; height: 30px; margin: 0 5px;' required>
                                        <button type='button' class='btn btn-warning btn-sm mx-2 update-cart-btn' data-product-id='" . $row['product_id'] . "' style='width: 60px; height: 30px;' class='form-control d-inline rounded' border-radius: 5px;'>Update</button>
                                        <button type='button' class='btn btn-danger btn-sm removeFromCart' data-id='" . $row['product_id'] . "' style='font-size: 0.9rem; border-radius: 5px;'><i class='bi bi-trash'></i></button>
                                    </div>
                                </div>
                            </div>
                            <span class='text-muted' style='font-size: 1.1rem;'>Rp " . number_format($row['price'] * $row['quantity'], 0, ',', '.') . "</span>
                        </li>";
                }
            } else {
                echo "<li class='list-group-item' style='font-size: 1.1rem; color: #6c757d;'>Keranjang Anda kosong.</li>";
            }
            ?>
        </ul>

        <!-- Total Price -->
        <li class="list-group-item d-flex justify-content-between" style="background-color: #f8f9fa; border-radius: 10px; font-weight: 600;">
            <span>Total (IDR)</span>
            <strong>Rp <?= isset($total_price) ? number_format($total_price, 0, ',', '.') : '0' ?></strong>
        </li>

        <!-- Checkout Button -->
        <a href="index.php?p=checkout" class="w-100 btn btn-secondary btn-lg mt-3" style="border-radius: 10px; font-size: 1.1rem;">Lanjutkan ke Pembayaran</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="
https://cdn.jsdelivr.net/npm/sweetalert2@11.15.3/dist/sweetalert2.all.min.js
"></script>
<script>
$(document).ready(function () {
    // Menangani form Add to Cart
    $('#addToCartForm').on('submit', function (e) {
        e.preventDefault();

        $.ajax({
            url: 'process_cart.php',
            type: 'POST',
            data: $(this).serialize() + '&action=addToCart',
            success: function (response) {
                try {
                    let result = JSON.parse(response);
                    if (result.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: result.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: result.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            location.reload();
                        });
                    }
                } catch (e) {
                    console.error('Kesalahan parsing JSON:', e);
                    Swal.fire({
                        icon: 'error',
                        title: 'Kesalahan Sistem!',
                        text: 'Data tidak valid diterima dari server.',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        location.reload();
                    });
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat menghubungi server.',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    location.reload();
                });
            }
        });
    });

    // Menangani perubahan kuantitas
    $(document).on('change', '.quantity-input', function () {
        const productId = $(this).data('product-id');
        const quantity = $(this).val();

        $.ajax({
            url: 'process_cart.php',
            type: 'POST',
            data: { action: 'updateQuantity', product_id: productId, quantity: quantity },
            success: function (response) {
                console.log('Server Response:', response); // Debug log untuk melihat respons server

                try {
                    const result = JSON.parse(response); // Parsing JSON dari server
                    console.log('Parsed JSON:', result); // Debug log hasil parsing JSON

                    if (result.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: result.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: result.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                } catch (error) {
                    console.error('Error parsing response:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Respons tidak valid dari server.',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat menghubungi server.',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        });
    });

    // Fungsi untuk menghapus produk dari keranjang
    $(document).on('click', '.removeFromCart', function () {
        const productId = $(this).data('id');

        $.ajax({
            url: 'process_cart.php',
            type: 'POST',
            data: { action: 'removeFromCart', product_id: productId },
            success: function (response) {
                try {
                    const result = JSON.parse(response);
                    if (result.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: result.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            location.reload(); // Refresh halaman setelah penghapusan berhasil
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: result.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                } catch (error) {
                    console.error('Error parsing response:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Respons tidak valid dari server.',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat menghapus produk.',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        });
    });
});
</script>