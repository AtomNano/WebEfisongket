<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Produk - <?= htmlspecialchars($product['name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
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
    </style>
</head>
<body>
    <div class="container mt-5 justify-content-center">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <!-- Gambar Produk -->
                <img src="./admin/uploads/<?= !empty($product['image']) ? $product['image'] : 'default.png' ?>" 
                alt="Product Image" 
                class="img-fluid product-image rounded" 
                style="width: 650px; height: 550px; object-fit: cover; object-position: center;">
            </div>
            <div class="col-md-5">
                <h1 class="text-warning"><?= htmlspecialchars($product['name']) ?></h1>
                <h3>Harga: Rp <?= number_format($product['price'], 0, ',', '.') ?></h3>
                <div class="rating">
                    <?php
                    // Menampilkan bintang rating berdasarkan rating rata-rata produk
                    for ($i = 1; $i <= 5; $i++) {
                        echo $i <= $average_rating ? '★' : '☆';
                    }
                    ?>
                    <span>(<?= count($reviews) ?> Ulasan)</span>
                </div>
                <p><strong>Deskripsi:</strong></p>
                <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                <p><strong>Stok:</strong> <?= $product['stock'] ?> item tersedia</p>
                <!-- Form untuk menambahkan produk ke keranjang -->
                <form method="POST" action="" id="addToCartForm">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <input type="number" name="quantity" value="1" min="1" class="form-control mb-3" required>
                    <button type="submit" name="add_to_cart" class="btn btn-success">Add to Cart</button>
                </form>
            </div>
        </div>

        <!-- Section Ulasan Produk -->
        <div class="review-section">
            <h4>Ulasan Produk</h4>
            <ul class="list-group" id="reviewList">
                <!-- Ulasan akan dimuat menggunakan AJAX -->
            </ul>

            <!-- Form untuk menambahkan ulasan -->
            <h5 class="mt-4">Tulis Ulasan Anda</h5>
            <form id="reviewForm" method="POST">
                <input type="hidden" name="product_id" value="<?= $product_id ?>">
                <div class="mb-3">
                    <label for="username" class="form-label">Nama Anda</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="rating" class="form-label">Rating</label>
                    <select class="form-control" id="rating" name="rating" required>
                        <option value="1">1 - Sangat Buruk</option>
                        <option value="2">2 - Buruk</option>
                        <option value="3">3 - Cukup</option>
                        <option value="4">4 - Bagus</option>
                        <option value="5">5 - Sangat Bagus</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="comment" class="form-label">Komentar</label>
                    <textarea class="form-control" id="comment" name="comment" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Kirim Ulasan</button>
            </form>
        </div>

        <a href="index.php" class="btn btn-primary mt-3">Kembali ke Produk</a>
    </div>

    <!-- Offcanvas Cart Section -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasCart" aria-labelledby="offcanvasCartLabel">
        <div class="offcanvas-header justify-content-between">
            <h5 class="offcanvas-title" id="offcanvasCartLabel">Keranjang Anda</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <h4 class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-primary">Keranjang Anda</span>
                <span class="badge bg-primary rounded-pill">
                    <?php 
                    // Menampilkan jumlah produk dalam keranjang
                    echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
                    ?>
                </span>
            </h4>

            <!-- Cart Items List -->
            <ul class="list-group mb-3">
                <?php 
                if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                    $total_price = 0;
                    foreach ($_SESSION['cart'] as $product_id => $product) {
                        $total_price += $product['price'] * $product['quantity'];
                        echo "<li class='list-group-item d-flex justify-content-between lh-sm'>
                                <div>
                                    <h6 class='my-0'>{$product['name']}</h6>
                                    <small class='text-muted'>Kuant itas: </small>
                                    <form method='POST' action='update_cart.php'>
                                        <input type='hidden' name='product_id' value='{$product_id}'>
                                        <input type='number' name='quantity' value='{$product['quantity']}' min='1' style='width: 50px;' class='form-control'>
                                        <button type='submit' name='update_quantity' class='btn btn-warning btn-sm mt-2'>Update</button>
                                    </form>
                                </div>
                                <span class='text-muted'>Rp " . number_format($product['price'] * $product['quantity'], 0, ',', '.') . "</span>
                                <form method='POST' action='remove_from_cart.php' class='ms-3'>
                                    <input type='hidden' name='product_id' value='{$product_id}'>
                                    <button type='submit' name='remove_product' class='btn btn-danger btn-sm'>Hapus</button>
                                </form>
                            </li>";
                    }
                }
                ?>
            </ul>

            <!-- Total Price -->
            <li class="list-group-item d-flex justify-content-between">
                <span>Total (IDR)</span>
                <strong>Rp <?= isset($total_price) ? number_format($total_price, 0, ',', '.') : '0' ?></strong>
            </li>

            <!-- Checkout Button -->
            <a href="checkout.php" class="w-100 btn btn-primary btn-lg">Lanjutkan ke Pembayaran</a>
        </div>
    </div>