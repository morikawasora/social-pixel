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

// 投稿の投稿者IDを取得
$stmt = $pdo->prepare("SELECT user_id FROM posts WHERE id = ?");
$stmt->execute([$postId]);
$post = $stmt->fetch();

if (!$post) {
  echo json_encode(['success' => false, 'message' => '投稿が見つかりません']);
  exit;
}

$postOwnerId = $post['user_id'];

// 既に「いいね」しているか確認
$stmt = $pdo->prepare("SELECT * FROM likes WHERE user_id = ? AND post_id = ?");
$stmt->execute([$userId, $postId]);
$existing = $stmt->fetch();

if ($existing) {
  // いいね解除
  $stmt = $pdo->prepare("DELETE FROM likes WHERE user_id = ? AND post_id = ?");
  $stmt->execute([$userId, $postId]);

  // 通知削除（解除されたから）
  $stmt = $pdo->prepare("DELETE FROM notifications WHERE user_id = ? AND from_user_id = ? AND post_id = ? AND type = 'like'");
  $stmt->execute([$postOwnerId, $userId, $postId]);

  echo json_encode(['success' => true, 'liked' => false]);
} else {
  // いいね追加
  $stmt = $pdo->prepare("INSERT INTO likes (user_id, post_id) VALUES (?, ?)");
  $stmt->execute([$userId, $postId]);

  // 自分の投稿には通知を作らない（重複防止）
  if ($postOwnerId != $userId) {
    $stmt = $pdo->prepare("INSERT INTO notifications (user_id, from_user_id, post_id, type, created_at) VALUES (?, ?, ?, 'like', NOW())");
    $stmt->execute([$postOwnerId, $userId, $postId]);
  }

  echo json_encode(['success' => true, 'liked' => true]);
}
?>
