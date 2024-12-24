<?php
// Variabel untuk judul dan kelas body
$title = 'Footer';
$body_class = 'homepage';
// Sertakan file header
?>
<style>
    footer p {
        margin: 0;
        padding: 0;
    }
</style>

<footer class="bg-light py-5">
    <div class="container">
        <div class="row d-flex flex-wrap justify-content-between">
            <div class="col-md-3 col-sm-6">
                <h5 class="widget-title text-uppercase mb-4">Tentang Kami</h5>
                <p>Efi Songket menyediakan baju tradisional songket berkualitas tinggi untuk kebutuhan Anda.</p>
                <ul class="list-unstyled d-flex gap-2">
                    <li><a href="https://www.tiktok.com/@efi.songket?_t=8nlCsqc7Vcw&_r=1" class="text-secondary"><i class="bi bi-facebook"></i></a></li>
                    <li><a href="https://www.facebook.com/efiyasnidar.indra?mibextid=ZbWKwL" class="text-secondary"><i class="bi bi-tiktok"></i></a></li>
                    <li><a href="https://www.instagram.com/efisongketpandaisikek/" class="text-secondary"><i class="bi bi-instagram"></i></a></li>
                    <li><a href="https://www.youtube.com/@RamadaniPutri-0277" class="text-secondary"><i class="bi bi-youtube"></i></a></li>
                    <li><a href="https://api.whatsapp.com/send?phone=6285261093463&text=Hai%2C%20bagaimana%20saya%20bisa%20memesan%3F" class="text-secondary" target="_blank"><i class="bi bi-whatsapp"></i></a></li>
                </ul>
            </div>

            <div class="col-md-3 col-sm-6">
                <h5 class="widget-title text-uppercase mb-4">Tautan Cepat</h5>
                <ul class="list-unstyled">
                    <li><a href="index.php" class="text-decoration-none">Beranda</a></li>
                    <li><a href="about.php" class="text-decoration-none">Tentang Kami</a></li>
                    <li><a href="shop.php" class="text-decoration-none">Produk</a></li>
                    <li><a href="contact.php" class="text-decoration-none">Hubungi Kami</a></li>
                </ul>
            </div>

            <div class="col-md-3 col-sm-6">
                <h5 class="widget-title text-uppercase mb-4">Hubungi Kami</h5>
                <p>Email: <a href="mailto:info@efisongket.com" class="text-decoration-none">info@efisongket.com</a></p>
                <p>Telepon: <a href="tel: +62 852-6109-3463" class="text-decoration-none"> +62 852-6109-3463</a></p>
                <p>Alamat: Perum Bunda Persada Blok I No. 2</p>
                <p>Kel. Gunuang Sarik,</p>
                <p>Kec. Kuranji Balai Baru,</p>
                <p>Kota Padang, Sumatera Barat, Indonesia 25173.</p>
            </div>

            <div class="col-md-3 col-sm-6">
                <h5 class="widget-title text-uppercase mb-4">Newsletter</h5>
                <form action="subscribe.php" method="post" class="d-flex gap-2">
                    <input type="email" name="email" class="form-control" placeholder="Masukkan email Anda">
                    <button type="submit" class="btn btn-primary rounded">Daftar</button>
                </form>
            </div>
        </div>
        <div class="row mt-4 border-top pt-4">
            <div class="col-md-6">
                <p class="mb-0">&copy; <?= date('Y'); ?> Efi Songket. Semua Hak Dilindungi.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <!-- Optional Additional Content Here -->
            </div>
        </div>
    </div>
</footer>

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
