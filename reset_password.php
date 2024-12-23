<?php
include 'phpconection.php';

$message = '';
$token = isset($_GET['token']) ? $_GET['token'] : '';

// Cek apakah token ada
if ($token) {
    // Debugging: tampilkan token yang diterima
    echo "Token received: " . htmlspecialchars($token); 

    // Query untuk mencari token di database
    $query = "SELECT * FROM user WHERE reset_token = ? AND reset_token_expiry > NOW()";
    $stmt = $db->prepare($query);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Jika token valid
        if (isset($_POST['submit'])) {
            // Ambil input password baru
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];

            // Validasi apakah password baru dan konfirmasi cocok
            if ($new_password !== $confirm_password) {
                $message = "<div class='alert alert-danger'>Password baru dan konfirmasi password tidak cocok!</div>";
            } else {
                // Enkripsi password baru
                $new_password_hashed = password_hash($new_password, PASSWORD_DEFAULT);

                // Update password di database
                $update_query = "UPDATE user SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE reset_token = ?";
                $stmt = $db->prepare($update_query);
                $stmt->bind_param("ss", $new_password_hashed, $token);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    $message = "<div class='alert alert-success'>Password berhasil diperbarui! Silakan login dengan password baru Anda.</div>";
                } else {
                    $message = "<div class='alert alert-danger'>Gagal memperbarui password. Silakan coba lagi.</div>";
                }
            }
        }
    } else {
        $message = "<div class='alert alert-danger'>Token tidak valid atau sudah kedaluwarsa.</div>";
    }
} else {
    $message = "<div class='alert alert-danger'>Token tidak ditemukan!</div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
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
                        <h2 class="mt-2">Reset Password</h2>
                    </div>

                    <!-- Pesan -->
                    <?php if ($message) echo $message; ?>

                    <form method="POST" action="">
                        <!-- Password Baru -->
                        <div class="form-outline mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" id="new_password" name="new_password" class="form-control" placeholder="Enter your new password" required>
                        </div>

                        <!-- Konfirmasi Password Baru -->
                        <div class="form-outline mb-4">
                            <label for="confirm_password" class="form-label">Confirm New Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirm your new password" required>
                        </div>

                        <!-- Tombol -->
                        <div class="d-grid">
                            <button type="submit" name="submit" class="btn btn-primary">Reset Password</button>
                        </div>
                    </form>

                    <!-- Tautan Kembali ke Login -->
                    <div class="text-center mt-4">
                        <a href="index.php?p=login" class="text-muted">Back to Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.js"></script>
</body>
</html>
