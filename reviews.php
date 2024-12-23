<?php
include 'phpconection.php';

// Cek metode HTTP
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Menangani pengiriman ulasan (Submit Review)
    $product_id = $_POST['product_id'];
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $rating = $_POST['rating'];
    $comment = mysqli_real_escape_string($db, $_POST['comment']);

    // Query untuk menyimpan ulasan ke dalam database
    $query = "INSERT INTO reviews (product_id, username, rating, comment, created_at) 
              VALUES ('$product_id', '$username', '$rating', '$comment', NOW())";
    if (mysqli_query($db, $query)) {
        echo json_encode(['status' => 'success', 'message' => 'Review submitted successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error submitting review']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Menangani pengambilan ulasan (Get Reviews)
    $product_id = isset($_GET['product_id']) ? $_GET['product_id'] : 0;

    if ($product_id > 0) {
        // Query untuk mengambil ulasan dari database
        $query = "SELECT * FROM reviews WHERE product_id = $product_id ORDER BY created_at DESC";
        $result = mysqli_query($db, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $reviews = [];
            while ($review = mysqli_fetch_assoc($result)) {
                $reviews[] = $review;
            }
            echo json_encode($reviews); // Kembalikan ulasan dalam format JSON
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No reviews found']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid product ID']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
