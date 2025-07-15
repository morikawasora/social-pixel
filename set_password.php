<?php
session_start();
require 'includes/db_connect.php'; // DB接続

if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $current_password = $_POST['current_password'] ?? '';
  $new_password = $_POST['new_password'] ?? '';
  $confirm_password = $_POST['confirm_password'] ?? '';

  // 入力バリデーション
  if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
    $errors[] = 'すべての項目を入力してください。';
  } elseif ($new_password !== $confirm_password) {
    $errors[] = '新しいパスワードと確認用パスワードが一致しません。';
  } elseif (strlen($new_password) < 6) {
    $errors[] = 'パスワードは6文字以上で入力してください。';
  } else {
    // 現在のパスワード確認
    $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    if ($user && password_verify($current_password, $user['password_hash'])) {
      // 新しいパスワードをハッシュ化して保存
      $hashed = password_hash($new_password, PASSWORD_DEFAULT);
      $update_stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
      $update_stmt->execute([$hashed, $_SESSION['user_id']]);
      $success = 'パスワードを変更しました。';
    } else {
      $errors[] = '現在のパスワードが正しくありません。';
    }
  }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>パスワード変更</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="main">
  <h1>パスワード変更</h1>

  <?php if (!empty($success)): ?>
    <p style="color: green;"><?= htmlspecialchars($success) ?></p>
    <p><a href="setting.php">← 設定に戻る</a></p>
  <?php else: ?>
    <?php if (!empty($errors)): ?>
      <ul style="color: red;">
        <?php foreach ($errors as $error): ?>
          <li><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>

    <form action="set_password.php" method="post">
      <label>現在のパスワード:<br>
        <input type="password" name="current_password" required>
      </label><br><br>

      <label>新しいパスワード:<br>
        <input type="password" name="new_password" required>
      </label><br><br>

      <label>確認用パスワード:<br>
        <input type="password" name="confirm_password" required>
      </label><br><br>

      <button type="submit">パスワードを変更</button>
    </form>

    <p><a href="setting.php">← 設定に戻る</a></p>
  <?php endif; ?>
</div>
</body>
</html>
