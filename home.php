<?php
include 'phpconection.php';

// Fetch products to display on home.php
$query = "SELECT * FROM products";
$result = mysqli_query($db, $query);


// Fetch categories from the database
$categoryQuery = "SELECT * FROM kategori";
$categoryResult = mysqli_query($db, $categoryQuery);
$categories = mysqli_fetch_all($categoryResult, MYSQLI_ASSOC);

// Fetch carousel items from the database
$carouselQuery = "SELECT * FROM products ORDER BY created_at DESC LIMIT 3";
$carouselResult = mysqli_query($db, $carouselQuery);
$carouselItems = mysqli_fetch_all($carouselResult, MYSQLI_ASSOC);
?>

<!-- Content Section -->
<div class="">

    <!-- Billboard Section -->
    <section id="billboard" class="bg-light py-2">
        <div class="container">
            <div class="row justify-content-center">
                <h1 class="section-title text-center" data-aos="fade-up">Koleksi Terbaru Efi Songket</h1>
                <div class="col-md-6 text-center" data-aos="fade-up" data-aos-delay="300">
                    <p>Temukan koleksi baju tradisional songket yang elegan dan berkualitas tinggi. Setiap produk
                        dirancang
                        dengan penuh perhatian untuk memberikan kenyamanan dan gaya.</p>
                </div>
            </div>

            
      <!-- card -->

      <div class="custom-card  d-flex flex-column flex-md-row align-items-center" data-aos="fade-up">
        <!-- Text Section -->
        <div class="text-section mr-md-4 mb-4 mb-md-0" data-aos="fade-up">
            <p class="text-muted mb-1">Efi Songket Exclusive</p>
            <h2 class="font-weight-bold mb-2">Songket Balapak Banyak Motif</h2>
            <p class="text">Kain songket asli dengan motif tradisional dan kualitas benang terbaik. Cocok untuk acara formal atau koleksi pribadi.</p>
                <a href="index.php?p=toko" class="btn btn-primary rounded-5">Lihat Detail</a>
            <!-- Dots -->
        
        </div>
        <!-- Image Section -->
        <div class="image-placeholder flex-grow-1 image-zoom-effect" data-aos="fade-up">
            <img src="gambarEfi/1.png" alt="Songket Efi" class="img-fluid landscape-image" />
        </div>
    </div>
    
      <!-- end card -->
    </section>

    <div class="container mt-5 mb-5" data-aos="fade-up" id="category">
    <h2 class="text-center my-3 mb-5">Best Sellers Efi Songket</h2>

    <div class="row mb-3">
        <div class="col-6">
            <h5 class="shop-by-category text-uppercase">Shop by Category</h5>
        </div>
        <div class="col-6 text-end">
            <button class="btn btn-outline-primary rounded-5">
                Lihat Semua <i class="bi bi-arrow-right"></i>
            </button>
        </div>
    </div>

    <nav class="my-3">
        <ul class="navbar-nav mx-auto mb-2 mb-lg-0 d-flex flex-row justify-content-center">
            <?php foreach ($categories as $category): ?>
                <li class="nav-item">
                    <a class="nav-link animated-link text-dark px-3 fw-bold" href="#" data-category="<?= $category['nama_kategori']; ?>"><?= ucfirst($category['nama_kategori']); ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <div class="row g-4" id="product-grid">
        </div>
</div>

<!-- END BEST SELLER -->


    <!-- END BEST SELLER -->

    <!-- Start corousel -->
    <?php
// Definisikan variabel carouselItems
$carouselItems = [
    [
        'image' => 'gambarEfi/1.png',
        'title' => 'Koleksi Efi Songket',
        'description' => 'Temukan keihan kain songket yang elegan dan berkualitas tinggi.'
    ],
    [
        'image' => 'gambarEfi/2.png',
        'title' => 'Baju Tradisional',
        'description' => 'Baju songket tradisional dengan desain yang memukau dan nyaman dipakai.'
    ],
    [
        'image' => 'gambarEfi/3.png',
        'title' => 'Aksesori Songket',
        'description' => 'Lengkapi penampilan Anda dengan aksesori songket yang menawan.'
    ]
];
?>

<style>
    .carousel-item {
    position: relative;
}

.carousel-overlay::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5); /* Transparansi 50% */
    z-index: 1;
}

.carousel-caption {
    position: absolute;
    z-index: 2; /* Pastikan teks berada di atas overlay */
}

</style>

    <!-- Start Carousel -->
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
                        <?php foreach ($carouselItems as $index => $item): ?>
                        <li data-bs-target="#carouselExampleIndicators" data-bs-slide-to="<?= $index; ?>"
                            class="<?= $index === 0 ? 'active' : ''; ?>"></li>
                        <?php endforeach; ?>
                    </ol>
                    <div class="carousel-inner rounded-5">
                        <?php foreach ($carouselItems as $index => $item): ?>
                        <div class="carousel-item <?= $index === 0 ? 'active' : ''; ?>">
                            <div class="carousel-overlay">
                                <img src="<?= $item['image']; ?>" alt="<?= $item['title']; ?>" class="d-block w-100"
                                    style="height: 500px; object-fit: cover;">
                                <div class="carousel-caption d-none d-md-block">
                                    <h3 class="font-weight-bold text-white"><?= $item['title']; ?></h3>
                                    <p class="text-white h3"><?= $item['description']; ?></p>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators"
                        data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators"
                        data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- end -->


    <section id="related-products" class="related-products product-carousel py-5 position-relative overflow-hidden">
        <div class="container">
            <div class="d-flex flex-wrap justify-content-between align-items-center mt-5 mb-3">
                <h4 class="text-uppercase">Anda Mungkin Juga Suka</h4>
                <a href="index.php?p=toko" class="btn-link">Lihat Semua Produk</a>
            </div>
            <div class="swiper product-swiper open-up swiper-initialized swiper-horizontal swiper-backface-hidden aos-init aos-animate"
                data-aos="zoom-out">
                <div class="swiper-wrapper d-flex" id="swiper-wrapper-80c47299e250ea52" aria-live="polite">
                    <div class="swiper-slide swiper-slide-active" role="group" aria-label="1 / 4"
                        style="width: 305.333px; margin-right: 10px;">
                        <div class="product-item image-zoom-effect link-effect">
                            <div class="image-holder">
                                <a href="index.php?p=toko">
                                    <img src="gambarEfi/Songket Pandai Sikek Tabur Kombinasi Suji Cair (3.000.000).jpg"
                                        alt="product" class="product-image img-fixed">
                                </a>
                                <a href="index.php?p=toko" class="btn-icon btn-wishlist">
                                    <i class="bi bi-heart"></i>
                                </a>
                                <div class="product-content">
                                    <h5 class="text-uppercase fs-5 mt-3">
                                        <a href="index.php?p=toko">Baju Songket Modern</a>
                                    </h5>
                                    <a href="index.php?p=toko" class="text-decoration-none" data-after="Add to cart"><span>Rp
                                            85.000</span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide swiper-slide-next" role="group" aria-label="2 / 4"
                        style="width: 305.333px; margin-right: 10px;">
                        <div class="product-item image-zoom-effect link-effect">
                            <div class="image-holder">
                                <a href="index.php?p=toko">
                                    <img src="gambarEfi/Songket Balapak Penuh Motif Banyak (Rp 4.000.000).jpg"
                                        alt="product" class="product-image img-fixed">
                                </a>
                                <a href="index.php?p=toko" class="btn-icon btn-wishlist">
                                    <i class="bi bi-heart"></i>
                                </a>
                                <div class="product-content">
                                    <h5 class="text-uppercase fs-5 mt-3">
                                        <a href="index.php?p=toko">Kain Songket Tradisional</a>
                                    </h5>
                                    <a href="#" class="text-decoration-none" data-after="Add to cart"><span>Rp
                                            75.000</span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide" role="group" aria-label="3 / 4"
                        style="width: 305.333px; margin-right: 10px;">
                        <div class="product-item image-zoom-effect link-effect">
                            <div class="image-holder">
                                <a href="index.php?p=toko">
                                    <img src="gambarEfi/Songket Balapak Penuh Selendang Suji Cair Motif Sisiak Ikan (3.700.000).jpg"
                                        alt="product" class="product-image img-fixed">
                                </a>
                                <a href="index.php?p=toko" class="btn-icon btn-wishlist">
                                    <i class="bi bi-heart"></i>
                                </a>
                                <div class="product-content">
                                    <h5 class="text-uppercase fs-5 mt-3">
                                        <a href="index.php?p=toko">Aksesori Songket</a>
                                    </h5>
                                    <a href="index.php?p=toko" class="text-decoration-none" data-after="Add to cart"><span>Rp
                                            40.000</span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide" role="group" aria-label="4 / 4"
                        style="width: 305.333px; margin-right: 10px;">
                        <div class="product-item image-zoom-effect link-effect">
                            <div class="image-holder">
                                <a href="index.php?p=toko">
                                    <img src="gambarEfi/Songket Balapak Ungu Selendang Kombinasi Suji Cair (3.500.000).jpg"
                                        alt="product" class="product-image img-fixed">
                                </a>
                                <a href="index.php?p=toko" class="btn-icon btn-wishlist">
                                    <i class="bi bi-heart"></i>
                                </a>
                                <div class="product-content">
                                    <h5 class="text-uppercase fs-5 mt-3">
                                        <a href="index.html">Sepatu Tradisional</a>
                                    </h5>
                                    <a href="index.php?p=toko" class="text-decoration-none" data-after="Add to cart"><span>Rp
                                            90.000</span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-pagination"></div>
                <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span>
            </div>
            <div class="icon-arrow icon-arrow-left swiper-button-disabled" tabindex="-1" role="button"
                aria-label="Previous slide" aria-controls="swiper-wrapper-80c47299e250ea52" aria-disabled="true">
                <i class="bi bi-arrow-left"></i>
            </div>
            <div class="icon-arrow icon-arrow-right" tabindex="0" role="button" aria-label="Next slide"
                aria-controls="swiper-wrapper-80c47299e250ea52" aria-disabled="false">
                <i class="bi bi-arrow-right"></i>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section id="testimonials" class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-4">Apa Kata Pelanggan Kami</h2>
            <div class="row gy-4">
                <?php
                // Testimonial data
                $testimonials = [
                    ["name" => "Ayu", "text" => "Baju songket yang saya beli sangat nyaman dan berkualitas tinggi."],
                    ["name" => "Budi", "text" => "Kain songketnya sangat indah dan cocok untuk acara formal."],
                    ["name" => "Siti", "text" => "Pelayanan ramah dan produk berkualitas!"]
                ];

                foreach ($testimonials
 as $testimonial) {
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
        $.ajax({
            url: 'getProducts.php', // Ganti dengan path ke getProducts.php
            type: 'GET',
            data: { category: category },
            dataType: 'json',
            success: function(data) {
                var productGrid = $('#product-grid');
                productGrid.empty(); // Kosongkan grid produk

                // Loop melalui data produk dan tambahkan ke grid
                $.each(data, function(index, product) {
                    var productElement = `
                        <div class="col-md-3">
                            <div class="card h-100">
                                <img src="${product.image}" class="card-img-top" alt="${product.name}">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title">${product.name}</h5>
                                    <p class="card-text flex-grow-1">${product.price}</p>
                                    <a href="#" class="btn btn-primary mt-auto">Lihat Detail</a>
                                </div>
                            </div>
                        </div>
                    `;
                    productGrid.append(productElement);
                });
            },
            error: function() {
                console.error('Gagal memuat produk.');
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
    loadProducts('songket');
});
</script>