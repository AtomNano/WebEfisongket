-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 20 Des 2024 pada 20.12
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `efisongket`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `quantity`, `created_at`, `updated_at`) VALUES
(236, 2, 66, 2, '2024-12-20 19:00:26', '2024-12-20 19:07:24');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `nama_kategori`) VALUES
(1, 'songket'),
(2, 'bordir'),
(3, 'batik');

-- --------------------------------------------------------

--
-- Struktur dari tabel `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `order_item`
--

CREATE TABLE `order_item` (
  `id` int(11) NOT NULL,
  `transaction_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `order_item`
--

INSERT INTO `order_item` (`id`, `transaction_id`, `product_id`, `quantity`, `price`) VALUES
(52, 95, 60, 2, 34234234.00),
(53, 96, 60, 1, 34234234.00),
(54, 96, 61, 1, 1231231.00),
(55, 97, 64, 1, 2312333.00),
(56, 98, 66, 1, 432123.00),
(57, 98, 65, 1, 231112.00),
(58, 99, 66, 3, 432123.00);

-- --------------------------------------------------------

--
-- Struktur dari tabel `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `category_id`, `price`, `stock`, `image`, `created_at`) VALUES
(64, 'Songket Kombinasi Selendang Suji Cair Katun Benang Dua ', 'adadasdawdawdawdawdwa', 1, 2312333.00, 3, 'a1.png', '2024-12-20 18:22:18'),
(65, 'Songket Balapak Biru Tosca Motif Kombinasi Suji Cair', 'asdasdasdawdawd', 1, 231112.00, 2, '11.png', '2024-12-20 18:22:36'),
(66, 'Songket Balapak Kombinasi Suji Cair Pink Baby Silver', 'addawdasdawd', 1, 432123.00, 3, '23.png', '2024-12-20 18:25:09'),
(68, 'Selendang Bordir Maroon ', 'adadaswdawda', 2, 324432.00, 4, '12.png', '2024-12-20 18:26:34'),
(69, 'bahan batik cap', 'adsdasdawda', 3, 342432.00, 3, '1.png', '2024-12-20 18:28:46'),
(70, 'satu set sarung selendang ', 'dasdasdasdasda', 3, 3424234.00, 4, '6.png', '2024-12-20 18:29:14'),
(71, 'Satu set sarung selendang batik tanah liek ', 'adawdawdaw', 3, 3423422.00, 4, '3.png', '2024-12-20 18:32:47');

-- --------------------------------------------------------

--
-- Struktur dari tabel `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `phone` varchar(20) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `payment_proof` varchar(255) NOT NULL,
  `status` varchar(50) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `transactions`
--

INSERT INTO `transactions` (`id`, `email`, `name`, `address`, `phone`, `total_price`, `payment_proof`, `status`, `created_at`) VALUES
(95, 'luthfi2264a@gmail.com', 'Songket Kombinasi Selendang Suji Cair Katun Benang Dua ', 'Jl. Jayapura f. 19 ulakkarang selatan asratek', '085805786626', 68468468.00, 'admin/payment_proofs/WhatsApp Image 2024-12-17 at 08.12.07_e21b364d.jpg', 'Confirmed', '2024-12-20 22:50:48'),
(96, 'luthfi2264a@gmail.com', 'Songket Kombinasi Selendang Suji Cair Katun Benang Dua ', 'Jl. Jayapura f. 19 ulakkarang selatan asratek', '085805786626', 35465465.00, 'admin/payment_proofs/Desain tanpa judul (3).png', 'Confirmed', '2024-12-21 00:31:04'),
(97, 'luthfi2264a@gmail.com', 'Songket Kombinasi Selendang Suji Cair Katun Benang Dua ', 'Jl. Jayapura f. 19 ulakkarang selatan asratek', '085805786626', 36546567.00, 'admin/payment_proofs/3.png', 'Pending', '2024-12-21 01:37:33'),
(98, 'luthfi2264a@gmail.com', 'dawd', 'Jl. Jayapura f. 19 ulakkarang selatan asratek', '085805786626', 37209802.00, 'admin/payment_proofs/3.png', 'Pending', '2024-12-21 01:38:55'),
(99, 'luthfi2264a@gmail.com', 'dawdawdadawdadad', 'dasdsssssssssssss', '085805786626ad', 38506171.00, 'admin/payment_proofs/1.png', 'Pending', '2024-12-21 01:55:08');

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaction_details`
--

CREATE TABLE `transaction_details` (
  `id` int(11) NOT NULL,
  `transaction_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `level` enum('admin','user') NOT NULL DEFAULT 'user',
  `last_login` datetime DEFAULT NULL,
  `last_logout` datetime DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`id`, `email`, `password`, `level`, `last_login`, `last_logout`, `reset_token`, `reset_token_expiry`) VALUES
(2, 'luthfi2264a@gmail.com', '$2y$10$gVR4VokGqAOGPf5YSD70X.470hfrH01gG89LzcbP2MtJ4Mt7WV8SC', 'user', '2024-12-20 18:42:20', '2024-12-20 18:42:10', '5156266b1c5f6f96b5f6ffad4c060a99', '2024-12-16 15:26:13'),
(6, 'admin@gmail.com', '$2y$10$/BC232jL9mv.g3cwSgzbsu.iabGPSSPn8CGBxYuZ24HRIKlcgyNUu', 'admin', '2024-12-17 16:11:15', '2024-12-18 03:29:50', NULL, NULL),
(7, 'sdfs@gmail.com', '$2y$10$FrEV9uuKuC8Hyicz6kn3ieib6DzuaVeoZMibhS0Qc8IXu4y5mxqiC', 'user', '2024-12-14 08:17:27', '2024-12-14 08:25:47', NULL, NULL),
(8, 'jaga@nwjns.es', '$2y$10$UjobQzhyE9zkWqLa3M0/cueIw0YMQSVXScOc00so7wE5pX7A85UlO', 'user', '2024-12-19 13:45:13', '2024-12-14 02:58:14', NULL, NULL),
(9, 'sasa@gmail.com', '$2y$10$ZAPCiFwmsWJAkzkjaKwTlexABGBdYoWONrnzMomXbUXPTdrvfUiGC', 'user', '2024-12-14 08:28:22', '2024-12-14 09:14:22', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_cart` (`user_id`,`product_id`);

--
-- Indeks untuk tabel `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indeks untuk tabel `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `order_item`
--
ALTER TABLE `order_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `order_item_ibfk_1` (`transaction_id`);

--
-- Indeks untuk tabel `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_category` (`category_id`);

--
-- Indeks untuk tabel `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indeks untuk tabel `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `transaction_details`
--
ALTER TABLE `transaction_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaction_id` (`transaction_id`);

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=238;

--
-- AUTO_INCREMENT untuk tabel `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `order_item`
--
ALTER TABLE `order_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT untuk tabel `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT untuk tabel `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT untuk tabel `transaction_details`
--
ALTER TABLE `transaction_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `order_item`
--
ALTER TABLE `order_item`
  ADD CONSTRAINT `order_item_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_category` FOREIGN KEY (`category_id`) REFERENCES `kategori` (`id_kategori`);

--
-- Ketidakleluasaan untuk tabel `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
