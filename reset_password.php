<?php
date_default_timezone_set('Asia/Jakarta'); // Set to your desired time zone
include 'phpconection.php';

$message = '';

// Periksa jika form telah disubmit
if (isset($_POST['submit'])) {
    // Ambil input dari form
    $verification_code = mysqli_real_escape_string($db, $_POST['verification_code']);
    $new_password = mysqli_real_escape_string($db, $_POST['new_password']);
    $confirm_password = mysqli_real_escape_string($db, $_POST['confirm_password']);

    // Periksa apakah password baru dan konfirmasi password baru cocok
    if ($new_password !== $confirm_password) {
        $message = "<div class='alert alert-danger'>Password baru dan konfirmasi password tidak cocok!</div>";
    } else {
        // Query untuk mencari verification code dan memeriksa apakah masih berlaku
        $query = "SELECT * FROM user WHERE reset_token = '$verification_code' AND reset_token_expiry > NOW()";
        $result = mysqli_query($db, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $data = mysqli_fetch_assoc($result);
            $new_password_hashed = password_hash($new_password, PASSWORD_DEFAULT);  // Enkripsi password baru

            // Update password baru dan hapus verification code
            $update_query = "UPDATE user SET password = '$new_password_hashed', reset_token = NULL, reset_token_expiry = NULL WHERE reset_token = '$verification_code'";
            if (mysqli_query($db, $update_query)) {
                $message = "<div class='alert alert-success'>Password berhasil diubah!</div>";
            } else {
                $message = "<div class='alert alert-danger'>Gagal memperbarui password. Silakan coba lagi.</div>";
            }
        } else {
            $message = "<div class='alert alert-danger'>Kode verifikasi tidak valid atau telah kedaluwarsa!</div>";
        }
    }
}
?>

<style>

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
        margin: 50px auto;
    }

    .card {
        
        height: auto;
    }

    .card img {
        object-fit: cover;
        height: 100%;
        width: 100%;
    }

    .card-body {
        padding: 2rem;
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
                        <!-- Kolom Form -->
                        <div class="col-md-6">
                            <div class="card-body p-5">
                                <h3 class="text-center fw-bold">Reset Password</h3>
                                <h3 class="text-center fw-bold">Efi Songket</h3>
                                <!-- Pesan -->
                                <?php if ($message) echo $message; ?>
                                <form method="POST" action="">
                                    <!-- Verification Code -->
                                    <div class="mb-3">
                                        <label for="verification_code" class="form-label">Kode Verifikasi</label>
                                        <input type="text" id="verification_code" name="verification_code" class="form-control" placeholder="Masukkan kode verifikasi Anda" required>
                                    </div>
                                    <!-- Password Baru -->
                                    <div class="mb-3">
                                        <label for="new_password" class="form-label">Password Baru</label>
                                        <input type="password" id="new_password" name="new_password" class="form-control" placeholder="Masukkan password baru Anda" minlength="8" required>
                                    </div>
                                    <!-- Konfirmasi Password Baru -->
                                    <div class="mb-4">
                                        <label for="confirm_password" class="form-label">Konfirmasi Password Baru</label>
                                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Konfirmasi password baru Anda" minlength="8" required>
                                    </div>
                                    <!-- Tombol -->
                                    <div class="d-grid">
                                        <button type="submit" name="submit" class="btn btn-primary rounded-pill">Reset Password</button>
                                    </div>
                                </form>
                                <!-- Tautan -->
                                <div class="text-center mt-4">
                                    <a href="index.php?p=login" class="text-muted">Kembali ke Login</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>