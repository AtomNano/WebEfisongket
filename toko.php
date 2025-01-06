<?php
include 'phpconection.php';

$query = "SELECT * FROM products WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param('i', $product_id);
$stmt->execute();
$result = $stmt->get_result();

// Ambil ID produk dari parameter URL
$product_id = isset($_GET['id']) ? $_GET['id'] : 0;

// Periksa apakah ID produk valid
if ($product_id > 0) {
    // Ambil detail produk dari database
    $query = "SELECT * FROM products WHERE id = $product_id";
    $result = mysqli_query($db, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $product = mysqli_fetch_assoc($result);

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

if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Cek apakah keranjang sudah ada, jika belum buat array keranjang baru
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
        $_SESSION['cart'][$product_id]['quantity'] += $quantity; // Problem
    }
    
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity'] += $quantity; // Menambah kuantitas jika produk sudah ada
    } else {
        $_SESSION['cart'][$product_id] = array(
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => $quantity,
            'image' => $product['image']
        );
    }
    

    // Redirect untuk menghindari pengiriman ulang form jika page direfresh
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}
// Ambil parameter dari URL
$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;
$min_price = isset($_GET['min_price']) ? intval($_GET['min_price']) : 0;
$max_price = isset($_GET['max_price']) ? intval($_GET['max_price']) : 0;

// Query dasar
$query = "SELECT * FROM products WHERE 1=1";

// Tambahkan filter kategori jika ada
if ($category_id > 0) {
    $query .= " AND category_id = $category_id";
}

// Tambahkan filter harga minimum jika ada
if ($min_price > 0) {
    $query .= " AND price >= $min_price";
}

// Tambahkan filter harga maksimum jika ada
if ($max_price > 0) {
    $query .= " AND price <= $max_price";
}

// Eksekusi query
$result = mysqli_query($db, $query);

// Ambil kategori untuk filter dropdown
$categories = mysqli_query($db, "SELECT * FROM kategori");

$min_price = $_GET['min_price'] ?? 100000;
$max_price = $_GET['max_price'] ?? 10000000;

?>


<style>
    
    .offcanvas-backdrop {
        background-color: rgba(0, 0, 0, 0.1) !important; /* Kurangi efek hitam */
    }

    .offcanvas {
        background-color: #f9f9f9; /* Sesuaikan dengan warna latar belakang yang diinginkan */
        border-radius: 10px;
    }
        
        .sidebar {
            background: #fff;
            border-radius: 5px;
            padding: 5px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }
        .product-item {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(243, 240, 240, 0.1);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .product-item:hover {
        transform: translateY(-10px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        
        .product-image {
    width: 80%;
    height: 300px;
    position: relative;
    border-radius: 8px;
    transition: transform 0.3s ease;
    margin: auto; /* Untuk memposisikan di tengah secara horizontal */
    display: flex; /* Untuk memposisikan di tengah secara horizontal dan vertikal */
    justify-content: center; /* Untuk memposisikan di tengah secara horizontal */
    align-items: center; /* Untuk memposisikan di tengah secara vertikal */
}

    .product-image:hover {
        transform: scale(1.05);
    }

    .btn-wishlist {
        background-color: rgba(255, 255, 255, 0.8);
        border-radius: 50%;
        padding: 10px;
        transition: background-color 0.3s ease;
    }

    .btn-wishlist:hover {
        background-color: rgba(255, 255, 255, 1);
    }

    .product-content {
        background-color: #f8f9fa;
        border-top: 1px solid #ddd;
        padding: 15px;
        border-radius: 0 0 8px 8px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        
        
    }
    #billboard {
    position: relative;
    overflow: hidden; /* Memastikan pseudo-element tidak keluar dari batas */
    z-index: 1; /* Pastikan elemen ini memiliki z-index yang lebih tinggi dari pseudo-element */
}

#billboard::before {
    content: '';
    background-image: url('gambarEfi/bg1.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    filter: blur(5px); /* Sesuaikan nilai blur sesuai kebutuhan */
    z-index: -1; /* Memastikan pseudo-element berada di belakang konten */
}

.product-content h5 {
    color: #333;
    min-height: 50px; /* Tetapkan tinggi minimum untuk nama produk */
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
}

.product-content a {
    color: rgb(131, 131, 131);
}

.product-content a:hover {
    color: rgb(0, 0, 0);
}
    </style>
    
<!-- Section for the introductory text -->
<section id="billboard" class="py-1 bg-light text-center">

    <div class="container">
        <h3 class="font-weight-bold text-light" data-aos="fade-up">Temukan Koleksi Baju Tradisional Songket</h3>
        <p class="text-light" data-aos="fade-up">Yang elegan dan berkualitas tinggi.Setiap produk dirancang dengan penuh perhatian untuk memberikan kenyamanan dan gaya.</p>
    </div>
</section>

<!-- Bagian Produk dan Sidebar -->
<section class="container-fluid py-5">
    <div class="container-fluid" data-aos="fade-up">
        <div class="row">
            <!-- Sidebar -->
            <aside class="col-md-2 mb-3" data-aos="fade-up">
                <div class="sidebar p-4 bg-white rounded shadow-sm">
                    <h4 class="font-weight-bold text-uppercase mb-4">Filter Produk</h4>
                    <!-- Form Filter -->
                    <form method="GET" action="index.php">
                        <input type="hidden" name="p" value="toko">
                        <div class="mb-3">
                            <!-- Dropdown Kategori -->
                            <label for="category_id" class="form-label">Kategori</label>
                            <select name="category_id" id="category_id" class="form-select" onchange="this.form.submit()">
                                <option value="">Semua Kategori</option>
                                <?php while ($category = mysqli_fetch_assoc($categories)): ?>
                                    <option value="<?= $category['id_kategori'] ?>" 
                                        <?= $category_id == $category['id_kategori'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($category['nama_kategori']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <!-- Filter Harga -->
                        <div class="filter-harga mt-5">
                            <h4>Filter Harga</h4>
                            <div class="mb-3">
                                <label for="price_range" class="form-label">Harga Minimum (Rp)</label>
                                <input type="range" class="form-range" id="price_range" name="min_price" 
                                    min="100000" max="10000000" step="500000" 
                                    value="<?= $min_price ?>" onchange="this.nextElementSibling.value = this.value">
                                <output><?= number_format($min_price, 0, ',', '.') ?></output>
                            </div>

                            <div class="mb-3">
                                <label for="max_price" class="form-label">Harga Maksimum (Rp)</label>
                                <input type="range" class="form-range" id="price_range_max" name="max_price" 
                                    min="100000" max="10000000" step="500000" 
                                    value="<?= $max_price ?>" onchange="this.nextElementSibling.value = this.value">
                                <output><?= number_format($max_price, 0, ',', '.') ?></output>
                            </div>
                        </div>

                        <!-- Tombol Terapkan Filter -->
                        <button type="submit" class="btn btn-primary w-100 rounded">Terapkan Filter</button>
                    </form>
                </div>
            </aside>

            <!-- Produk -->
            <div class="col-md-9" data-aos="fade-up">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="text-uppercase">Produk Kami</h4>
                </div>
                <div class="row g-4">
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($product = mysqli_fetch_assoc($result)): ?>
                            <div class="col-12 col-sm-6 col-md-3 mb-2 d-flex align-items-stretch">
                                <div class="product-item image-zoom-effect link-effect bg-white rounded shadow-sm w-100 border border-secondary-subtle border-1">
                                    <div class="image-holder position-relative">
                                        <!-- Link ke halaman detail produk -->
                                        <a href="index.php?p=toko&id=<?= $product['id'] ?>" class="stretched-link">
                                            <img src="./admin/uploads/<?= !empty($product['image']) ? $product['image'] : 'default.png' ?>" 
                                                alt="Product Image" class="img-fluid product-image">
                                        </a>
                                        <!-- Wishlist Button -->
                                        <a href="index.php?p=toko&id=<?= $product['id'] ?>" class="btn-icon btn-wishlist position-absolute top-0 end-0 m-2">
                                            <i class="bi bi-heart"></i>
                                        </a>
                                    </div>
                                    <div class="product-content p-3 text-center d-flex flex-column">
                                        <!-- Product Name -->
                                        <a href="index.php?p=toko&id=<?= $product['id'] ?>" class="text-decoration-none">
                                            <h5 class="text-uppercase fs-5 mt-3 d-flex align-items-center justify-content-center" style="height: 70px; overflow: hidden;">
                                                <?= htmlspecialchars($product['name']) ?>
                                            </h5>
                                        </a>
                                        <!-- Product Price -->
                                        <a href="index.php?p=toko&id=<?= $product['id'] ?>" class="text-decoration-none" data-after="Tambah Keranjang">
                                            <span class="text-primary font-weight-bold">Rp <?= number_format($product['price'], 0, ',', '.') ?></span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <p class="text-center">Tidak ada produk ditemukan.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
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
                    $total_items = 0;
                    if (isset($_SESSION['cart'][$_SESSION['user_id']])) {
                        foreach ($_SESSION['cart'][$_SESSION['user_id']] as $item) {
                            $total_items += $item['quantity'];
                        }
                    }
                    echo $total_items;
                    ?>
                </span>
</h4>


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
            echo "<li class='list-group-item d-flex justify-content-between align-items-center lh-sm' style='border-radius: 10px; margin-bottom: 10px; background-color: #ffffff; box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);'>
                    <div class='d-flex align-items-center'>
                        <img src='./admin/uploads/" . htmlspecialchars($row['image']) . "' alt='" . htmlspecialchars($row['name']) . "' class='img-thumbnail' style='width: 60px; height: 60px; margin-right: 10px;'>
                        <div>
                            <h6 class='my-0' style='font-size: 1.1rem; font-weight: 500;'>" . htmlspecialchars($row['name']) . "</h6>
                            <small class='text-muted'>Kuantitas: </small>
                            <div class='d-flex align-items-center'>
                                <button type='button' class='btn btn-light btn-sm decrease-quantity' data-product-id='" . $row['product_id'] . "' style='font-size: 1.2rem; border-radius: 5px;'><i class='bi bi-dash-circle'></i></button>
                                <input type='number' class='form-control d-inline text-center quantity-input' data-product-id='" . $row['product_id'] . "' value='" . $row['quantity'] . "' min='1' style='width: 50px; height: 30px; margin: 0 5px;' required>
                                <button type='button' class='btn btn-light btn-sm increase-quantity' data-product-id='" . $row['product_id'] . "' style='font-size: 1.2rem; border-radius: 5px;'><i class='bi bi-plus-circle'></i></button>
                            </div>
                        </div>
                    </div>
                    <span class='text-muted' style='font-size: 1.1rem;'>Rp " . number_format($row['price'] * $row['quantity'], 0, ',', '.') . "</span>
                    <button type='button' class='btn btn-danger btn-sm removeFromCart' data-id='" . $row['product_id'] . "' style='font-size: 0.9rem; border-radius: 5px;'><i class='bi bi-trash'></i></button>
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
        <a href="index.php?p=checkout" class="w-100 btn btn-primary btn-lg mt-3" style="border-radius: 10px; font-size: 1.1rem;">Lanjutkan ke Pembayaran</a>
    </div>
</div>

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
                let result = JSON.parse(response);
                if (result.status === 'success') {
                     // Update jumlah produk di keranjang
                location.reload(); // Refresh page or update the UI
                updateCartCount();

                // Tampilkan notifikasi berhasil
                alert(result.message);

                // Menampilkan sidebar offcanvas otomatis
                var offcanvas = new bootstrap.Offcanvas(document.getElementById('offcanvasSidebar'));
                offcanvas.show();
                } else {
                    alert(result.message);
                }
            },
            error: function () {
                alert('Error menambahkan produk ke keranjang.');
            },
        });
    });

    // Fungsi untuk memperbarui jumlah item di keranjang
})

// Update Cart Quantity
$('.update-cart-btn').click(function() {
    var product_id = $(this).data('product-id');
    var quantity = $(this).siblings('.update-quantity').val();

    $.ajax({
        url: 'process_cart.php',
        type: 'POST',
        data: {
            action: 'updateQuantity',
            product_id: product_id,
            quantity: quantity
        },
        success: function(response) {
            const result = JSON.parse(response);
            if (result.status === 'success') {
                // Update the cart count UI element (e.g., the number of items in the cart)
                $('#cartCount').text(result.cartCount);
                location.reload(); // Refresh page or update the UI
                var offcanvas = new bootstrap.Offcanvas(document.getElementById('offcanvasCart'));
                offcanvas.show(); // Membuka offcanvas secara otomatis
                // Redirect back to the same product page to reflect changes
                let urlParams = new URLSearchParams(window.location.search);
                let productPageUrl = "index.php?p=toko&id=" + urlParams.get('id');
                window.location.href = productPageUrl; // Refresh/redirect to the same product page
                var offcanvas = new bootstrap.Offcanvas(document.getElementById('offcanvasCart'));
                offcanvas.show();

            } else {
                alert(result.message); // Show error message if any issue occurs
            }
        },
        error: function() {
            alert('There was an error updating your cart.');
        }
    });
});

$(document).on('click', '.removeFromCart', function () {
    const productId = $(this).data('id');

    $.ajax({
        url: 'process_cart.php',
        type: 'POST',
        data: { action: 'removeFromCart', product_id: productId }, // Pastikan action dikirim dengan benar
        success: function (response) {
            const result = JSON.parse(response);
            if (result.status === 'success') {
                updateCartCount();

                // Memuat ulang konten keranjang untuk memperbarui daftar produk yang ada di keranjang
                $('#cartItems').load('process_cart.php', { action: 'getCartItems' });

                location.reload(); // Refresh page or update the UI
                var offcanvas = new bootstrap.Offcanvas(document.getElementById('offcanvasCart'));
                offcanvas.show(); // Membuka offcanvas secara otomatis
                // Redirect back to the same product page to reflect changes
                let urlParams = new URLSearchParams(window.location.search);
                let productPageUrl = "index.php?p=toko&id=" + urlParams.get('id');
                window.location.href = productPageUrl; // Refresh/redirect to the same product page
                var offcanvas = new bootstrap.Offcanvas(document.getElementById('offcanvasCart'));
                offcanvas.show();
                
            } else {
                alert(result.message);
            }
        },
        error: function () {
            alert('Error menghapus produk.');
            
        }
    });
});

</script>
