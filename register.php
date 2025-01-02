<?php
include 'phpconection.php';

if (isset($_POST['submit'])) {
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash password
    $level = 'user'; // Default level

    // Periksa apakah email sudah terdaftar
    $checkEmailQuery = "SELECT * FROM user WHERE email='$email'";
    $checkEmailResult = mysqli_query($db, $checkEmailQuery);

    if (mysqli_num_rows($checkEmailResult) > 0) {
        $error = "Email is already registered!";
    } else {
        // Simpan data ke database
        $query = "INSERT INTO user (email, password, level) VALUES ('$email', '$password', '$level')";
        if (mysqli_query($db, $query)) {
            echo "<script>alert('Registration successful!'); window.location.href ='index.php?p=login';</script>";
        } else {
            $error = "Registration failed, please try again.";
        }
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
                            <img src="https://i.pinimg.com/736x/fd/27/1d/fd271d3c6cc30147f896acc8ec68f1eb.jpg"
                                 alt="Register form"
                                 class="img-fluid h-100"
                                 style="object-fit: cover;">
                        </div>
                        <!-- Kolom Form Register -->
                        <div class="col-md-6">
                            <div class="card-body p-5">
                                <h2 class="text-center fw-bold">Daftar</h2>
                                <h2 class="text-center mb-4 fw-bold">Efi Songket</h2>
                                <form method="POST" action="">
                                    <!-- Tampilkan error jika ada -->
                                    <?php if (!empty($error)): ?>
                                        <div class="alert alert-danger"><?php echo $error; ?></div>
                                    <?php endif; ?>
                                    <!-- Input Email -->
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Alamat Email</label>
                                        <input type="email" id="email" name="email" class="form-control" placeholder="Masukkan email Anda" required>
                                    </div>
                                    <!-- Input Password -->
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Kata Sandi</label>
                                        <input type="password" id="password" name="password" class="form-control" placeholder="Masukkan kata sandi Anda" required>
                                    </div>
                                    <!-- Tombol Register -->
                                    <div class="d-grid">
                                        <button type="submit" name="submit" class="btn btn-primary rounded-pill">Daftar</button>
                                    </div>
                                </form>
                                <div class="text-center mt-3">
                                    <span>Sudah punya akun? <a href="index.php?p=login" class="text-primary">Masuk di sini</a></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>