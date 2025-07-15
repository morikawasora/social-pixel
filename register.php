<?php
session_start();
require 'includes/db_connect.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username']);
  $email = trim($_POST['email']);
  $password = $_POST['password'];
  $password_confirm = $_POST['password_confirm'];

  // 入力チェック
  if (strlen($username) < 3) {
    $error = 'ユーザー名は3文字以上にしてください。';
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = '有効なメールアドレスを入力してください。';
  } elseif ($password !== $password_confirm) {
    $error = 'パスワードが一致しません。';
  } elseif (strlen($password) < 6) {
    $error = 'パスワードは6文字以上にしてください。';
  } else {
    // 同じユーザー名またはメールアドレスがすでに存在するか確認
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);

    if ($stmt->fetch()) {
      $error = 'そのユーザー名またはメールアドレスはすでに使われています。';
    } else {
      // デフォルトアイコン
      $iconFileName = 'default.png';

      // アイコン画像アップロード処理
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

      // 新規登録処理
      $hash = password_hash($password, PASSWORD_DEFAULT);
      $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, icon) VALUES (?, ?, ?, ?)");
      $stmt->execute([$username, $email, $hash, $iconFileName]);

      // 自動ログインしてトップページにリダイレクト
      $_SESSION['user_id'] = $pdo->lastInsertId();
      $_SESSION['username'] = $username;
      header("Location: index.php");
      exit;
    }
  }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>アカウント作成 - S&P</title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="login-body">
  <div class="login-container">
    <h2>アカウント作成</h2>
    <form method="post" enctype="multipart/form-data">
      <input type="text" name="username" placeholder="ユーザー名（3文字以上）" required>
      <input type="email" name="email" placeholder="メールアドレス" required>
      <input type="password" name="password" placeholder="パスワード（6文字以上）" required>
      <input type="password" name="password_confirm" placeholder="パスワード再入力" required>
      <label>アイコン（任意）:<input type="file" name="icon"></label><br>
      <button type="submit">登録</button>
    </form>
    <p style="color:red;">
      <?= htmlspecialchars($error) ?>
    </p>
    <p>すでにアカウントをお持ちの方は <a href="login.php">ログイン</a></p>
  </div>
</body>
</html>
