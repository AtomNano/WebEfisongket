<?php
include 'phpconection.php';

?>
<!-- Section for the introductory text -->
<section class="py-1 bg-light text-center">
    <div class="container">
        <h3 class="font-weight-bold">Temukan Koleksi Baju Tradisional Songket</h3>
        <p>Yang elegan dan berkualitas tinggi.Setiap produk dirancang dengan penuh perhatian untuk memberikan kenyamanan dan gaya.</p>
    </div>
</section>
<section id="related-products" class="related-products product-carousel py-5 position-relative overflow-hidden">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-center mt-5 mb-3">
            <h4 class="text-uppercase">Product Kami</h4>
            <a href="index.php?p=toko" class="btn-link">Lihat Semua Produk</a> <!-- Perbaiki Link "Lihat Semua Produk" -->
        </div>
        <div class="row">
            <?php
            // Ambil produk dari database
            $result = mysqli_query($db, "SELECT * FROM products");

            // Looping untuk menampilkan setiap produk
            while ($product = mysqli_fetch_assoc($result)):
            ?>
                <div class="col-12 col-sm-6 col-md-3 mb-4">
                    <div class="product-item image-zoom-effect link-effect">
                        <div class="image-holder">
                            <!-- Link ke halaman detail produk -->
                            <a href="index.php?p=toko&id=<?= $product['id'] ?>" class="stretched-link">
                                <img src="./admin/uploads/<?= !empty($product['image']) ? $product['image'] : 'default.png' ?>" alt="Product Image" class="img-fluid" style="width: 100%; height: auto; object-fit: cover;">
                            </a>
                            <a href="#" class="btn-icon btn-wishlist">
                                <i class="bi bi-heart"></i>
                            </a>
                            <div class="product-content">
                                <h5 class="text-uppercase fs-5 mt-3">
                                    <!-- Nama produk -->
                                    <?= htmlspecialchars($product['name']) ?>
                                </h5>
                                <a href="#" class="text-decoration-none" data-after="Add to cart">
                                    <span>Rp <?= number_format($product['price'], 0, ',', '.') ?></span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
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
            <span class="badge bg-primary rounded-pill" style="font-size: 1.1rem;">
                <?php 
                // Menampilkan jumlah produk dalam keranjang
                echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
                ?>
            </span>
        </h4>

        <!-- Cart Items List -->
        <ul class="list-group mb-3" style="list-style-type: none; padding-left: 0;">
            <?php 
            if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                $total_price = 0;
                foreach ($_SESSION['cart'] as $product_id => $product) {
                    $total_price += $product['price'] * $product['quantity'];
                    echo "<li class='list-group-item d-flex justify-content-between lh-sm' style='border-radius: 10px; margin-bottom: 10px; background-color: #ffffff; box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);'>
                            <div>
                                <h6 class='my-0' style='font-size: 1.1rem; font-weight: 500;'>{$product['name']}</h6>
                                <small class='text-muted'>Kuantitas: </small>
                                <form method='POST' action='process_cart.php' class='d-inline'>
                                    <input type='hidden' name='product_id' value='{$product_id}'>
                                    <input type='number' name='quantity' value='{$product['quantity']}' min='1' style='width: 60px; height: 30px;' class='form-control d-inline' required>
                                    <button type='submit' class='btn btn-warning btn-sm mt-2' style='font-size: 0.9rem; border-radius: 5px;'>Update</button>
                                </form>
                            </div>
                            <span class='text-muted' style='font-size: 1.1rem;'>Rp " . number_format($product['price'] * $product['quantity'], 0, ',', '.') . "</span>
                            <form method='POST' action='process_cart.php' class='ms-3 d-inline'>
                                <input type='hidden' name='product_id' value='{$product_id}'>
                                <button type='submit' name='remove_product' class='btn btn-danger btn-sm' style='font-size: 0.9rem; border-radius: 5px;'>Hapus</button>
                            </form>
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
        <a href="checkout.php" class="w-100 btn btn-primary btn-lg mt-3" style="border-radius: 10px; font-size: 1.1rem;">Lanjutkan ke Pembayaran</a>
    </div>
</div>
