<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id']) || empty($_POST['post_id']) || empty($_POST['content'])) {
  echo json_encode(['success' => false]);
  exit;
}

$post_id = $_POST['post_id'];
$content = trim($_POST['content']);

$stmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)");
$stmt->execute([$post_id, $_SESSION['user_id'], $content]);

echo json_encode(['success' => true]);
