<?php
include 'phpconection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category'])) {
    $category = intval($_POST['category']); // Ensure category is an integer

    // Validate if category exists
    $categoryCheckQuery = "SELECT COUNT(*) as count FROM kategori WHERE id_kategori = ?";
    $stmtCheck = $db->prepare($categoryCheckQuery);
    $stmtCheck->bind_param('i', $category);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();
    $categoryExists = $resultCheck->fetch_assoc()['count'] > 0;

    if (!$categoryExists) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid category selected.'
        ]);
        exit;
    }

    // Fetch products from the selected category
    $query = "SELECT id, name, description, price, image FROM products WHERE category_id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param('i', $category);
    $stmt->execute();
    $result = $stmt->get_result();

    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }

    echo json_encode([
        'status' => 'success',
        'products' => $products
    ]);
    exit;
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request.'
    ]);
    exit;
}
?>