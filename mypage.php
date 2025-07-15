<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

// 表示対象のユーザーID（自分 or 他人）
$profile_user_id = isset($_GET['id']) ? (int)$_GET['id'] : $_SESSION['user_id'];
$is_own_page = $profile_user_id === $_SESSION['user_id'];

// ユーザー情報取得
$stmt = $pdo->prepare("SELECT id, username, email, bio, icon FROM users WHERE id = ?");
$stmt->execute([$profile_user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
  die("ユーザー情報が見つかりりません。");
}

// フォロー数
$stmt = $pdo->prepare("SELECT COUNT(*) FROM follows WHERE follower_id = ?");
$stmt->execute([$profile_user_id]);
$following_count = $stmt->fetchColumn();

// フォロワー数
$stmt = $pdo->prepare("SELECT COUNT(*) FROM follows WHERE followed_id = ?");
$stmt->execute([$profile_user_id]);
$follower_count = $stmt->fetchColumn();

// フォロー中のユーザー一覧（自分のページの場合のみ取得）
$following_users = [];
if ($is_own_page) {
  $stmt = $pdo->prepare("
    SELECT u.id, u.username, u.icon 
    FROM follows f 
    JOIN users u ON f.followed_id = u.id 
    WHERE f.follower_id = ?
  ");
  $stmt->execute([$profile_user_id]);
  $following_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// フォロワーのユーザー一覧（自分のページの場合のみ取得）
$follower_users = [];
if ($is_own_page) {
  $stmt = $pdo->prepare("
    SELECT u.id, u.username, u.icon 
    FROM follows f 
    JOIN users u ON f.follower_id = u.id 
    WHERE f.followed_id = ?
  ");
  $stmt->execute([$profile_user_id]);
  $follower_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// フォロー状態（他人のページを見ている場合のみ）
$is_following = false;
if (!$is_own_page) {
  $stmt = $pdo->prepare("SELECT 1 FROM follows WHERE follower_id = ? AND followed_id = ?");
  $stmt->execute([$_SESSION['user_id'], $profile_user_id]);
  $is_following = $stmt->fetch() ? true : false;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8" />
  <title>S&P - マイページ</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    .profile-icon {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid #ccc;
      margin-bottom: 1rem;
    }
    .follow-btn {
      padding: 6px 12px;
      background-color: #0084ff;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      margin-bottom: 1rem;
    }
    .follow-btn.unfollow {
      background-color: #aaa;
    }
    .user-list {
      margin-top: 1rem;
      display: none; /* 初期状態で非表示 */
    }
    .user-list.active {
      display: block; /* トグルで表示 */
    }
    .user-list-item {
      display: flex;
      align-items: center;
      margin-bottom: 0.5rem;
    }
    .user-list-item img {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      margin-right: 10px;
    }
    .clickable {
      cursor: pointer;
      color: #0084ff;
      text-decoration: underline;
    }
  </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main" id="mainContent">
  <h1>👤 <?= $is_own_page ? 'マイページ' : 'ユーザープロフィール' ?></h1>

  <img src="<?= !empty($user['icon']) ? 'uploads/' . htmlspecialchars($user['icon']) : 'uploads/default.png' ?>" class="profile-icon" alt="プロフィールアイコン">

  <p><strong>ユーザー名:</strong> <?= htmlspecialchars($user['username']) ?></p>
  <p><strong>メールアドレス:</strong> <?= !empty($user['email']) ? htmlspecialchars($user['email']) : '未登録' ?></p>
  <p><strong>自己紹介:</strong><br>
  <?= !empty(trim($user['bio'])) ? nl2br(htmlspecialchars($user['bio'])) : '未記入' ?>
  </p>

  <!-- フォロー・フォロワー表示 -->
  <p>
    <strong>フォロー中:</strong> 
    <?php if ($is_own_page): ?>
      <span class="clickable" onclick="toggleList('following-list')"><?= $following_count ?>人</span>
    <?php else: ?>
      <?= $following_count ?>人
    <?php endif; ?>
    　<strong>フォロワー:</strong> 
    <?php if ($is_own_page): ?>
      <span class="clickable" onclick="toggleList('follower-list')"><?= $follower_count ?>人</span>
    <?php else: ?>
      <?= $follower_count ?>人
    <?php endif; ?>
  </p>

  <?php if ($is_own_page): ?>
    <!-- フォロー中のユーザー一覧 -->
    <div class="user-list" id="following-list">
      <h3>フォロー中のユーザー</h3>
      <?php if (empty($following_users)): ?>
        <p>フォロー中のユーザーはいません。</p>
      <?php else: ?>
        <?php foreach ($following_users as $following_user): ?>
          <div class="user-list-item">
            <img src="<?= !empty($following_user['icon']) ? 'uploads/' . htmlspecialchars($following_user['icon']) : 'uploads/default.png' ?>" alt="ユーザーアイコン">
            <a href="mypage.php?id=<?= $following_user['id'] ?>"><?= htmlspecialchars($following_user['username']) ?></a>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <!-- フォロワーのユーザー一覧 -->
    <div class="user-list" id="follower-list">
      <h3>フォロワー</h3>
      <?php if (empty($follower_users)): ?>
        <p>フォロワーはいません。</p>
      <?php else: ?>
        <?php foreach ($follower_users as $follower_user): ?>
          <div class="user-list-item">
            <img src="<?= !empty($follower_user['icon']) ? 'uploads/' . htmlspecialchars($follower_user['icon']) : 'uploads/default.png' ?>" alt="ユーザーアイコン">
            <a href="mypage.php?id=<?= $follower_user['id'] ?>"><?= htmlspecialchars($follower_user['username']) ?></a>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <?php if (!$is_own_page): ?>
    <form method="POST" action="follow_toggle.php">
      <input type="hidden" name="followed_id" value="<?= $profile_user_id ?>">
      <button class="follow-btn <?= $is_following ? 'unfollow' : '' ?>">
        <?= $is_following ? 'フォロー解除' : 'フォローする' ?>
      </button>
    </form>
  <?php else: ?>
    <p><a href="mypage_edit.php">プロフィールを編集する</a></p>
  <?php endif; ?>

  <p><a href="index.php">← ホームに戻る</a></p>
</div>

<script>
  function toggleList(listId) {
    const list = document.getElementById(listId);
    list.classList.toggle('active');
  }
</script>

</body>
</html>