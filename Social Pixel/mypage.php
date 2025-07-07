<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

// ユーザー情報取得
$stmt = $pdo->prepare("SELECT username, email, bio, icon FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
  die("ユーザー情報が見つかりません。");
}

$iconPath = !empty($user['icon']) ? 'uploads/' . htmlspecialchars($user['icon']) : 'uploads/default.png';
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8" />
  <title>S&P - マイページ</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    .profile-icon {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid #ccc;
      margin-bottom: 1rem;
    }
  </style>
</head>
<body>

<?php 
  include 'sidebar.php';
  $bio = isset($user['bio']) ? trim($user['bio']) : '';
 ?>

<div class="main" id="mainContent">
  <h1>👤 マイページ</h1>

  <img src="<?= !empty($sidebarUser['icon']) ? 'uploads/' . htmlspecialchars($sidebarUser['icon']) : 'uploads/default.png' ?>" class="sidebar-icon">
  <div class="sidebar-username"><?= htmlspecialchars($sidebarUser['username']) ?></div>
  <p><strong>ユーザー名:</strong> <?= htmlspecialchars($user['username']) ?></p>
  <p><strong>メールアドレス:</strong> <?= !empty($user['email']) ? htmlspecialchars($user['email']) : '未登録' ?></p>
  <p><strong>自己紹介:</strong><br>
  <?= $bio !== '' ? nl2br(htmlspecialchars($bio)) : '未記入' ?>
  </p>
  <p><a href="mypage_edit.php">プロフィールを編集する</a></p>
  <p><a href="index.php">← ホームに戻る</a></p>
</div>

</body>
</html>
