<?php
include 'phpconection.php'; // Pastikan koneksi database sudah ada

// Ambil data kategori untuk dropdown
$categories = mysqli_query($db, "SELECT * FROM kategori");

// Filter produk berdasarkan kategori (gunakan parameter terfilter)
$category_id = isset($_GET['category_id']) && is_numeric($_GET['category_id']) ? $_GET['category_id'] : null;

$query = "SELECT p.*, k.nama_kategori FROM products p 
          LEFT JOIN kategori k ON p.category_id = k.id_kategori";

if ($category_id) {
    $query .= " WHERE p.category_id = ?";
}

$stmt = $db->prepare($query);

if ($category_id) {
    $stmt->bind_param('i', $category_id);
}

$stmt->execute();
$result = $stmt->get_result();

// Periksa apakah query berhasil
if (!$result) {
    die("Query gagal: " . $db->error);
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Efi Songket</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
</head>

<body>
    <?php
    // Menampilkan notifikasi jika ada status tertentu
    if (isset($_GET['status'])) {
        $status = $_GET['status'];
        $message = $_GET['message'];
        if ($status == 'success') {
            echo '<div class="alert alert-success" role="alert">' . htmlspecialchars($message) . '</div>';
        } else {
            echo '<div class="alert alert-danger" role="alert">' . htmlspecialchars($message) . '</div>';
        }
    }
    ?>

    <div class="container mt-5">
        <h2 class="text-warning">Manage Products</h2>
        
        <!-- Button to trigger modal for adding product -->
        <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addProductModal">Add New Product</button>

             <!-- Dropdown Filter Berdasarkan Kategori -->
        <!-- Dropdown Filter Berdasarkan Kategori -->
    <form method="GET" action="index.php" class="mb-3">
        <!-- Input hidden untuk parameter halaman -->
        <input type="hidden" name="p" value="manage_product">
        
        <!-- Dropdown untuk kategori -->
        <select name="category_id" class="form-select" onchange="this.form.submit()">
            <option value="">Semua Kategori</option>
            <?php while ($category = mysqli_fetch_assoc($categories)): ?>
                <option value="<?= $category['id_kategori'] ?>" 
                    <?= isset($_GET['category_id']) && $_GET['category_id'] == $category['id_kategori'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($category['nama_kategori']) ?>
                </option>
            <?php endwhile; ?>
        </select>
    </form>

        <!-- Tabel Produk -->
        <table class="table table-bordered" id="productTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Stock</th>
                    <th>Gambar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($product = mysqli_fetch_assoc($result)): ?>
                    <tr id="product-<?= $product['id'] ?>">
                        <td><?= $product['id'] ?></td>
                        <td><?= htmlspecialchars($product['name']) ?></td>
                        <td><?= htmlspecialchars($product['description']) ?></td>
                        <td><?= htmlspecialchars($product['nama_kategori']) ?></td>
                        <td>Rp <?= number_format($product['price'], 0, ',', '.') ?></td>
                        <td><?= $product['stock'] ?></td>
                        <td>
                            <img src="uploads/<?= !empty($product['image']) ? $product['image'] : 'default.png' ?>" alt="Product Image" style="width: 100px; height: 100px; object-fit: cover;">
                        </td>
                        <td>
                            <!-- Edit Button -->
                            <button class="btn btn-warning btn-sm editProductBtn" 
                                    data-id="<?= $product['id'] ?>" 
                                    data-name="<?= htmlspecialchars($product['name']) ?>" 
                                    data-description="<?= htmlspecialchars($product['description']) ?>" 
                                    data-price="<?= $product['price'] ?>" 
                                    data-stock="<?= $product['stock'] ?>"
                                    data-category-id="<?= $product['category_id'] ?>"
                                    data-image="<?= $product['image'] ?>">Edit</button>
                        
                            <!-- Delete Button -->
                            <form action="crud_product.php?proses=delete" method="POST" style="display:inline;">
                                <input type="hidden" name="id" value="<?= $product['id'] ?>">
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this product?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal Add Product -->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProductModalLabel">Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addProductForm" enctype="multipart/form-data" action="crud_product.php?proses=insert" method="POST">
                        <div class="mb-3">
                            <label for="productName" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="productName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="productDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="productDescription" name="description" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="category_id" class="form-label">Kategori</label>
                            <select class="form-select" id="category_id" name="category_id" required>
                                <?php
                                $categories = mysqli_query($db, "SELECT * FROM kategori");
                                while ($category = mysqli_fetch_assoc($categories)) {
                                    echo "<option value='{$category['id_kategori']}'>{$category['nama_kategori']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="productPrice" class="form-label">Price</label>
                            <input type="number" class="form-control" id="productPrice" name="price" required>
                        </div>
                        <div class="mb-3">
                            <label for="productStock" class="form-label">Stock</label>
                            <input type="number" class="form-control" id="productStock" name="stock" required>
                        </div>
                        <div class="mb-3">
                            <label for="editProductImage" class="form-label">Product Image</label>
                            <input type="file" class="form-control" id="editProductImage" name="fileToUpload">
                            <img id="editProductImagePreview" style="width: 100px; height: 100px; object-fit: cover; display: none;">
                            <input type="hidden" id="currentImage" name="currentImage"> <!-- Menyimpan gambar saat ini -->
                        </div>

                        <button type="submit" class="btn btn-primary">Add Product</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Product -->
<div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProductModalLabel">Edit Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editProductForm" enctype="multipart/form-data" action="crud_product.php?proses=update" method="POST">
                    <input type="hidden" id="editProductId" name="id">
                    
                    <div class="mb-3">
                        <label for="editProductName" class="form-label">Product Name</label>
                        <input type="text" class="form-control" id="editProductName" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="editProductDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="editProductDescription" name="description" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="editProductPrice" class="form-label">Price</label>
                        <input type="number" class="form-control" id="editProductPrice" name="price" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="editProductStock" class="form-label">Stock</label>
                        <input type="number" class="form-control" id="editProductStock" name="stock" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="editCategory" class="form-label">Kategori</label>
                        <select class="form-select" id="editCategory" name="category_id" required>
                            <?php
                            $categories = mysqli_query($db, "SELECT * FROM kategori");
                            while ($category = mysqli_fetch_assoc($categories)) {
                                echo "<option value='{$category['id_kategori']}'>{$category['nama_kategori']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="editProductImage" class="form-label">Product Image</label>
                        <input type="file" class="form-control" id="editProductImage" name="fileToUpload">
                        <img id="editProductImagePreview" style="width: 100px; height: 100px; object-fit: cover; display: none;" />
                        <input type="hidden" id="currentImage" name="currentImage"> <!-- Hidden input to hold current image name -->
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Update Product</button>
                </form>
            </div>
        </div>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script>
       // Handle Edit Product Button
       $('.editProductBtn').click(function() {
    var id = $(this).data('id');
    var name = $(this).data('name');
    var description = $(this).data('description');
    var price = $(this).data('price');
    var stock = $(this).data('stock');
    var categoryId = $(this).data('category-id');
    var image = $(this).data('image');

    // Set values in the edit modal
    $('#editProductId').val(id);
    $('#editProductName').val(name);
    $('#editProductDescription').val(description);
    $('#editProductPrice').val(price);
    $('#editProductStock').val(stock);
    $('#editCategory').val(categoryId);
    $('#currentImage').val(image); // Set the current image name
    if (image) {
        $('#editProductImagePreview').attr('src', 'uploads/' + image).show();
    } else {
        $('#editProductImagePreview').hide();
    }

    $('#editProductModal').modal('show');
});
    </script>
</body>

</html>
