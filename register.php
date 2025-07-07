<?php
session_start();
require 'db_connect.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $username = trim($_POST['username']);
  $password = $_POST['password'];

  if (strlen($username) < 3 || strlen($password) < 6) {
    $error = 'ユーザー名は3文字以上、パスワードは6文字以上にしてください。';
  } else {
    // 同じユーザー名がすでに存在するか確認
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);

    if ($stmt->fetch()) {
      $error = 'そのユーザー名はすでに使われています。';
    } else {
      // 新規登録処理
      $hash = password_hash($password, PASSWORD_DEFAULT);
      $stmt = $pdo->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
      $stmt->execute([$username, $hash]);

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
    <form method="post">
      <input type="text" name="username" placeholder="ユーザー名" required>
      <input type="password" name="password" placeholder="パスワード（6文字以上）" required>
      <button type="submit">登録</button>
    </form>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <p>すでにアカウントをお持ちの方は <a href="login.php">ログイン</a></p>
  </div>
</body>
</html>
