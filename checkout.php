<?php

include 'phpconection.php'; // Koneksi ke database jika perlu

// Ambil email dari session
$email = $_SESSION['email'] ?? null;
if (!$email) {
    die('Pengguna tidak terautentikasi. Silakan login terlebih dahulu.');
}

// Ambil data pengguna berdasarkan email
$user_query = "SELECT id, email FROM user WHERE email = ?";
$user_stmt = $db->prepare($user_query);
$user_stmt->bind_param('s', $email);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_assoc();
if (!$user) {
    die('Pengguna tidak ditemukan.');
}
$user_id = $user['id'];

// Ambil data produk dalam keranjang berdasarkan user_id
$cart_query = "SELECT c.*, p.name AS product_name, p.price, p.image AS product_image FROM cart c
                JOIN products p ON c.product_id = p.id WHERE c.user_id = ?";
$cart_stmt = $db->prepare($cart_query);
$cart_stmt->bind_param('i', $user_id);
$cart_stmt->execute();
$cart_result = $cart_stmt->get_result();
$cart_items = $cart_result->fetch_all(MYSQLI_ASSOC);
$total_price = 0;

// Menghitung total harga keranjang
foreach ($cart_items as $item) {
    $total_price += $item['price'] * $item['quantity'];
}

// Simpan total harga di sesi
$_SESSION['total_price'] = $total_price;
?>

<style>

body{
    background-image: url('gambarEfi/bg1.png');
    backdrop-filter: blur(5px);
}

    .checkout-container {
        display: flex;
        justify-content: space-between;
        gap: 20px;
    }
    .checkout-form, .cart-summary {
        flex: 1;
        border: 1px solid #ddd;
        padding: 20px;
        margin-bottom: 50px;
        border-radius: 8px;
        background-color: #fff; /* Set background to white */
    }
    .transaction-history {
        margin-top: 30px;
    }
    .transaction-table {
        width: 100%;
        border-collapse: collapse;
    }
    .transaction-table th, .transaction-table td {
        border: 1px solid #ddd;
        padding: 8px;
    }
    .product-image {
        width: 70px;
        height: 70px;
        object-fit: cover;
        border-radius: 5px;
    }
</style>

<div class="container mt-1">
    <h2 class="text-center text-white">Checkout</h2>
    <div class="checkout-container">
        <!-- Formulir Checkout -->
        <div class="checkout-form">
            <h2>Formulir Checkout</h2>
            <form id="checkoutForm" action="process_checkout.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                </div>
                <div class="mb-3">
                    <label for="name" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Alamat Pengiriman</label>
                    <textarea id="address" name="address" class="form-control" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Nomor Telepon</label>
                    <input type="text" id="phone" name="phone" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="bank-account" class="form-label">Nomor Rekening Bank Pemilik</label>
                    <label for="bank-account" class="form-label fw-bold">
                        <img src="gambarEfi/bri.png" alt="BRI" style="width: 30px; height: 30px; margin-right: 10px;">
                        BRI 2312312312312312312
                    </label>
                </div>
                <div class="mb-3">
                    <label for="payment-proof" class="form-label">Upload Bukti Pembayaran</label>
                    <input type="file" id="payment-proof" name="payment-proof" class="form-control" required>
                    <img id="payment-proof-preview" src="#" alt="Preview Bukti Pembayaran" style="display: none; margin-top: 10px; max-width: 300px; max-height: 300px;">
                </div>
                <button type="submit" class="btn btn-primary w-100 rounded-pill">Checkout</button>
            </form>
        </div>

        <!-- Ringkasan Pesanan -->
        <div class="cart-summary">
            <div class="mt-4">
                <h3>Tata Cara Transfer Uang ke Rekening BRI</h3>
                <ol>
                    <li>Masukkan kartu ATM BRI Anda ke mesin ATM.</li>
                    <li>Masukkan PIN ATM Anda.</li>
                    <li>Pilih menu "Transfer".</li>
                    <li>Pilih "Transfer ke Rekening BRI".</li>
                    <li>Masukkan nomor rekening tujuan: <strong>2312312312312312312</strong>.</li>
                    <li>Masukkan jumlah uang yang akan ditransfer.</li>
                    <li>Ikuti instruksi selanjutnya untuk menyelesaikan transaksi.</li>
                    <li>Simpan bukti transfer untuk diunggah pada form di atas.</li>
                </ol>
            </div>
            <h2>Ringkasan Pesanan</h2>
            <?php if (!empty($cart_items)): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Gambar</th>
                            <th>Nama Produk</th>
                            <th>Jumlah</th>
                            <th>Harga</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart_items as $item): ?>
                            <tr>
                                <td>
                                    <?php $image_path = './admin/uploads/' . $item['product_image']; ?>
                                    <?php if (!empty($item['product_image']) && file_exists($image_path)): ?>
                                        <img src="<?php echo htmlspecialchars($image_path); ?>" alt="Product Image" class="product-image">
                                    <?php else: ?>
                                        <span>Tidak ada gambar</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td>Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Total:</strong></td>
                            <td><strong>Rp <?php echo number_format($total_price, 0, ',', '.'); ?></strong></td>
                        </tr>
                    </tfoot>
                </table>
            <?php else: ?>
                <p>Keranjang Anda kosong.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>

document.getElementById('payment-proof').addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('payment-proof-preview');
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
});

    // Menampilkan preview gambar setelah upload
    document.getElementById('payment-proof').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const imgPreview = document.getElementById('img-preview');
                const imgContainer = document.getElementById('img-preview-container');
                imgPreview.src = e.target.result;
                imgContainer.style.display = 'block'; // Menampilkan preview gambar

                // Menampilkan tanggal saat bukti pembayaran diunggah
                const currentDate = new Date();
                const dateString = currentDate.toISOString().split('T')[0]; // Mengambil tanggal saat ini (YYYY-MM-DD)
                document.getElementById('payment-date').value = dateString;
            };
            reader.readAsDataURL(file);
        }
    });

    // Menangani form checkout
    document.getElementById('checkoutForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Mencegah form dikirim secara default

        const formData = new FormData(this);

        fetch('process_checkout.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.href = 'index.php?p=detail_transaksi&id=' + data.transaction_id;
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: data.message,
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Terjadi kesalahan saat memproses checkout.',
                showConfirmButton: false,
                timer: 1500
            });
        });
    });
</script>