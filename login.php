<?php
session_start();
require 'db_connect.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $username = trim($_POST['username']);
  $password = $_POST['password'];

  $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
  $stmt->execute([$username]);
  $user = $stmt->fetch();

if ($user && password_verify($password, $user['password_hash'])) {
  session_regenerate_id(true);
  $_SESSION['user_id'] = $user['id'];
  $_SESSION['username'] = $user['username'];
  header("Location: index.php");
  exit;
  } else {
  $error = 'ユーザー名またはパスワードが違います。';
  }

}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>ログイン - S&P</title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="login-body">
  <div class="login-container">
    <h2>ログイン</h2>
    <form method="post">
      <input type="text" name="username" placeholder="ユーザー名" required>
      <input type="password" name="password" placeholder="パスワード" required>
      <button type="submit">ログイン</button>
    </form>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <!-- アカウント作成リンク -->
アカウントを持っていない人は <a href="register.php">こちら</a>
  </div>
</body>
</html>
