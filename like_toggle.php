<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(403);
  echo json_encode(['success' => false]);
  exit;
}

$userId = $_SESSION['user_id'];
$postId = (int)($_POST['post_id'] ?? 0);

// 既に「いいね」しているか確認
$stmt = $pdo->prepare("SELECT * FROM likes WHERE user_id = ? AND post_id = ?");
$stmt->execute([$userId, $postId]);
$existing = $stmt->fetch();

if ($existing) {
  // 既にいいね → 取り消す
  $stmt = $pdo->prepare("DELETE FROM likes WHERE user_id = ? AND post_id = ?");
  $stmt->execute([$userId, $postId]);
  echo json_encode(['success' => true, 'liked' => false]);
} else {
  // まだいいねしてない → 登録
  $stmt = $pdo->prepare("INSERT INTO likes (user_id, post_id) VALUES (?, ?)");
  $stmt->execute([$userId, $postId]);
  echo json_encode(['success' => true, 'liked' => true]);
}
?>
