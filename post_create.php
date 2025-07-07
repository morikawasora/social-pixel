<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
  http_response_code(403);
  echo json_encode(['error' => 'ログインしてください']);
  exit;
}

$content = trim($_POST['content'] ?? '');

if ($content === '') {
  http_response_code(400);
  echo json_encode(['error' => '内容が空です']);
  exit;
}

// DBへ保存
$stmt = $pdo->prepare("INSERT INTO posts (user_id, content) VALUES (?, ?)");
$stmt->execute([$_SESSION['user_id'], $content]);

echo json_encode(['success' => true]);
