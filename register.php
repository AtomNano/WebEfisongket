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
<section class="vh-100 bg-light">
    <div class="container py-5 h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col col-xl-10">
                <div class="card shadow-lg p-3 mb-5" style="border-radius: 1rem;">
                    <div class="row g-0">
                        <div class="col-md-6 col-lg-5 d-none d-md-block">
                            <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-login-form/img1.webp"
                                alt="Register form" class="img-fluid" style="border-radius: 1rem 0 0 1rem;" />
                        </div>
                        <div class="col-md-6 col-lg-7 d-flex align-items-center">
                            <div class="card-body p-4 p-lg-5 text-black">
                                <form method="POST" action="">
                                    <div class="d-flex align-items-center mb-3 pb-1">
                                        <i class="fas fa-user-plus fa-2x me-3" style="color: #ff6219;"></i>
                                        <span class="h1 fw-bold mb-0">Register</span>
                                    </div>

                                    <h5 class="fw-normal mb-3 pb-3" style="letter-spacing: 1px;">Create your account
                                    </h5>

                                    <!-- Tampilkan error jika ada -->
                                    <?php if (!empty($error)): ?>
                                    <div class="alert alert-danger" role="alert">
                                        <?= htmlspecialchars($error); ?>
                                    </div>
                                    <?php endif; ?>

                                    <!-- Input Email -->
                                    <div class="form-outline mb-4">
                                        <input type="email" id="form2Example17" name="email"
                                            class="form-control form-control-lg" required />
                                        <label class="form-label" for="form2Example17">Email address</label>
                                    </div>

                                    <div class="form-outline mb-4">
                                        <input type="password" id="form2Example27" name="password"
                                            class="form-control form-control-lg" minlength="8" required />
                                        <label class="form-label" for="form2Example27">Password</label>
                                        <div class="invalid-feedback">
                                            Password harus memiliki minimal 8 karakter.
                                        </div>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="pt-1 mb-4">
                                        <button name="submit" type="submit"
                                            class="btn btn-dark btn-lg btn-block">Register</button>
                                    </div>

                                    <p class="mb-5 pb-lg-2" style="color: #393f81;">Already have an account? <a
                                            href="index.php?p=login" style="color: #393f81;">Login here</a></p>

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

<script>
document.getElementById('form2Example27').addEventListener('input', function() {
    if (this.value.length < 8) {
        this.setCustomValidity('Password harus memiliki minimal 8 karakter.');
    } else {
        this.setCustomValidity('');
    }
});
</script>