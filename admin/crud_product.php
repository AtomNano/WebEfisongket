<?php
include 'phpconection.php';

$proses = $_GET['proses'] ?? '';

// Insert Product
if ($proses == 'insert') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $image = '';

    if ($_FILES['fileToUpload']['name']) {
        // Upload image
        $target_dir = "uploads/";
        $target_file = basename($_FILES["fileToUpload"]["name"]); // Simpan hanya nama file
        move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_dir . $target_file);
        $image = $target_file; // Simpan nama file
    }
    

    $sql = "INSERT INTO products (name, description, price, stock, image) 
            VALUES ('$name', '$description', '$price', '$stock', '$image')";
    if (mysqli_query($db, $sql)) {
        // Redirect ke index.php dengan pesan sukses
        header('Location: index.php?p=manage_product&status=success&message=Product added successfully');
    } else {
        // Redirect ke index.php dengan pesan error
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
    $image = '';

    if ($_FILES['fileToUpload']['name']) {
        // Upload image
        $target_dir = "uploads/";
        $target_file = basename($_FILES["fileToUpload"]["name"]); // Store only the file name
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_dir . $target_file)) {
            $image = $target_file; // Save the new file name
        } else {
            // Handle upload error if needed
            $image = $_POST['currentImage']; // Keep the current image if upload fails
        }
    } else {
        // Keep current image if no new image is uploaded
        $image = $_POST['currentImage']; // Ensure this is sent in the form
    }

    // Prepare the SQL statement
    $sql = "UPDATE products SET name = '$name', description = '$description', price = '$price', stock = '$stock', image = '$image' WHERE id = '$id'";
    if (mysqli_query($db, $sql)) {
        // Redirect to index.php with success message
        header('Location: index.php?p=manage_product&status=success&message=Product updated successfully');
    } else {
        // Redirect to index.php with error message
        header('Location: index.php?p=manage_product&status=danger&message=Error updating product');
    }
    exit;
}
