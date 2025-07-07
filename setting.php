<?php
session_start();
$logged_in = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>S&P - 設定</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="main">
    <h1>設定</h1>

    <?php if ($logged_in): ?>
      <p>ようこそ、<?= htmlspecialchars($_SESSION['username']) ?> さん</p>
      <p><a href="index.php">← ホームに戻る</a></p>
      <p><a href="logout.php">🚪 ログアウトする</a></p>
    <?php else: ?>
      <p>このページを表示するにはログインしてください。</p>
      <p><a href="login.php">ログイン画面へ</a></p>
    <?php endif; ?>
  </div>
  <script src="script.js"></script>
</body>
</html>
