<?php
session_start();
require 'db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
  echo json_encode([]);
  exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
  SELECT n.id, n.type, n.created_at, u.username AS from_username, u.icon AS from_icon, p.content AS post_content, p.id AS post_id
  FROM notifications n
  JOIN users u ON n.from_user_id = u.id
  JOIN posts p ON n.post_id = p.id
  WHERE n.user_id = ? AND n.is_read = 0
  ORDER BY n.created_at DESC
");
$stmt->execute([$user_id]);

echo json_encode($stmt->fetchAll());
exit;
