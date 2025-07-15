<?php
session_start();
require 'db_connect.php';

header('Content-Type: application/json');

$post_id = $_GET['post_id'] ?? null;
if (!$post_id) {
    echo json_encode([]);
    exit;
}

// コメント一覧をユーザー情報（username, icon, id）と結合して取得
$sql = "
SELECT 
  c.id, c.post_id, c.user_id, c.content, c.created_at,
  u.username, u.icon
FROM comments c
JOIN users u ON c.user_id = u.id
WHERE c.post_id = ?
ORDER BY c.created_at ASC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$post_id]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($comments);
