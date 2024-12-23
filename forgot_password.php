<?php
include 'phpconection.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$message = '';

// Periksa jika form telah disubmit
if (isset($_POST['submit'])) {
    // Ambil input dari form
    $email = mysqli_real_escape_string($db, $_POST['email']);

    // Generate reset token and expiry time
    $reset_token = bin2hex(random_bytes(16));
    $reset_token_expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

    // Store reset token and expiry time in the database
    $query = "UPDATE user SET reset_token = '$reset_token', reset_token_expiry = '$reset_token_expiry' WHERE email = '$email'";
    if (mysqli_query($db, $query)) {
        // Send reset token to email
        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'your-email@gmail.com';
            $mail->Password = 'your-email-password';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            //Recipients
            $mail->setFrom('your-email@gmail.com', 'Your Name');
            $mail->addAddress($email);

            //Content
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body = "Click the link below to reset your password:<br><a href='http://yourwebsite.com/reset_password.php?token=$reset_token'>Reset Password</a>";

            $mail->send();
            $message = "<div class='alert alert-success'>A password reset link has been sent to your email.</div>";
        } catch (Exception $e) {
            $message = "<div class='alert alert-danger'>Message could not be sent. Mailer Error: {$mail->ErrorInfo}</div>";
        }
    } else {
        $message = "<div class='alert alert-danger'>Failed to store reset token in the database.</div>";
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

                    <!-- Tombol -->
                    <div class="d-grid">
                        <button type="submit" name="submit" class="btn btn-primary">Send Reset Link</button>
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