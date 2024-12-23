<?php
// functions.php

// // functions.php

// if (!function_exists('getMonthlyTransactions')) {
//     function getMonthlyTransactions($month, $year, $db) {
//         $startDate = "$year-$month-01";
//         $endDate = date("Y-m-t", strtotime($startDate));

//         $query = "
//             SELECT 
//                 COUNT(t.id) AS total_transactions,
//                 SUM(t.total_price) AS total_revenue
//             FROM transactions t
//             WHERE t.created_at BETWEEN '$startDate' AND '$endDate'
//         ";

//         return mysqli_query($db, $query); // Return query result
//     }
// }

// // Ambil laporan transaksi bulan ini (misalnya, Desember 2024)
// $month = date('m'); // Bulan saat ini
// $year = date('Y');  // Tahun saat ini

// // Panggil fungsi untuk mendapatkan laporan transaksi
// $result = getMonthlyTransactions($month, $year, $db);

// // Ambil total pendapatan dari hasil query
// $monthly_revenue = ($result && mysqli_num_rows($result) > 0) ? mysqli_fetch_assoc($result)['total_revenue'] : 0;




?>


