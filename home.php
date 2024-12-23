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

<!-- Bagian Konten -->
<div class="">

    <!-- Bagian Billboard -->
    <section id="billboard" class="bg-light py-2">
        <div class="container">
            <div class="row justify-content-center">
                <h1 class="section-title text-center" data-aos="fade-up">Koleksi Terbaru Efi Songket</h1>
                <div class="col-md-6 text-center" data-aos="fade-up" data-aos-delay="300">
                    <p>Temukan koleksi baju tradisional songket yang elegan dan berkualitas tinggi. Setiap produk
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
        <h2 class="text-center my-3 mb-5">Best Sellers Efi Songket</h2>

        <div class="row g-4">
            <?php
            if (!empty($topProducts)) {
                foreach ($topProducts as $product) {
                    ?>
                    <div class="col-md-3">
                        <div class="card h-100">
                            <img src="admin/uploads/<?= !empty($product['image']) ? $product['image'] : 'default.png' ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                                <p class="card-text flex-grow-1">Rp <?= number_format($product['price'], 0, ',', '.') ?></p>
                                <a href="index.php?p=toko&id=<?= $product['id'] ?>" class="btn btn-primary mt-auto">Lihat Detail</a>
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

    <!-- Bagian Belanja Berdasarkan Kategori -->
    <div class="container mt-5 mb-5" data-aos="fade-up" id="category">
        <h2 class="text-center my-3 mb-5">Belanja Berdasarkan Kategori</h2>

        <nav class="my-3">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 d-flex flex-row justify-content-center">
                <?php foreach ($categories as $category): ?>
                    <li class="nav-item">
                        <a class="nav-link animated-link text-dark px-3 fw-bold" href="#" data-category="<?= $category['id_kategori']; ?>"><?= ucfirst($category['nama_kategori']); ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>

        <div class="row g-4" id="product-grid">
            <!-- Produk akan dimuat di sini berdasarkan kategori yang dipilih -->
        </div>
    </div>
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
                                    <h3 class="font-weight-bold text-black">Keindahan Songket</h3>
                                    <p class="text-black h3">Menampilkan keanggunan dan tradisi dalam setiap helai kain.</p>
                                </div>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <div class="carousel-overlay">
                                <img src="gambarEfi/2.png" alt="Elegansi Tradisional" class="d-block w-100" style="height: 500px; object-fit: cover; filter: brightness(70%);">
                                <div class="carousel-caption d-none d-md-block">
                                    <h3 class="font-weight-bold text-black">Elegansi Tradisional</h3>
                                    <p class="text-black h3">Kain songket yang memadukan warisan budaya dengan gaya modern.</p>
                                </div>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <div class="carousel-overlay">
                                <img src="gambarEfi/3.png" alt="Kualitas Terbaik" class="d-block w-100" style="height: 500px; object-fit: cover; filter: brightness(70%);">
                                <div class="carousel-caption d-none d-md-block">
                                    <h3 class="font-weight-bold text-black">Kualitas Terbaik</h3>
                                    <p class="text-black h3">Setiap produk dibuat dengan bahan terbaik dan perhatian terhadap detail.</p>
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
                    $relatedProductsQuery = "SELECT * FROM products ORDER BY created_at DESC LIMIT 4";
                    $relatedProductsResult = mysqli_query($db, $relatedProductsQuery);

                    if ($relatedProductsResult && mysqli_num_rows($relatedProductsResult) > 0) {
                        while ($product = mysqli_fetch_assoc($relatedProductsResult)) {
                            ?>
                            <div class="swiper-slide" role="group" aria-label="1 / 4" style="width: 305.333px; margin-right: 10px;">
                                <div class="product-item image-zoom-effect link-effect">
                                    <div class="image-holder">
                                        <a href="index.php?p=toko&id=<?= $product['id'] ?>">
                                            <img src="admin/uploads/<?= !empty($product['image']) ? $product['image'] : 'default.png' ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-image img-fixed">
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

    <!-- Bagian Testimoni -->
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
    </section>
</div>

<script>
$(document).ready(function() {
    // Fungsi untuk memuat produk berdasarkan kategori
    function loadProducts(category) {
        console.log('Loading products for category:', category); // Debug log
        $.ajax({
            url: 'getProductsByCategory.php', // Ganti dengan path ke getProductsByCategory.php
            type: 'POST',
            data: { category: category },
            dataType: 'json',
            success: function(data) {
                console.log('Products loaded:', data); // Debug log
                var productGrid = $('#product-grid');
                productGrid.empty(); // Kosongkan grid produk

                // Loop melalui data produk dan tambahkan ke grid
                $.each(data, function(index, product) {
                    var productElement = `
                        <div class="col-md-3">
                            <div class="card h-100">
                                <img src="admin/uploads/${product.image}" class="card-img-top" alt="${product.name}">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title">${product.name}</h5>
                                    <p class="card-text flex-grow-1">Rp ${product.price.toLocaleString('id-ID')}</p>
                                    <a href="index.php?p=toko&id=${product.id}" class="btn btn-primary mt-auto">Lihat Detail</a>
                                </div>
                            </div>
                        </div>
                    `;
                    productGrid.append(productElement);
                });
            },
            error: function(xhr, status, error) {
                console.error('Gagal memuat produk:', status, error); // Debug log
            }
        });
    }

    // Event handler untuk klik kategori
    $('.nav-link').click(function(e) {
        e.preventDefault();
        var category = $(this).data('category');
        loadProducts(category);
    });

    // Muat produk awal (misalnya, kategori 'songket')
    loadProducts(1); // 1 adalah id_kategori untuk 'songket'
});
</script>