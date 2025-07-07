<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

// ユーザー情報取得（JavaScriptに渡すための処理）
$stmt = $pdo->prepare("SELECT username, icon FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$username = htmlspecialchars($user['username']);
$icon = !empty($user['icon']) ? 'uploads/' . htmlspecialchars($user['icon']) : 'uploads/default.png';
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>S&P - ホーム</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .post-header {
      display: flex;
      align-items: center;
      margin-bottom: 5px;
    }
    .post-icon {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      object-fit: cover;
      margin-right: 10px;
    }
    .post {
      border: 1px solid #ccc;
      background: #fff;
      padding: 10px;
      margin-top: 10px;
      border-radius: 8px;
    }
    .post-time {
      font-size: 0.8em;
      color: #666;
    }
    .like-btn {
      background: none;
      border: none;
      cursor: pointer;
      color: #e0245e;
      font-weight: bold;
    }
  </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<!-- メイン -->
<div class="main" id="mainContent">
  <h1>投稿</h1>
  <form id="postForm">
    <textarea id="postContent" rows="3" placeholder="いまなにしてる？" required></textarea>
    <br>
    <button type="submit" class="post-btn">投稿</button>
  </form>

  <div id="timeline"></div>
</div>

<script>
  const postForm = document.getElementById('postForm');
  const postContent = document.getElementById('postContent');
  const timeline = document.getElementById('timeline');

  // PHPからのデータをJSに渡す
  const username = <?= json_encode($username) ?>;
  const userIcon = <?= json_encode($icon) ?>;

  // 投稿取得
  document.addEventListener('DOMContentLoaded', fetchPosts);

  function fetchPosts() {
    fetch('get_posts.php')
      .then(res => res.json())
      .then(posts => {
        timeline.innerHTML = '';
        posts.forEach(post => {
          const postElement = document.createElement('div');
          postElement.className = 'post';
          postElement.innerHTML = `
            <div class="post-header">
              <img src="${post.icon ? 'uploads/' + post.icon : 'uploads/default.png'}" class="post-icon" alt="アイコン">
              <span class="post-user">${escapeHTML(post.username)}</span>
            </div>
            <div class="post-text">${escapeHTML(post.content)}</div>
            <div class="post-time">${new Date(post.created_at).toLocaleString()}</div>
            <button class="like-btn">♡ いいね (<span class="like-count">0</span>)</button>
          `;

          const likeBtn = postElement.querySelector('.like-btn');
          const likeCount = postElement.querySelector('.like-count');
          let liked = false;

          likeBtn.addEventListener('click', () => {
            liked = !liked;
            const count = parseInt(likeCount.textContent);
            likeCount.textContent = liked ? count + 1 : count - 1;
            likeBtn.textContent = liked ? `💖 いいね (${likeCount.textContent})` : `♡ いいね (${likeCount.textContent})`;
          });

          timeline.appendChild(postElement);
        });
      });
  }

  // 投稿送信処理
  postForm.addEventListener('submit', (e) => {
    e.preventDefault();
    const content = postContent.value.trim();
    if (content === '') return;

    const formData = new FormData();
    formData.append('content', content);

    fetch('post_create.php', {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        postContent.value = '';
        fetchPosts();
      }
    });
  });

  function escapeHTML(str) {
    return str.replace(/[&<>"']/g, (match) => {
      const escapeMap = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;',
      };
      return escapeMap[match];
    });
  }
</script>
</body>
</html>
