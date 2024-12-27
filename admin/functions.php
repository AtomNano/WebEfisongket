<?php
// functions.php

if (!function_exists('getMonthlyTransactions')) {
    function getMonthlyTransactions($month, $year, $db) {
        $startDate = "$year-$month-01";
        $endDate = date("Y-m-t", strtotime($startDate));

        $query = "
            SELECT 
                t.status AS status,
                COUNT(t.id) AS total_transactions,
                SUM(t.total_price) AS total_revenue,
                SUM(oi.quantity) AS total_products_sold
            FROM transactions t
            JOIN order_item oi ON t.id = oi.transaction_id
            WHERE t.created_at BETWEEN '$startDate' AND '$endDate'
            GROUP BY t.status
        ";

        $result = mysqli_query($db, $query);

        if (!$result) {
            die("Query gagal: " . mysqli_error($db));
        }

        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }

        return $data;
    }
}

if (!function_exists('getTopSellingProducts')) {
    function getTopSellingProducts($db) {
        $query = "
            SELECT p.name AS product_name, SUM(oi.quantity) AS total_sold
            FROM order_item oi
            JOIN products p ON oi.product_id = p.id
            GROUP BY oi.product_id
            ORDER BY total_sold DESC
            LIMIT 10
        ";  

        $result = mysqli_query($db, $query);

        if (!$result) {
            die("Query gagal: " . mysqli_error($db));
        }

        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }

        return $data;
    }
}

if (!function_exists('getTransactionReport')) {
    function getTransactionReport($db, $startDate, $endDate) {
        $query = "
            SELECT 
                p.id AS product_id,
                p.name AS product_name,
                SUM(oi.quantity) AS total_quantity,
                SUM(oi.quantity * oi.price) AS total_revenue
            FROM order_item oi
            INNER JOIN products p ON oi.product_id = p.id
            INNER JOIN transactions t ON oi.transaction_id = t.id
            WHERE t.created_at BETWEEN '$startDate' AND '$endDate'
            GROUP BY p.id, p.name
            ORDER BY total_revenue DESC
        ";

        $result = mysqli_query($db, $query);

        if (!$result) {
            die("Query gagal: " . mysqli_error($db));
        }

        // Mengubah hasil query menjadi array biasa
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row; // Menambahkan setiap baris data ke array
        }

        // Menggunakan usort untuk mengurutkan data berdasarkan total_revenue
        usort($data, function($a, $b) {
            return $b['total_revenue'] - $a['total_revenue']; // Mengurutkan berdasarkan pendapatan
        });

        return $data; // Mengembalikan array yang sudah diurutkan
    }
}

if (!function_exists('getMonthlySalesDataByDateRange')) {
    function getMonthlySalesDataByDateRange($startDate, $endDate, $db) {
        $query = "
            SELECT 
                DATE_FORMAT(t.created_at, '%Y-%m') AS month,
                SUM(oi.quantity) AS total_quantity,
                SUM(oi.quantity * oi.price) AS total_revenue
            FROM transactions t
            JOIN order_item oi ON t.id = oi.transaction_id
            WHERE t.created_at BETWEEN '$startDate' AND '$endDate'
            GROUP BY DATE_FORMAT(t.created_at, '%Y-%m')
            ORDER BY DATE_FORMAT(t.created_at, '%Y-%m')
        ";

        $result = mysqli_query($db, $query);

        if (!$result) {
            die("Query gagal: " . mysqli_error($db));
        }

        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }

        return $data;
    }
}

if (!function_exists('getConfirmationStatus')) {
    function getConfirmationStatus($db, $startDate, $endDate) {
        $query = "
            SELECT 
                status,
                COUNT(*) AS total_transactions,
                SUM(total_price) AS total_revenue,
                SUM(oi.quantity) AS total_products_sold
            FROM transactions t
            JOIN order_item oi ON t.id = oi.transaction_id
            WHERE t.created_at BETWEEN '$startDate' AND '$endDate'
            GROUP BY status
        ";
        $result = mysqli_query($db, $query);

        if (!$result) {
            die("Query gagal: " . mysqli_error($db));
        }

        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }

        return $data;
    }
}

if (!function_exists('getMonthlyTransactionsByDateRange')) {
    function getMonthlyTransactionsByDateRange($startDate, $endDate, $db) {
        $query = "
            SELECT 
                t.status AS status,
                COUNT(t.id) AS total_transactions,
                SUM(t.total_price) AS total_revenue,
                SUM(oi.quantity) AS total_products_sold
            FROM transactions t
            JOIN order_item oi ON t.id = oi.transaction_id
            WHERE t.created_at BETWEEN '$startDate' AND '$endDate'
            GROUP BY t.status
        ";

        $result = mysqli_query($db, $query);

        if (!$result) {
            die("Query gagal: " . mysqli_error($db));
        }

        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }

        return $data;
    }
}
?>