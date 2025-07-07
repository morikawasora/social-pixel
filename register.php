<?php
session_start();
require 'php/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username']);
  $email = trim($_POST['email']);
  $password = $_POST['password'];

  $errors = [];

  // 簡単なバリデーション
  if (empty($username) || empty($email) || empty($password)) {
    $errors[] = 'すべての項目を入力してください。';
  }

  // デフォルトアイコン
  $iconFileName = 'default.png';

  if (isset($_FILES['icon']) && $_FILES['icon']['error'] === UPLOAD_ERR_OK) {
    $tmpName = $_FILES['icon']['tmp_name'];
    $originalName = basename($_FILES['icon']['name']);
    $ext = pathinfo($originalName, PATHINFO_EXTENSION);
    $newFileName = uniqid() . '.' . $ext;
    $uploadPath = 'uploads/' . $newFileName;

    if (move_uploaded_file($tmpName, $uploadPath)) {
      $iconFileName = $newFileName;
    }
  }

  if (empty($errors)) {
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, icon) VALUES (?, ?, ?, ?)");
    $stmt->execute([
      $username,
      $email,
      password_hash($password, PASSWORD_DEFAULT),
      $iconFileName
    ]);
    header('Location: login.php');
    exit;
  }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>ユーザー登録</title>
</head>
<body>
  <h1>ユーザー登録</h1>
  <?php if (!empty($errors)): ?>
    <ul style="color:red;">
      <?php foreach ($errors as $e): ?>
        <li><?= htmlspecialchars($e) ?></li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data">
    名前: <input type="text" name="username"><br>
    メール: <input type="email" name="email"><br>
    パスワード: <input type="password" name="password"><br>
    アイコン（任意）: <input type="file" name="icon"><br>
    <button type="submit">登録</button>
  </form>
</body>
</html>
