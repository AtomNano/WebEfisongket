<?php
include 'phpconection.php';

$proses = $_GET['proses'] ?? '';

// Insert Product
if ($proses == 'insert') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $category_id = $_POST['category_id']; // Tambahkan kategori
    $image = '';

    if ($_FILES['fileToUpload']['name']) {
        // Upload image with validation
        $target_dir = "uploads/";
        $target_file = basename($_FILES["fileToUpload"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        // Check if the file is an image and validate file type
        if (in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif', 'heic'])) {
            move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_dir . $target_file);
            $image = $target_file; // Save the file name
        } else {
            echo "<script>alert('Invalid file type. Only JPG, JPEG, PNG, GIF, HEIC files are allowed.');</script>";
            exit;
        }
    }

    $sql = "INSERT INTO products (name, description, price, stock, image, category_id) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('ssdssi', $name, $description, $price, $stock, $image, $category_id);

    if ($stmt->execute()) {
        header('Location: index.php?p=manage_product&status=success&message=Product added successfully');
    } else {
        header('Location: index.php?p=manage_product&status=danger&message=Error adding product');
    }
    exit;
}

// Update Product
if ($proses == 'update') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $category_id = $_POST['category_id'];
    $currentImage = $_POST['currentImage']; // Default to the current image

    $image = $currentImage; // Use the current image by default

    if ($_FILES['fileToUpload']['name']) {
        // Upload new image with validation
        $target_dir = "uploads/";
        $target_file = basename($_FILES["fileToUpload"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if the file is an image and validate file type
        if (in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif', 'heic'])) {
            move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_dir . $target_file);
            $image = $target_file; // Use the new file name
        } else {
            echo "<script>alert('Invalid file type. Only JPG, JPEG, PNG, GIF, HEIC files are allowed.');</script>";
            exit;
        }
    }

    $sql = "UPDATE products SET name = ?, description = ?, price = ?, stock = ?, category_id = ?, image = ? WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('ssdissi', $name, $description, $price, $stock, $category_id, $image, $id);

    if ($stmt->execute()) {
        header('Location: index.php?p=manage_product&status=success&message=Product updated successfully');
    } else {
        header('Location: index.php?p=manage_product&status=danger&message=Error updating product');
    }
    exit;
}


// Delete Product
if ($proses == 'delete') {
    $id = $_POST['id'];
    $sql = "DELETE FROM products WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
        header('Location: index.php?p=manage_product&status=success&message=Product deleted successfully');
    } else {
        header('Location: index.php?p=manage_product&status=danger&message=Error deleting product');
    }
    exit;
}
?>
