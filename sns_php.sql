-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- ホスト: 127.0.0.1
-- 生成日時: 2025-07-15 03:28:58
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
-- テーブルの構造 `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `comments`
--

INSERT INTO `comments` (`id`, `user_id`, `post_id`, `content`, `created_at`) VALUES
(1, 5, 6, 'おはよう', '2025-07-07 14:55:10'),
(2, 5, 6, 'こんちは', '2025-07-07 14:58:06'),
(3, 5, 6, '元気か？', '2025-07-07 15:00:34'),
(4, 5, 5, '俺は元気だよ', '2025-07-07 15:09:37'),
(5, 6, 9, 'まだ未完成だけど', '2025-07-14 15:45:41');

-- --------------------------------------------------------

--
-- テーブルの構造 `follows`
--

CREATE TABLE `follows` (
  `id` int(11) NOT NULL,
  `follower_id` int(11) NOT NULL,
  `followed_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `follows`
--

INSERT INTO `follows` (`id`, `follower_id`, `followed_id`, `created_at`) VALUES
(2, 5, 6, '2025-07-07 15:54:11'),
(3, 6, 5, '2025-07-14 15:26:07'),
(4, 6, 4, '2025-07-14 15:30:17'),
(5, 6, 3, '2025-07-14 15:30:21');

-- --------------------------------------------------------

--
-- テーブルの構造 `likes`
--

CREATE TABLE `likes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `likes`
--

INSERT INTO `likes` (`id`, `user_id`, `post_id`, `created_at`) VALUES
(2, 6, 6, '2025-07-07 13:17:31'),
(8, 6, 4, '2025-07-07 13:57:50'),
(12, 5, 6, '2025-07-07 15:02:43'),
(13, 5, 5, '2025-07-07 15:02:47'),
(14, 6, 5, '2025-07-07 15:10:26'),
(22, 6, 9, '2025-07-14 15:45:13');

-- --------------------------------------------------------

--
-- テーブルの構造 `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `from_user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `type` varchar(20) NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `from_user_id`, `post_id`, `type`, `is_read`, `created_at`) VALUES
(1, 4, 6, 4, 'like', 0, '2025-07-07 13:57:50'),
(5, 6, 5, 6, 'like', 1, '2025-07-07 15:02:43'),
(6, 5, 6, 5, 'like', 1, '2025-07-07 15:10:26'),
(12, 5, 6, 9, 'like', 0, '2025-07-14 15:45:13');

-- --------------------------------------------------------

--
-- テーブルの構造 `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `posts`
--

INSERT INTO `posts` (`id`, `user_id`, `content`, `created_at`) VALUES
(1, 3, 'あ', '2025-06-16 14:57:14'),
(2, 3, 'こんにちは', '2025-06-16 14:58:43'),
(3, 3, 'ああああ', '2025-06-16 15:46:09'),
(4, 4, 'aaaaaa', '2025-07-01 11:01:30'),
(5, 5, 'お元気ですか？', '2025-07-01 11:06:59'),
(6, 6, 'おはようございます', '2025-07-01 11:22:43'),
(7, 5, '今日も今日とて飯がうまい', '2025-07-07 15:42:43'),
(8, 5, 'ゲーム作った', '2025-07-07 16:02:13'),
(9, 5, 'こんなの作ってみたよ', '2025-07-07 16:11:02'),
(10, 6, '', '2025-07-14 16:19:17'),
(11, 8, 'このメッセージが　みれたら　おかしいよ', '2025-07-15 10:25:31');

-- --------------------------------------------------------

--
-- テーブルの構造 `post_media`
--

CREATE TABLE `post_media` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `media_type` enum('image','video') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `post_media`
--

INSERT INTO `post_media` (`id`, `post_id`, `file_path`, `media_type`) VALUES
(1, 8, 'uploads/posts/media_686b70f52a8bd.mp4', 'video'),
(2, 9, 'uploads/posts/media_686b73068d4f4.png', 'image'),
(3, 10, 'uploads/posts/media_6874af75bc73e.jpeg', 'image');

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
(6, '発表太郎', '$2y$10$vEJlC2nLNIWEXBzwgkblYOOPlS32Pe/uhkDm18C26/489gbk0Rg6K', '2025-07-01 11:22:07', '', '変わっていますか？', 'icon_6_1752477702.png'),
(7, 'tester', '$2y$10$udFK1hls7.DtJKGBmz2EJe6EKozkOrRuG.dhLhM.xXmmEScoIIm/C', '2025-07-07 14:09:15', NULL, NULL, NULL),
(8, 'テストマン', '$2y$10$kcjkbSi64cARQ6e1DK3Nmu1cNC0/j9isaEWFnEOpw3QcbPZKLgaie', '2025-07-15 10:08:17', 'user1@test.com', 'ダミーのテスト君やで', 'default.png');

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `post_id` (`post_id`);

--
-- テーブルのインデックス `follows`
--
ALTER TABLE `follows`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_follow` (`follower_id`,`followed_id`),
  ADD KEY `followed_id` (`followed_id`);

--
-- テーブルのインデックス `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_like` (`user_id`,`post_id`),
  ADD KEY `post_id` (`post_id`);

--
-- テーブルのインデックス `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `from_user_id` (`from_user_id`),
  ADD KEY `post_id` (`post_id`);

--
-- テーブルのインデックス `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- テーブルのインデックス `post_media`
--
ALTER TABLE `post_media`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`);

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
-- テーブルの AUTO_INCREMENT `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- テーブルの AUTO_INCREMENT `follows`
--
ALTER TABLE `follows`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- テーブルの AUTO_INCREMENT `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- テーブルの AUTO_INCREMENT `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- テーブルの AUTO_INCREMENT `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- テーブルの AUTO_INCREMENT `post_media`
--
ALTER TABLE `post_media`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- テーブルの AUTO_INCREMENT `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- ダンプしたテーブルの制約
--

--
-- テーブルの制約 `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE;

--
-- テーブルの制約 `follows`
--
ALTER TABLE `follows`
  ADD CONSTRAINT `follows_ibfk_1` FOREIGN KEY (`follower_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `follows_ibfk_2` FOREIGN KEY (`followed_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- テーブルの制約 `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE;

--
-- テーブルの制約 `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`from_user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `notifications_ibfk_3` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`);

--
-- テーブルの制約 `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- テーブルの制約 `post_media`
--
ALTER TABLE `post_media`
  ADD CONSTRAINT `post_media_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
