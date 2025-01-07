<?php
// Variabel untuk judul dan kelas body
$title = 'Footer';
$body_class = 'homepage';
// Sertakan file header
?>
<footer class="bg-light py-5">
    <div class="container">
        <!-- Footer Main Row -->
        <div class="row d-flex flex-wrap justify-content-between">
            <!-- Tentang Kami -->
            <div class="col-md-4 col-sm-6 mb-4">
                <h5 class="widget-title text-uppercase mb-4">Tentang Kami</h5>
                <p>Efi Songket menyediakan baju tradisional songket berkualitas tinggi untuk kebutuhan Anda.</p>
                <ul class="list-unstyled d-flex gap-3">
                    <li><a href="https://www.facebook.com/efiyasnidar.indra?mibextid=ZbWKwL" class="text-secondary fs-5"><i class="bi bi-facebook"></i></a></li>
                    <li><a href="https://www.tiktok.com/@efi.songket?_t=8nlCsqc7Vcw&_r=1" class="text-secondary fs-5"><i class="bi bi-tiktok"></i></a></li>
                    <li><a href="https://www.instagram.com/efisongketpandaisikek/" class="text-secondary fs-5"><i class="bi bi-instagram"></i></a></li>
                    <li><a href="https://www.youtube.com/@RamadaniPutri-0277" class="text-secondary fs-5"><i class="bi bi-youtube"></i></a></li>
                    <li><a href="https://api.whatsapp.com/send?phone=6285261093463&text=Hai%2C%20bagaimana%20saya%20bisa%20memesan%3F" class="text-secondary fs-5" target="_blank"><i class="bi bi-whatsapp"></i></a></li>
                </ul>
            </div>

            <!-- Tautan Cepat -->
            <div class="col-md-4 col-sm-6 mb-4">
                <h5 class="widget-title text-uppercase mb-4">Tautan Cepat</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="index.php" class="text-decoration-none text-dark">Beranda</a></li>
                    <li class="mb-2"><a href="index.php?p=toko" class="text-decoration-none text-dark">Produk</a></li>
                    <li class="mb-2"><a href="index.php?p=aboutus" class="text-decoration-none text-dark">Tentang Kami</a></li>
                </ul>
            </div>

            <!-- Hubungi Kami -->
            <div class="col-md-4 col-sm-6 mb-4">
                <h5 class="widget-title text-uppercase mb-4">Hubungi Kami</h5>
                <p>Email: <a href="mailto:info@efisongket.com" class="text-decoration-none text-dark">info@efisongket.com</a></p>
                <p>Telepon: <a href="tel:+6285261093463" class="text-decoration-none text-dark">+62 852-6109-3463</a></p>
                <a href="https://maps.app.goo.gl/hczgQapzW6Lp6R1h7">
                    <p>Alamat: <br>
                        Perum Bunda Persada Blok I No. 2<br>
                        Kel. Gunuang Sarik, Kec. Kuranji Balai Baru,<br>
                        Kota Padang, Sumatera Barat, Indonesia 25173.
                    </p>

                </a>
            </div>
        </div>

        <!-- Footer Bottom Row -->
        <div class="row mt-4 pt-4 border-top">
            <div class="col-md-6">
                <p class="mb-0">&copy; <?= date('Y'); ?> Efi Songket. Semua Hak Dilindungi.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <p class="mb-0">Didesain dengan ❤️ oleh Efi Songket Team</p>
            </div>
        </div>
    </div>
</footer>

<style>
    footer {
        background-color: #f8f9fa;
        color: #6c757d;
    }
    footer .widget-title {
        font-weight: 600;
        color: #343a40;
    }
    footer ul li a:hover {
        color: #007bff;
    }
    footer ul {
        padding-left: 0;
    }
    footer ul li {
        list-style: none;
    }
    footer .btn-primary {
        background-color: #343a40;
        border: none;
    }
    footer .btn-primary:hover {
        background-color: #495057;
    }
</style>


<!-- JavaScript Files -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
<script src="js/jquery.min.js"></script>
<script src="js/plugins.js"></script>
<script src="js/script.min.js"></script>
<!-- Swiper and Other JS Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.1/dist/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.15.3/dist/sweetalert2.all.min.js"></script>
<script>
    AOS.init();
</script>
<script src="js/products.js"></script>
</body>
</html>
