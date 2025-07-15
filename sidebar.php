<?php
if (!isset($_SESSION)) session_start();
require_once 'db_connect.php';

$sidebarUser = ['username' => 'ゲスト', 'icon' => 'default.png'];

if (isset($_SESSION['user_id'])) {
  $stmt = $pdo->prepare("SELECT username, icon FROM users WHERE id = ?");
  $stmt->execute([$_SESSION['user_id']]);
  $result = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($result) {
    $sidebarUser = $result;
  }
}

$iconPath = !empty($sidebarUser['icon']) ? 'uploads/' . htmlspecialchars($sidebarUser['icon']) : 'uploads/default.png';
$username = htmlspecialchars($sidebarUser['username']);
?>


<style>
  * {
    box-sizing: border-box;
  }

  body {
    margin: 0;
    overflow-x: hidden;
  }

  .sidebar {
    position: fixed;
    top: 0; left: -200px;
    width: 200px; height: 100%;
    background: #333;
    color: #fff;
    padding: 20px;
    transition: left 0.3s ease;
    z-index: 1000;
  }

  .sidebar.open {
    left: 0;
  }

  .sidebar-user {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
  }

  .sidebar-icon {
    width: 60px; height: 60px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #fff;
    margin-right: 10px;
  }

  .sidebar-username {
    font-weight: bold;
  }

  .sidebar a {
    display: block;
    color: white;
    margin: 15px 0;
    text-decoration: none;
  }

  .menu-toggle {
    font-size: 24px; cursor: pointer;
    background: none; border: none;
    position: fixed; top: 10px; left: 10px; z-index: 1001;
    color: #333;
  }

  .main.shifted {
    margin-left: 200px;
    transition: margin-left 0.3s ease;
  }
</style>

<button class="menu-toggle" id="menuToggle">☰</button>

<div class="sidebar" id="sidebar">
  <h2>S&P</h2>
  <div class="sidebar-user">
  <a href="mypage.php">
    <img src="<?= $iconPath ?>" alt="ユーザーアイコン" class="sidebar-icon">
  </a>
    <div class="sidebar-username"><?= $username ?></div>
  </div>
  <a href="index.php">🏠 ホーム</a>
  <a href="mypage.php">👤 マイページ</a>
  <a href="setting.php">⚙ 設定</a>
</div>

<script>
  const menuToggle = document.getElementById('menuToggle');
  const sidebar = document.getElementById('sidebar');
  const mainContent = document.getElementById('mainContent');

menuToggle.addEventListener('click', () => {
  sidebar.classList.toggle('open');
  if(mainContent) mainContent.classList.toggle('shifted');
});

// サイドバー外クリックで閉じる処理
document.addEventListener('click', (e) => {
  // sidebar要素とmenuToggleボタンがクリック対象でなければ閉じる
  if (!sidebar.contains(e.target) && e.target !== menuToggle) {
    if (sidebar.classList.contains('open')) {
      sidebar.classList.remove('open');
      if(mainContent) mainContent.classList.remove('shifted');
    }
  }
});
</script>
