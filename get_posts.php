<?php
session_start();
require 'db_connect.php';

$user_id = $_SESSION['user_id'];

// 投稿とユーザー情報取得＋いいね数・いいね済み情報を取得
$stmt = $pdo->prepare("
  SELECT 
    p.*,
    u.username,
    u.icon,
    (SELECT COUNT(*) FROM likes WHERE post_id = p.id) AS like_count,
    (SELECT 1 FROM likes WHERE post_id = p.id AND user_id = ?) AS liked_by_user
  FROM posts p
  JOIN users u ON p.user_id = u.id
  ORDER BY p.created_at DESC
  LIMIT 10
");
$stmt->execute([$user_id]);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// メディア情報取得
$stmt_media = $pdo->prepare("SELECT file_path, media_type FROM post_media WHERE post_id = ?");

foreach ($posts as &$post) {
  $stmt_media->execute([$post['id']]);
  $post['media'] = $stmt_media->fetchAll(PDO::FETCH_ASSOC);

  // liked_by_user が null の場合は 0 にする（false扱い）
  $post['liked_by_user'] = $post['liked_by_user'] ? 1 : 0;
}

header('Content-Type: application/json');
echo json_encode($posts);
