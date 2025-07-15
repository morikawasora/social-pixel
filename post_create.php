<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
  echo json_encode(['success' => false, 'message' => 'ログインしてください']);
  exit;
}

$user_id = $_SESSION['user_id'];
$content = $_POST['content'] ?? '';
$content = trim($content);

// 画像・動画がアップロードされているかチェック
$hasMedia = !empty($_FILES['media_files']) && is_array($_FILES['media_files']['name']) && count(array_filter($_FILES['media_files']['name'])) > 0;

// 本文もメディアもない場合はエラー
if ($content === '' && !$hasMedia) {
  echo json_encode(['success' => false, 'message' => '本文かメディアのいずれかを入力してください']);
  exit;
}

try {
  $pdo->beginTransaction();

  $stmt = $pdo->prepare("INSERT INTO posts (user_id, content, created_at) VALUES (?, ?, NOW())");
  $stmt->execute([$user_id, $content]);
  $post_id = $pdo->lastInsertId();

  if ($hasMedia) {
    $files = $_FILES['media_files'];

    for ($i = 0; $i < count($files['name']); $i++) {
      if ($files['error'][$i] !== UPLOAD_ERR_OK) continue;

      $tmp_name = $files['tmp_name'][$i];
      $name = basename($files['name'][$i]);
      $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

      $allowed_img = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
      $allowed_vid = ['mp4', 'mov', 'avi', 'wmv', 'flv', 'mkv'];

      if (!in_array($ext, array_merge($allowed_img, $allowed_vid))) {
        continue;
      }

      $new_name = uniqid('media_') . '.' . $ext;
      $upload_dir = 'uploads/posts/';

      if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
      }

      $dest = $upload_dir . $new_name;
      if (move_uploaded_file($tmp_name, $dest)) {
        $media_type = in_array($ext, $allowed_img) ? 'image' : 'video';

        $stmt2 = $pdo->prepare("INSERT INTO post_media (post_id, file_path, media_type) VALUES (?, ?, ?)");
        $stmt2->execute([$post_id, $dest, $media_type]);
      }
    }
  }

  $pdo->commit();
  echo json_encode(['success' => true]);

} catch (Exception $e) {
  $pdo->rollBack();
  echo json_encode(['success' => false, 'message' => '投稿処理でエラーが発生しました']);
}
