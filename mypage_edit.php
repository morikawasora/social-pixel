<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

$errorMessages = [];
$successMessages = [];

// 現在のユーザー情報取得
$stmt = $pdo->prepare("SELECT username, email, bio, icon FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) {
  die("ユーザー情報が見つかりません。");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $newUsername = trim($_POST['username'] ?? '');
  $newEmail = trim($_POST['email'] ?? '');
  $newBio = trim($_POST['bio'] ?? '');
  $iconFileName = $user['icon']; // 現在のアイコンを初期値に

  // ユーザー名が空かチェック
  if ($newUsername === '') {
    $errorMessages[] = "ユーザー名は空にできません。";
  } else {
    // 重複チェック（自分以外）
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? AND id != ?");
    $stmt->execute([$newUsername, $_SESSION['user_id']]);
    if ($stmt->fetchColumn() > 0) {
      $errorMessages[] = "そのユーザー名は既に使われています。";
    }
  }

  if (empty($errorMessages)) {
    // 画像アップロード処理
    if (isset($_FILES['icon']) && $_FILES['icon']['error'] === UPLOAD_ERR_OK) {
      $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
      $fileType = mime_content_type($_FILES['icon']['tmp_name']);

      if (in_array($fileType, $allowedTypes)) {
        $uploadDir = __DIR__ . '/uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $ext = pathinfo($_FILES['icon']['name'], PATHINFO_EXTENSION);
        $newFileName = 'icon_' . $_SESSION['user_id'] . '_' . time() . '.' . $ext;
        $uploadFile = $uploadDir . $newFileName;

        if (move_uploaded_file($_FILES['icon']['tmp_name'], $uploadFile)) {
          // 古いアイコン削除
          if (!empty($user['icon']) && file_exists($uploadDir . $user['icon'])) {
            unlink($uploadDir . $user['icon']);
          }
          $iconFileName = $newFileName;
          $successMessages[] = "アイコン画像を更新しました。";
        } else {
          $errorMessages[] = "画像のアップロードに失敗しました。";
        }
      } else {
        $errorMessages[] = "JPEG, PNG, GIF形式の画像のみアップロード可能です。";
      }
    }

    // DB更新処理
    $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, bio = ?, icon = ? WHERE id = ?");
    $stmt->execute([$newUsername, $newEmail, $newBio, $iconFileName, $_SESSION['user_id']]);

    $_SESSION['username'] = $newUsername;
    $successMessages[] = "プロフィールを更新しました。";

    // 更新後に再取得
    $stmt = $pdo->prepare("SELECT username, email, bio, icon FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
  }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8" />
  <title>S&P - プロフィール編集</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <div class="main">
    <h1>プロフィール編集</h1>

    <?php foreach ($errorMessages as $msg): ?>
      <p style="color: red;"><?= htmlspecialchars($msg) ?></p>
    <?php endforeach; ?>

    <?php foreach ($successMessages as $msg): ?>
      <p style="color: green;"><?= htmlspecialchars($msg) ?></p>
    <?php endforeach; ?>

    <form method="post" enctype="multipart/form-data">
      <label>ユーザー名:</label><br>
      <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required><br><br>

      <label>メールアドレス:</label><br>
      <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>"><br><br>

      <label>自己紹介:</label><br>
      <textarea name="bio" rows="4"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea><br><br>

      <label>アイコン画像:</label><br>
      <?php if (!empty($user['icon'])): ?>
        <img src="uploads/<?= htmlspecialchars($user['icon']) ?>" style="max-width:100px;"><br>
      <?php endif; ?>
      <input type="file" name="icon" accept="image/*"><br><br>

      <button type="submit">更新する</button>
    </form>

    <p><a href="mypage.php">← マイページに戻る</a></p>
  </div>
</body>
</html>
