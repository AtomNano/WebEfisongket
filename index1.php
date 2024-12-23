<?php
// Variabel untuk judul dan kelas body
$title = 'Efi Songket - Koleksi Terbaru';
$body_class = 'homepage';

// Sertakan file header
include 'header.php';
?>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg bg-light text-uppercase fs-6 p-3 border-bottom">
    <div class="container-fluid">
        <!-- Brand Name -->
        <a class="navbar-brand text-primary" href="index.php?p=home">Efi Songket</a>

        <!-- Toggler for Collapsible Navbar -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Collapsible Navbar -->
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?= (isset($_GET['p']) && $_GET['p'] == 'home') ? 'active' : '' ?>"
                        href="index.php?p=home">Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (isset($_GET['p']) && $_GET['p'] == 'toko') ? 'active' : '' ?>"
                        href="index.php?p=toko">Toko</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (isset($_GET['p']) && $_GET['p'] == 'blog') ? 'active' : '' ?>"
                        href="index.php?p=blog">Blog</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (isset($_GET['p']) && $_GET['p'] == 'kontak') ? 'active' : '' ?>"
                        href="index.php?p=kontak">Kontak</a>
                </li>
            </ul>

            <!-- Search Form -->
            <form class="d-flex me-3" role="search">
                <input class="form-control me-2" type="search" placeholder="Cari produk..." aria-label="Search">
                <button class="btn btn-outline-primary" type="submit">Cari</button>
            </form>

            <!-- Login and Cart -->
            <ul class="navbar-nav mb-2 lg-0">
                <li class="nav-item">
                    <a href="cart.php" class="nav-link"><i class="bi bi-cart"></i> Keranjang</a>
                </li>
                <li class="nav-item">
                    <a href="login.php" class="nav-link"><i class="bi bi-box-arrow-in-right"></i> Login</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Content Section -->
<div class="container my-4">
    <?php
    // Halaman dinamis berdasarkan parameter `p`
    $page = isset($_GET['p']) ? $_GET['p'] : 'home';
    switch ($page) {
        case 'home':
            include 'home.php';
            break;
        case 'toko':
            include 'toko.php';
            break;
        case 'blog':
            include 'blog.php';
            break;
        case 'kontak':
            include 'kontak.php';
            break;
        default:
            echo "<p class='text-center'>Halaman tidak ditemukan.</p>";
    }
    ?>
</div>

<!-- Billboard Section -->
<section id="billboard" class="bg-light py-5">
    <div class="container">
        <div class="row justify-content-center">
            <h1 class="section-title text-center mt-1" data-aos="fade-up">Koleksi Terbaru</h1>
            <div class="col-md-6 text-center" data-aos="fade-up" data-aos-delay="300">
                <p>Temukan koleksi baju tradisional songket yang elegan dan berkualitas tinggi. Setiap produk dirancang
                    dengan penuh perhatian untuk memberikan kenyamanan dan gaya.</p>
            </div>
        </div>

        <!-- Featured Product Card -->
        <div class="custom-card d-flex flex-column flex-md-row align-items-center" data-aos="fade-up">
            <div class="text-section me-md-4 mb-4 mb-md-0" data-aos="fade-up">
                <p class="text-muted mb-1">Efi Songket Exclusive</p>
                <h2 class="font-weight-bold mb-2">Songket Balapak Banyak Motif</h2>
                <p>Kain songket asli dengan motif tradisional dan kualitas benang terbaik. Cocok untuk acara formal atau
                    koleksi pribadi.</p>
                <button class="btn btn-custom">Lihat Detail</button>
            </div>
            <div class="image-placeholder flex-grow-1 image-zoom-effect" data-aos="fade-up">
                <img src="gambarEfi/1.png" alt="Songket Efi" class="img-fluid landscape-image" />
            </div>
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

<!-- Footer -->
<?php include 'footer.php'; ?>