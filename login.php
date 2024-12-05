<?php
include 'phpconection.php';

$title = 'Efi Songket - Koleksi Terbaru';

$error = '';

// Periksa apakah form telah disubmit
if (isset($_POST['submit'])) {
    // Ambil email dan password dari form
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $password = $_POST['password']; // Ambil password tanpa hashing di sini

    // Lakukan query ke database
    $query = "SELECT * FROM user WHERE email='$email'";
    $login = mysqli_query($db, $query);

    // Cek hasil login
    if ($login && mysqli_num_rows($login) > 0) {
        $data_login = mysqli_fetch_assoc($login);

        // Verifikasi password dengan hash
        if (password_verify($password, $data_login['password'])) {
            // Buat session dan redirect ke halaman berdasarkan level user
            $_SESSION['user'] = $data_login['email'];
            $_SESSION['user_id'] = $data_login['id'];
            $_SESSION['level'] = $data_login['level'];
            
            if ($data_login['level'] == 'admin') {
                header('Location: admin/index.php');
            } else {
                header('Location: index.php'); // Arahkan ke index.php untuk user biasa
            }
            exit; // Pastikan untuk mengakhiri skrip setelah redirection
        } else {
            echo "<script>alert('Email or password invalid!')</script>";
        }
    } else {
        echo "<script>alert('Email or password invalid!')</script>";
    }
}

?>

<section class="vh-100 bg-light">
    <div class="container py-5 h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col col-xl-10">
                <div class="card shadow-lg p-3 mb-5" style="border-radius: 1rem;">
                    <div class="row g-0">
                        <div class="col-md-6 col-lg-5 d-none d-md-block">
                            <img class="img-fluid"
                                src="gambarEfi/Songket Balapak Maroon Silver Selendang Kombinasi Suji Cair (3.500.000).jpg"
                                alt="login form" class="img-fluid" style="border-radius: 1rem 0 0 1rem;" />
                        </div>
                        <div class="col-md-6 col-lg-7 d-flex align-items-center">
                            <div class="card-body p-4 p-lg-5 text-black">
                                <form method="POST" action="">
                                    <div class="d-flex align-items-center mb-3 pb-1">
                                        <i class="fas fa-cubes fa-2x me-3" style="color: #ff6219;"></i>
                                        <span class="h1 fw-bold mb-0">Efi-Songket</span>
                                    </div>
                                    <h5 class="fw-normal mb-3 pb-3" style="letter-spacing: 1px;">Sign into your account
                                    </h5>

                                    <!-- Input Email -->
                                    <div data-mdb-input-init class="form-outline mb-4">
                                        <input type="email" id="form2Example17" name="email"
                                            class="form-control form-control-lg" required />
                                        <label class="form-label" for="form2Example17">Email address</label>
                                    </div>

                                    <!-- Input Password -->
                                    <div data-mdb-input-init class="form-outline mb-4">
                                        <input type="password" id="form2Example27" name="password"
                                            class="form-control form-control-lg" required />
                                        <label class="form-label" for="form2Example27">Password</label>
                                    </div>

                                    <!-- Tombol Login -->
                                    <div class="pt-1 mb-4">
                                        <button name="submit" type="submit" class="btn btn-dark btn-lg btn-block">Login</button>
                                    </div>

                                    <a class="small text-muted" href="#!">Forgot password?</a>
                                    <p class="mb-5 pb-lg-2" style="color: #393f81;">Don't have an account? <a
                                            href="index.php?p=register" style="color: #393f81;">Register here</a></p>
                                    <a href="#!" class="small text-muted">Terms of use.</a>
                                    <a href="#!" class="small text-muted">Privacy policy</a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>