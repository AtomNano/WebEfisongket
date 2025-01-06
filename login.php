<?php

include 'phpconection.php'; // Menghubungkan ke database

$title = 'Efi Songket - Koleksi Terbaru';
$error = '';

// Pastikan form login di-submit
if (isset($_POST['submit'])) {
    // Menangkap dan membersihkan input
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $password = $_POST['password'];

    // Gunakan prepared statements untuk query agar lebih aman
    $query = "SELECT * FROM user WHERE email=?";
    $stmt = mysqli_prepare($db, $query);
    mysqli_stmt_bind_param($stmt, 's', $email); // Bind email ke parameter
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Jika ada pengguna yang ditemukan
    if ($result && mysqli_num_rows($result) > 0) {
        $data_login = mysqli_fetch_assoc($result);

        // Verifikasi password yang dimasukkan dengan password yang ada di database
        if (password_verify($password, $data_login['password'])) {
            // Update waktu login terakhir
            $current_time = date('Y-m-d H:i:s');
            $update_query = "UPDATE user SET last_login=? WHERE id=?";
            $update_stmt = mysqli_prepare($db, $update_query);
            mysqli_stmt_bind_param($update_stmt, 'si', $current_time, $data_login['id']);
            mysqli_stmt_execute($update_stmt);

            // Buat session untuk pengguna
            $_SESSION['user_id'] = $data_login['id'];
            $_SESSION['email'] = $data_login['email'];
            $_SESSION['name'] = $data_login['name']; // Pastikan 'name' ada dalam hasil query
            $_SESSION['level'] = $data_login['level']; // Gunakan 'level' untuk role

            // Pastikan bahwa 'level' sudah ada dalam session dan redirect sesuai level
            if (isset($_SESSION['level'])) {
                if ($_SESSION['level'] == 'admin') {
                    // Redirect ke halaman admin jika level adalah admin
                    header('Location: admin/index.php');
                } else {
                    // Redirect ke halaman utama untuk pengguna biasa
                    header('Location: index.php?p=home');
                }
            } else {
                // Jika level tidak ditemukan dalam session
                echo "<script>alert('Level tidak ditemukan!');</script>";
            }
            exit;
        } else {
            // Password salah
            echo "<script>alert('Password salah!');</script>";
        }
    } else {
        // Email tidak ditemukan
        echo "<script>alert('Email tidak ditemukan!');</script>";
    }
}

?>
<style>
    body {
        margin: 0;
        padding: 0;
        height: 100vh;
    }

    section {
        padding: 60px;
    }

    .background {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: url('gambarEfi/A_modern_web_background_inspired_by_Indonesian_tra.png.webp') no-repeat center center fixed;
        background-size: cover;
        filter: blur(8px);
        z-index: -1;
    }

    .container {
        position: relative;
        z-index: 1;
    }

    .card {
        max-height: 70vh;
    }

    .card img {
        object-fit: cover;
        height: 100%;
    }
</style>

<div class="background"></div>

<section class="d-flex align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-lg rounded-5 overflow-hidden">
                    <div class="row g-0">
                        <!-- Kolom Gambar -->
                        <div class="col-md-6 d-none d-md-block">
                            <img src="gambarEfi/A_traditional_yet_modern_design_featuring_a_promin.png.webp" 
                                 alt="Efi Songket" 
                                 class="img-fluid h-100" 
                                 style="object-fit: cover;">
                        </div>
                        <!-- Kolom Form Login -->
                        <div class="col-md-6">
                            <div class="card-body p-5">
                                <h2 class="text-center fw-bold">Masuk</h2>
                                <h2 class="text-center mb-4 fw-bold">Efi Songket</h2>
                                <form method="POST">
                                    <!-- Input Email -->
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Alamat Email</label>
                                        <input type="email" id="email" name="email" class="form-control" placeholder="Masukkan email Anda" required>
                                    </div>
                                    <!-- Input Password -->
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Kata Sandi</label>
                                        <input type="password" id="password" name="password" class="form-control" placeholder="Masukkan kata sandi Anda" minlength="8" required>
                                    </div>
                                    <!-- Tombol Login -->
                                    <div class="d-grid">
                                        <button type="submit" name="submit" class="btn btn-primary rounded-pill">Masuk</button>
                                    </div>
                                </form>
                                <div class="text-center mt-3">
                                    <a href="index.php?p=forgot_password" class="text-muted">Lupa kata sandi?</a><br>
                                    <span>Belum punya akun? <a href="index.php?p=register" class="text-primary">Daftar di sini</a></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

