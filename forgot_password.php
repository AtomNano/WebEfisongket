<?php
include 'phpconection.php';

$message = '';

// Periksa jika form telah disubmit
if (isset($_POST['submit'])) {
    // Ambil input dari form
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Periksa apakah password baru dan konfirmasi password baru cocok
    if ($new_password !== $confirm_password) {
        $message = "<div class='alert alert-danger'>Password baru dan konfirmasi password tidak cocok!</div>";
    } else {
        $new_password_hashed = password_hash($new_password, PASSWORD_DEFAULT);  // Enkripsi password baru

        // Query untuk mencari email
        $query = "SELECT * FROM user WHERE email = '$email'";
        $result = mysqli_query($db, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            // Update password baru
            $update_query = "UPDATE user SET password = '$new_password_hashed' WHERE email = '$email'";
            if (mysqli_query($db, $update_query)) {
                $message = "<div class='alert alert-success'>Password berhasil diubah!</div>";
            } else {
                $message = "<div class='alert alert-danger'>Gagal memperbarui password. Silakan coba lagi.</div>";
            }
        } else {
            $message = "<div class='alert alert-danger'>Email tidak ditemukan!</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center align-items-center">
            <div class="col-md-6">
                <div class="card form-card p-4">
                    <div class="text-center mb-4">
                        <i class="bi bi-shield-lock-fill form-icon"></i>
                        <h2 class="mt-2">Lupa Password</h2>
                    </div>

                    <!-- Pesan -->
                    <?php if ($message) echo $message; ?>

                    <form method="POST" action="">
                        <!-- Email -->
                        <div class="form-outline mb-3">
                            <label for="email" class="form-label">Alamat Email</label>
                            <input type="email" id="email" name="email" class="form-control" placeholder="Masukkan email Anda" required>
                        </div>

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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.js"></script>
</body>
</html>