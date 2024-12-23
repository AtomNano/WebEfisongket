<?php
include 'phpconection.php';

$message = '';

// Periksa jika form telah disubmit
if (isset($_POST['submit'])) {
    // Ambil input dari form
    $reset_token = mysqli_real_escape_string($db, $_POST['reset_token']);
    $new_password = mysqli_real_escape_string($db, $_POST['new_password']);
    $confirm_password = mysqli_real_escape_string($db, $_POST['confirm_password']);

    // Periksa apakah password baru dan konfirmasi password baru cocok
    if ($new_password !== $confirm_password) {
        $message = "<div class='alert alert-danger'>Password baru dan konfirmasi password tidak cocok!</div>";
    } else {
        // Query untuk mencari reset token dan memeriksa apakah masih berlaku
        $query = "SELECT * FROM user WHERE reset_token = '$reset_token' AND reset_token_expiry > NOW()";
        $result = mysqli_query($db, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $new_password_hashed = password_hash($new_password, PASSWORD_DEFAULT);  // Enkripsi password baru

            // Update password baru dan hapus reset token
            $update_query = "UPDATE user SET password = '$new_password_hashed', reset_token = NULL, reset_token_expiry = NULL WHERE reset_token = '$reset_token'";
            if (mysqli_query($db, $update_query)) {
                $message = "<div class='alert alert-success'>Password berhasil diubah!</div>";
            } else {
                $message = "<div class='alert alert-danger'>Gagal memperbarui password. Silakan coba lagi.</div>";
            }
        } else {
            $message = "<div class='alert alert-danger'>Token reset tidak valid atau telah kedaluwarsa!</div>";
        }
    }
}
?>

<style>
    body {
        background-color: #f8f9fa;
    }
    .form-card {
        border-radius: 15px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
    }
    .form-icon {
        font-size: 50px;
        color: #007bff;
    }
    .btn-primary {
        background-color: #007bff;
        border: none;
    }
    .btn-primary:hover {
        background-color: #0056b3;
    }
</style>

<div class="container py-5">
    <div class="row justify-content-center align-items-center">
        <div class="col-md-6">
            <div class="card form-card p-4">
                <div class="text-center mb-4">
                    <i class="bi bi-shield-lock-fill form-icon"></i>
                    <h2 class="mt-2">Reset Password</h2>
                </div>

                <!-- Pesan -->
                <?php if ($message) echo $message; ?>

                <form method="POST" action="">
                    <!-- Reset Token -->
                    <input type="hidden" name="reset_token" value="<?php echo $_GET['token']; ?>">

                    <!-- Password Baru -->
                    <div class="form-outline mb-3">
                        <label for="new_password" class="form-label">Password Baru</label>
                        <input type="password" id="new_password" name="new_password" class="form-control" placeholder="Masukkan password baru Anda" required>
                    </div>

                    <!-- Konfirmasi Password Baru -->
                    <div class="form-outline mb-4">
                        <label for="confirm_password" class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Konfirmasi password baru Anda" required>
                    </div>

                    <!-- Tombol -->
                    <div class="d-grid">
                        <button type="submit" name="submit" class="btn btn-primary">Reset Password</button>
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