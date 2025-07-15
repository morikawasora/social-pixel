<?php
session_start();
require 'db_connect.php';

if (!isset($_GET['id'])) {
  echo "投稿が見つかりません。";
  exit;
}

$post_id = $_GET['id'];

$stmt = $pdo->prepare("SELECT p.*, u.username, u.icon FROM posts p JOIN users u ON p.user_id = u.id WHERE p.id = ?");
$stmt->execute([$post_id]);
$post = $stmt->fetch();

if (!$post) {
  echo "投稿が見つかりません。";
  exit;
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>投稿詳細</title>
</head>
<body>
  <h1>投稿詳細</h1>
  <p><strong><?= htmlspecialchars($post['username']) ?></strong></p>
  <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
  <p><?= $post['created_at'] ?></p>
  <a href="index.php">← 戻る</a>
</body>
</html>
