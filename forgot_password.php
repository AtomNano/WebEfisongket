<?php

include 'phpconection.php'; // Menghubungkan ke database

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$message = '';

// Set the default time zone
date_default_timezone_set('Asia/Jakarta');

// Pastikan form telah disubmit
if (isset($_POST['submit'])) {
    // Ambil input dari form
    $email = mysqli_real_escape_string($db, $_POST['email']);

    // Generate 6-digit verification code
    $verification_code = rand(100000, 999999);
    $reset_token_expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

    // Store verification code and expiry time in the database
    $query = "UPDATE user SET reset_token = '$verification_code', reset_token_expiry = '$reset_token_expiry' WHERE email = '$email'";
    if (mysqli_query($db, $query)) {
        // Send verification code to email
        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'luthfi2264a@gmail.com';
            $mail->Password = 'jwfxgozhpmliwrer';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            //Recipients
            $mail->setFrom('no-reply@gmail.com', 'Efi-songket Reset Password'); // Replace with your email and name
            $mail->addAddress($email);

            //Content
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body = "Your verification code is: <strong>$verification_code</strong><br><br>Please click the following link to reset your password: <a href='http://localhost/WEBEFISONGKET/index.php?p=reset_password'>Reset Password</a>";

            $mail->send();
            // Notify the user and redirect to reset_password.php with the email as a query parameter
            echo "<script>setTimeout(function(){ window.location.href = 'index.php?p=reset_password&email=$email'; }, 3000);</script>";
            echo "<script>alert('Kode verifikasi telah dikirim ke email Anda.');</script>";
            exit();
        } catch (Exception $e) {
            $message = "<div class='alert alert-danger'>Message could not be sent. Mailer Error: {$mail->ErrorInfo}</div>";
        }
    } else {
        $message = "<div class='alert alert-danger'>Failed to store verification code in the database.</div>";
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
                        <!-- Kolom Form -->
                        <div class="col-md-6">
                            <div class="card-body p-5">
                                <h2 class="text-center fw-bold">Lupa Password</h2>
                                <h2 class="text-center mb-4 fw-bold">Efi Songket</h2>
                                <!-- Pesan -->
                                <?php if ($message) echo $message; ?>
                                <form method="POST" action="">
                                    <!-- Email -->
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Alamat Email</label>
                                        <input type="email" id="email" name="email" class="form-control" placeholder="Masukkan email Anda" required>
                                    </div>
                                    <!-- Tombol -->
                                    <div class="d-grid">
                                        <button type="submit" name="submit" class="btn btn-primary rounded-pill">Kirim Kode Verifikasi</button>
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