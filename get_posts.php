<?php
session_start();
require 'db_connect.php';

$stmt = $pdo->prepare("
  SELECT posts.*, users.username, users.icon
  FROM posts
  JOIN users ON posts.user_id = users.id
  ORDER BY posts.created_at DESC
");
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($posts);
