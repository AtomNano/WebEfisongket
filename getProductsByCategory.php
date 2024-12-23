<?php
include 'phpconection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category'])) {
    $category = $_POST['category'];

    $query = "SELECT * FROM products WHERE category_id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param('i', $category);
    $stmt->execute();
    $result = $stmt->get_result();

    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }

    echo json_encode($products);
    exit;
} else {
    echo json_encode(['error' => 'Invalid request']);
    exit;
}
?>