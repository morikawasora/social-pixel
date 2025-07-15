-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- ホスト: 127.0.0.1
-- 生成日時: 2025-07-15 02:43:44
-- サーバのバージョン： 10.4.32-MariaDB
-- PHP のバージョン: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- データベース: `sns_php`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `email` varchar(100) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`, `created_at`, `email`, `bio`, `icon`) VALUES
(3, 'legister_test', '$2y$10$/jgZ4D.n8jgXcSYOd68P4Od3OfFkihpqjTBhpnM3I945coqOzTb0.', '2025-06-16 13:37:20', '240160gc@yse-c.net', 'こんにちは', 'icon_3_1750054110.png'),
(4, 'test', '$2y$10$9hGE4XrAKS1fyRCA4/kWUOrFIZxcVj37Kr3gK9AsHuwwH1DINOE6e', '2025-06-16 13:47:39', '', 'よろしくお願いします', 'icon_4_1750651796.png'),
(5, '森川蒼良', '$2y$10$xSBFYFzB//vUwoS1ggTkJ.XDI7tvQZWxidDTfF0oSUN195MBTmq3.', '2025-07-01 11:06:24', '', 'はろー', 'icon_5_1751335601.png'),
(6, '発表太郎', '$2y$10$CcTOxkE27CUeVSuL1.CDDu534VCwlpg3tmuf3uXZp5E1kXfb8XfsG', '2025-07-01 11:22:07', '', '変わっていますか？', 'icon_6_1752477702.png'),
(7, 'tester', '$2y$10$udFK1hls7.DtJKGBmz2EJe6EKozkOrRuG.dhLhM.xXmmEScoIIm/C', '2025-07-07 14:09:15', NULL, NULL, NULL);

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- ダンプしたテーブルの AUTO_INCREMENT
--

--
-- テーブルの AUTO_INCREMENT `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
