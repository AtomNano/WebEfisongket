

<!DOCTYPE html>
<html lang="en">

<head>
    <title><?= isset($title) ? $title : 'Efi Songket - Baju Tradisional'; ?></title>
    <link rel="icon" href="gambarEfi/logoEfi.png" type="image/icon">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="format-detection" content="telephone=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="author" content="Efi Songket">
    <meta name="keywords" content="ecommerce, fashion, traditional clothing, songket">
    <meta name="description" content="Efi Songket - Menyediakan baju tradisional songket berkualitas tinggi.">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css" />

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@300;400;500;700&family=Marcellus&display=swap"
        rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" type="text/css" href="css/vendor.css">
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="stylesheet" href="toko.css">
</head>

<body class="<?= isset($body_class) ? $body_class : ''; ?>">

    <!-- Navbar Section with Hover Animation -->
    <nav class="navbar navbar-expand-lg bg-light text-uppercase fs-6 p-3 border-bottom">
        <div class="container-fluid">
            <!-- Brand Name -->
            <img src="gambarEfi/logoEfi.png" alt="Logo Efi Songket" class="navbar-logo" style="height: 50px; width: auto;">
            <a class="navbar-brand text-primary" href="index.php?p=home">Efi Songket</a>

            <!-- Toggler for Offcanvas Menu -->
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar"
                aria-controls="offcanvasNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Offcanvas Menu -->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar"
                aria-labelledby="offcanvasNavbarLabel">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="offcanvasNavbarLabel">Menu</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link animated-link <?= (isset($_GET['p']) && $_GET['p'] == 'home') ? 'active' : '' ?>"
                                href="index.php?p=home">Beranda</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link animated-link <?= (isset($_GET['p']) && $_GET['p'] == 'toko') ? 'active' : '' ?>"
                                href="index.php?p=toko">Toko</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link animated-link <?= (isset($_GET['p']) && $_GET['p'] == 'aboutus') ? 'active' : '' ?>"
                                href="index.php?p=aboutus">Tentang Kami</a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Right Side Icons -->
            <div class="d-flex align-items-center">
                <!-- Keranjang -->
                <a href="#" class="text-uppercase me-3" data-bs-toggle="offcanvas" data-bs-target="#offcanvasCart"
                    aria-controls="offcanvasCart" id="cartLink">
                    Keranjang (<span id="cartCount"><?= isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0 ?></span>)
                </a>
            </div>
        </div>
    </nav>
    <?php
    // Halaman dinamis berdasarkan parameter `p`
    $page = isset($_GET['p']) ? $_GET['p'] : 'home';
    switch ($page) {
        case 'home':
            include 'home.php'; // Memanggil konten spesifik untuk halaman home
            break;
        case 'toko':
            include 'toko.php';
            break;
        case 'aboutus':
            include 'aboutus.php';
            break;
        case 'login':
            include 'login.php'; 
            break;
        case 'register':
            include 'register.php'; 
            break;
        case 'product_detail':
            include 'product_detail.php'; 
            break;
        case 'forgot_password':
            include 'forgot_password.php'; 
            break;
        default:
            echo "<p class='text-center'>Halaman tidak ditemukan.</p>";
    }
    ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-..."></script>
</body>
</html>
</html>
