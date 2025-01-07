<?php
include 'phpconection.php';

// Ambil kategori dari database
$categoryQuery = "SELECT * FROM kategori";
$categoryResult = mysqli_query($db, $categoryQuery);
$categories = mysqli_fetch_all($categoryResult, MYSQLI_ASSOC);

// Fungsi untuk mendapatkan produk terlaris
function getTopSellingProducts($db) {
    $query = "
        SELECT p.id, p.name, p.price, p.image, SUM(oi.quantity) AS total_sold
        FROM order_item oi
        JOIN products p ON oi.product_id = p.id
        GROUP BY oi.product_id
        ORDER BY total_sold DESC
        LIMIT 4
    ";  

    $result = mysqli_query($db, $query);

    if (!$result) {
        die("Query gagal: " . mysqli_error($db));
    }

    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }

    return $data;
}

// Ambil data produk terlaris
$topProducts = getTopSellingProducts($db);
?>

<style>
    #billboard {
        background-image: url('gambarEfi/bg1.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        position: relative;
        
        
    }

    #billboard::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5); /* Optional: to add a dark overlay */
        z-index: 1;
        
    }

    #billboard .container {
        position: relative;
        z-index: 2;
    }

    .offcanvas-backdrop {
        background-color: rgba(0, 0, 0, 0.1) !important; /* Kurangi efek hitam */
    }

    .offcanvas {
        background-color: #f9f9f9; /* Sesuaikan dengan warna latar belakang yang diinginkan */
        border-radius: 10px;
    }
</style>

<!-- Bagian Keranjang -->

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



<!-- Bagian Konten -->
<div class="">

    <!-- Bagian Billboard -->
    <section id="billboard" class="py-2">
        <div class="container">
            <div class="row justify-content-center">
                <h1 class="section-title text-center text-white" data-aos="fade-up">Koleksi Terbaru Efi Songket</h1>
                <div class="col-md-6 text-center" data-aos="fade-up" data-aos-delay="300">
                    <p class="text-white">Temukan koleksi baju tradisional songket yang elegan dan berkualitas tinggi. Setiap produk
                        dirancang dengan penuh perhatian untuk memberikan kenyamanan dan gaya.</p>
                </div>
            </div>

            <!-- Kartu -->
            <div class="custom-card d-flex flex-column flex-md-row align-items-center" data-aos="fade-up">
                <!-- Bagian Teks -->
                <div class="text-section mr-md-4 mb-4 mb-md-0" data-aos="fade-up">
                    <p class="text-muted mb-1">Efi Songket Eksklusif</p>
                    <h2 class="font-weight-bold mb-2">Songket Balapak Banyak Motif</h2>
                    <p class="text">Kain songket asli dengan motif tradisional dan kualitas benang terbaik. Cocok untuk acara formal atau koleksi pribadi.</p>
                    <a href="index.php?p=toko" class="btn btn-primary rounded-5">Lihat Detail</a>
                </div>
                <!-- Bagian Gambar -->
                <div class="image-placeholder flex-grow-1 image-zoom-effect" data-aos="fade-up">
                    <img src="gambarEfi/1.png" alt="Songket Efi" class="img-fluid landscape-image" />
                </div>
            </div>
            <!-- Akhir Kartu -->
        </div>
    </section>

    <!-- Bagian Best Seller -->
<div class="container mt-5 mb-5" data-aos="fade-up">
    <div class="row align-items-center mb-4">
        <div class="col text-center text-md-start">
            <h3 class="fw-bold text-uppercase">Best Sellers Efi Songket</h3>
        </div>
        <div class="col text-center text-md-end">
            <a href="index.php?p=toko" class="btn btn-primary rounded-pill px-4 py-2">Lihat Semua Produk</a>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
        <?php
        if (!empty($topProducts)) {
            foreach ($topProducts as $product) {
        ?>
                <div class="col">
                    <div class="card h-100 shadow-sm border-0">
                        <!-- Gambar Produk -->
                        <div class="position-relative">
                            <img src="admin/uploads/<?= !empty($product['image']) ? $product['image'] : 'default.png' ?>" 
                                class="img-fluid rounded-top product-image" 
                                style="height: 100%; object-fit: cover;" 
                                alt="<?= htmlspecialchars($product['name']) ?>">
                            <!-- Label Diskon -->
                            <div class="position-absolute top-0 start-0 m-2">
                                <span class="badge bg-success">Best Seller</span>
                            </div>
                        </div>

                        <!-- Detail Produk -->
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title text-truncate"><?= htmlspecialchars($product['name']) ?></h5>
                            <p class="card-text text-muted">Rp <?= number_format($product['price'], 0, ',', '.') ?></p>
                            <a href="index.php?p=toko&id=<?= $product['id'] ?>" 
                                class="btn btn-primary mt-auto rounded-pill shadow-sm">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
        <?php
            }
        } else {
            echo "<p class='text-center'>Tidak ada produk best seller ditemukan.</p>";
        }
        ?>
    </div>
</div>
<!-- Akhir Best Seller -->


    <!-- Akhir Belanja Berdasarkan Kategori -->

    <!-- Bagian Carousel -->
    <div class="container mt-5 mb-5" data-aos="fade-up">
        <div class="row">
            <div class="col-md-12 text-center">
                <h1 class="font-weight-bold">Efi Songket</h1>
                <p class="text-muted">Menghadirkan keindahan dan keanggunan dalam setiap kain songket.</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
                    <ol class="carousel-indicators">
                        <li data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active"></li>
                        <li data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1"></li>
                        <li data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2"></li>
                    </ol>
                    <div class="carousel-inner rounded-5">
                        <div class="carousel-item active">
                            <div class="carousel-overlay">
                                <img src="gambarEfi/1.png" alt="Keindahan Songket" class="d-block w-100" style="height: 500px; object-fit: cover; filter: brightness(70%);">
                                <div class="carousel-caption d-none d-md-block">
                                    <h3 class="font-weight-bold text-white">Keindahan Songket</h3>
                                    <p class="text-white h3">Menampilkan keanggunan dan tradisi dalam setiap helai kain.</p>
                                </div>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <div class="carousel-overlay">
                                <img src="gambarEfi/2.png" alt="Elegansi Tradisional" class="d-block w-100" style="height: 500px; object-fit: cover; filter: brightness(70%);">
                                <div class="carousel-caption d-none d-md-block">
                                    <h3 class="font-weight-bold text-white">Elegansi Tradisional</h3>
                                    <p class="text-white h3">Kain songket yang memadukan warisan budaya dengan gaya modern.</p>
                                </div>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <div class="carousel-overlay">
                                <img src="gambarEfi/3.png" alt="Kualitas Terbaik" class="d-block w-100" style="height: 500px; object-fit: cover; filter: brightness(70%);">
                                <div class="carousel-caption d-none d-md-block">
                                    <h3 class="font-weight-bold text-white">Kualitas Terbaik</h3>
                                    <p class="text-white h3">Setiap produk dibuat dengan bahan terbaik dan perhatian terhadap detail.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Sebelumnya</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Selanjutnya</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Akhir Carousel -->

    <!-- Bagian Produk Terkait -->
    <section id="related-products" class="related-products product-carousel py-5 position-relative overflow-hidden">
        <div class="container">
            <div class="d-flex flex-wrap justify-content-between align-items-center mt-5 mb-3">
                <h4 class="text-uppercase">Anda Mungkin Juga Suka</h4>
                <a href="index.php?p=toko" class="btn-link">Lihat Semua Produk</a>
            </div>
            <div class="swiper product-swiper open-up swiper-initialized swiper-horizontal swiper-backface-hidden aos-init aos-animate" data-aos="zoom-out">
                <div class="swiper-wrapper d-flex" id="swiper-wrapper-80c47299e250ea52" aria-live="polite">
                    <?php
                    // Ambil produk dari database
                    $relatedProductsQuery = "SELECT * FROM products ORDER BY created_at DESC LIMIT 8";
                    $relatedProductsResult = mysqli_query($db, $relatedProductsQuery);

                    if ($relatedProductsResult && mysqli_num_rows($relatedProductsResult) > 0) {
                        while ($product = mysqli_fetch_assoc($relatedProductsResult)) {
                            ?>
                            <div class="swiper-slide" role="group" aria-label="1 / 4" style="width: 100%; margin-right: 10px;">
                                <div class="product-item image-zoom-effect link-effect">
                                    <div class="image-holder">
                                        <a href="index.php?p=toko&id=<?= $product['id'] ?>">
                                            <img src="admin/uploads/<?= !empty($product['image']) ? $product['image'] : 'default.png' ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-image img-fixed" style="max-width: 100%; height: 550px; object-fit: cover; object-position: center;">
                                        </a>
                                        <a href="index.php?p=toko&id=<?= $product['id'] ?>" class="btn-icon btn-wishlist">
                                            <i class="bi bi-heart"></i>
                                        </a>
                                        <div class="product-content">
                                            <h5 class="text-uppercase fs-5 mt-3">
                                                <a href="index.php?p=toko&id=<?= $product['id'] ?>"><?= htmlspecialchars($product['name']) ?></a>
                                            </h5>
                                            <a href="index.php?p=toko&id=<?= $product['id'] ?>" class="text-decoration-none" data-after="Add to cart"><span>Rp <?= number_format($product['price'], 0, ',', '.') ?></span></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo "<p class='text-center'>Tidak ada produk ditemukan.</p>";
                    }
                    ?>
                </div>
                <div class="swiper-pagination"></div>
                <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span>
            </div>
            <div class="icon-arrow icon-arrow-left swiper-button-disabled" tabindex="-1" role="button" aria-label="Slide Sebelumnya" aria-controls="swiper-wrapper-80c47299e250ea52" aria-disabled="true">
                <i class="bi bi-arrow-left"></i>
            </div>
            <div class="icon-arrow icon-arrow-right" tabindex="0" role="button" aria-label="Slide Selanjutnya" aria-controls="swiper-wrapper-80c47299e250ea52" aria-disabled="false">
                <i class="bi bi-arrow-right"></i>
            </div>
        </div>
    </section>

    <!-- Bagian Testimoni
    <section id="testimonials" class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-4">Apa Kata Pelanggan Kami</h2>
            <div class="row gy-4">
                <?php
                // Data testimoni
                $testimonials = [
                    ["name" => "Ayu", "text" => "Baju songket yang saya beli sangat nyaman dan berkualitas tinggi."],
                    ["name" => "Budi", "text" => "Kain songketnya sangat indah dan cocok untuk acara formal."],
                    ["name" => "Siti", "text" => "Pelayanan ramah dan produk berkualitas!"]
                ];

                foreach ($testimonials as $testimonial) {
                    echo '<div class="col-12 col-md-4">';
                    echo '<div class="card h-100">';
                    echo '<div class="card-body">';
                    echo '<blockquote class="blockquote">';
                    echo '<p class="mb-4">“' . $testimonial['text'] . '”</p>';
                    echo '<footer class="blockquote-footer">' . $testimonial['name'] . '</footer>';
                    echo '</blockquote>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </section> -->
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