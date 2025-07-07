<?php
session_start();
require 'db_connect.php';

$user_id = $_SESSION['user_id'] ?? 0;

$stmt = $pdo->prepare("
  SELECT
    posts.id,
    posts.content,
    posts.created_at,
    users.username,
    users.icon,
    (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id) AS like_count,
    EXISTS (
      SELECT 1 FROM likes WHERE likes.post_id = posts.id AND likes.user_id = ?
    ) AS liked_by_user
  FROM posts
  JOIN users ON posts.user_id = users.id
  ORDER BY posts.created_at DESC
");
$stmt->execute([$user_id]);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($posts);
