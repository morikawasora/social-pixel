<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

// ユーザー情報取得（JavaScript用）
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

    /* メディア表示 */
    img.post-media {
      max-width: 100%;
      height: auto;
      max-height: 300px;
      object-fit: contain;
      margin-top: 8px;
      border-radius: 8px;
      display: block;
    }
    video.post-media {
      max-width: 100%;
      height: auto;
      max-height: 300px;
      object-fit: contain;
      border-radius: 8px;
      display: block;
      margin-top: 8px;
    }

    /* コメント用 */
    .comment-form {
      display: flex;
      margin-top: 5px;
    }
    .comment-input {
      flex: 1;
      padding: 4px;
      font-size: 0.9em;
    }
    .comment-form button {
      padding: 4px 8px;
      margin-left: 4px;
    }
    .comments {
      margin-top: 5px;
    }
    .comment-user-icon {
      width: 24px;
      height: 24px;
      border-radius: 50%;
      object-fit: cover;
      margin-right: 6px;
    }

    /* 通知エリアのスタイル */
    #notification-area {
      position: fixed;
      top: 10px;
      right: 10px;
      z-index: 1000;
      font-family: sans-serif;
    }
    #notification-button {
      background: none;
      border: none;
      cursor: pointer;
      font-size: 24px;
      position: relative;
    }
    #notification-count {
      background: red;
      color: white;
      font-size: 12px;
      font-weight: bold;
      padding: 2px 6px;
      border-radius: 50%;
      position: absolute;
      top: -6px;
      right: -10px;
      min-width: 18px;
      text-align: center;
      line-height: 1;
    }
    #notification-list {
      display: none;
      background: #fff;
      border: 1px solid #ccc;
      max-height: 300px;
      overflow-y: auto;
      width: 300px;
      position: absolute;
      right: 0;
      top: 30px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }
    #notification-list div {
      padding: 8px;
      border-bottom: 1px solid #ddd;
      cursor: pointer;
    }
    #notification-list div:hover {
      background-color: #f0f0f0;
    }
  </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<!-- 通知アイコンエリア -->
<div id="notification-area">
  <button id="notification-button">🔔 <span id="notification-count">0</span></button>
  <div id="notification-list">
    <!-- 通知内容がここに入る -->
  </div>
</div>

<!-- メイン -->
<div class="main" id="mainContent">
  <h1>投稿</h1>
  <form id="postForm" enctype="multipart/form-data">
    <textarea id="postContent" rows="3" placeholder="いまなにしてる？"></textarea>
    <br>
    <input type="file" id="postMedia" name="media_files[]" accept="image/*,video/*" multiple>
    <br>
    <button type="submit" class="post-btn">投稿</button>
  </form>

  <div id="timeline"></div>
</div>

<script>
  const postForm = document.getElementById('postForm');
  const postContent = document.getElementById('postContent');
  const postMedia = document.getElementById('postMedia');
  const timeline = document.getElementById('timeline');

  // PHPの値をJavaScriptに渡す
  const username = <?= json_encode($username) ?>;
  const userIcon = <?= json_encode($icon) ?>;

  document.addEventListener('DOMContentLoaded', () => {
    fetchPosts();
    fetchNotifications();
    setInterval(fetchNotifications, 60000);
  });

  postForm.addEventListener('submit', (e) => {
    e.preventDefault();

    const content = postContent.value.trim();
    const hasMedia = postMedia.files.length > 0;

    // 本文もメディアもない場合はエラー
    if (!content && !hasMedia) {
      alert("投稿内容または画像・動画を入力してください。");
      return;
    }

    const formData = new FormData();
    formData.append('content', content); // 修正: 'NULL' ではなく 'content'

    if (hasMedia) {
      for (let i = 0; i < postMedia.files.length; i++) {
        formData.append('media_files[]', postMedia.files[i]);
      }
    }

    fetch('post_create.php', {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        postContent.value = '';
        postMedia.value = '';
        fetchPosts();
      } else {
        alert(data.message || '投稿に失敗しました');
      }
    })
    .catch(error => {
      console.error('投稿エラー:', error);
      alert('投稿中にエラーが発生しました');
    });
  });  function fetchPosts() {
    fetch('get_posts.php')
      .then(res => res.json())
      .then(posts => {
        timeline.innerHTML = '';
        posts.forEach(post => {
          const liked = post.liked_by_user === 1;
          let mediaHTML = '';
          if (post.media && post.media.length > 0) {
            post.media.forEach(m => {
              if (m.media_type === 'image') {
                mediaHTML += `<img src="${escapeHTML(m.file_path)}" alt="画像" class="post-media">`;
              } else if (m.media_type === 'video') {
                mediaHTML += `
                  <video controls class="post-media">
                    <source src="${escapeHTML(m.file_path)}" type="video/mp4">
                    動画を再生できません
                  </video>
                `;
              }
            });
          }

          const postElement = document.createElement('div');
          postElement.className = 'post';
          postElement.innerHTML = `
            <div class="post-header">
              <a href="mypage.php?id=${post.user_id}">
                <img src="${post.icon ? 'uploads/' + post.icon : 'uploads/default.png'}" class="post-icon" alt="アイコン">
              </a>
              <span class="post-user">${escapeHTML(post.username)}</span>
            </div>
            <div class="post-text">${escapeHTML(post.content)}</div>
            ${mediaHTML}
            <div class="post-time">${new Date(post.created_at).toLocaleString()}</div>
            <button class="like-btn" data-post-id="${post.id}">
              ${liked ? '💖' : '♡'} いいね (<span class="like-count">${post.like_count ?? 0}</span>)
            </button>
            <button class="comment-toggle-btn" data-post-id="${post.id}" style="margin-left:10px;">コメント一覧</button>

            <!-- コメントエリアは非表示 -->
            <div class="comments" id="comments-${post.id}" style="margin-top:10px; display:none;"></div>

            <form class="comment-form" data-post-id="${post.id}" style="margin-top:5px; display:none;">
              <input type="text" class="comment-input" placeholder="コメントを入力..." required>
              <button type="submit">送信</button>
            </form>
          `;

          // likeボタン処理（略。既存のコードを流用してください）
          const likeBtn = postElement.querySelector('.like-btn');
          const likeCount = postElement.querySelector('.like-count');
          likeBtn.addEventListener('click', () => {
            const postId = likeBtn.dataset.postId;

            fetch('like_toggle.php', {
              method: 'POST',
              headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
              body: `post_id=${encodeURIComponent(postId)}`
            })
            .then(res => res.json())
            .then(data => {
              if (data.success) {
                let count = parseInt(likeCount.textContent);
                if (data.liked) {
                  count++;
                  likeBtn.innerHTML = `💖 いいね (<span class="like-count">${count}</span>)`;
                } else {
                  count = Math.max(0, count - 1);
                  likeBtn.innerHTML = `♡ いいね (<span class="like-count">${count}</span>)`;
                }
              }
            });
          });

          // コメント一覧表示ボタンとフォームのトグル（略。既存コードのまま）
          const commentToggleBtn = postElement.querySelector('.comment-toggle-btn');
          const commentsContainer = postElement.querySelector('.comments');
          const commentForm = postElement.querySelector('.comment-form');

          commentToggleBtn.addEventListener('click', () => {
            if (commentsContainer.style.display === 'none' || commentsContainer.style.display === '') {
              loadComments(post.id);
              commentsContainer.style.display = 'block';
              commentForm.style.display = 'flex';
            } else {
              commentsContainer.style.display = 'none';
              commentForm.style.display = 'none';
            }
          });

          // コメント送信（既存コードと同様）
          commentForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const postId = commentForm.dataset.postId;
            const input = commentForm.querySelector('.comment-input');
            const content = input.value.trim();
            if (!content) return;

            const formData = new FormData();
            formData.append('post_id', postId);
            formData.append('content', content);

            fetch('post_comment.php', {
              method: 'POST',
              body: formData
            })
            .then(res => res.json())
            .then(data => {
              if (data.success) {
                input.value = '';
                loadComments(postId);
              }
            });
          });

          timeline.appendChild(postElement);
        });
      });
  }

  // コメント読み込み関数（既存コード）
  function loadComments(postId) {
    const commentsContainer = document.getElementById(`comments-${postId}`);
    fetch(`get_comments.php?post_id=${postId}`)
      .then(res => res.json())
      .then(comments => {
        commentsContainer.innerHTML = '';
        comments.forEach(c => {
          const iconPath = c.icon ? 'uploads/' + c.icon : 'uploads/default.png';
          const cDiv = document.createElement('div');
          cDiv.innerHTML = `
            <div style="display:flex; align-items:center; margin-bottom:4px;">
              <a href="mypage.php?id=${c.user_id}">
                <img src="${iconPath}" alt="icon" class="comment-user-icon">
              </a>
              <strong style="margin-right:6px;">${escapeHTML(c.username)}</strong>
              <span style="font-size: 0.9em;">${escapeHTML(c.content)}</span>
            </div>
            <small style="margin-left:30px; color:#888;">${new Date(c.created_at).toLocaleString()}</small>
          `;
          commentsContainer.appendChild(cDiv);
        });
      })
      .catch(e => {
        console.error('コメント読み込み失敗', e);
      });
  }

  // 通知処理（既存コード）
  const notificationButton = document.getElementById('notification-button');
  const notificationList = document.getElementById('notification-list');
  const notificationCount = document.getElementById('notification-count');

  notificationButton.addEventListener('click', () => {
    if (notificationList.style.display === 'none' || notificationList.style.display === '') {
      notificationList.style.display = 'block';
      markAllAsRead();
    } else {
      notificationList.style.display = 'none';
    }
  });

  function fetchNotifications() {
    fetch('notifications.php')
      .then(res => res.json())
      .then(notifications => {
        notificationCount.textContent = notifications.length;
        notificationList.innerHTML = '';

        if (notifications.length === 0) {
          notificationList.innerHTML = '<p style="padding:10px;">通知はありません</p>';
          return;
        }

        notifications.forEach(n => {
          const item = document.createElement('div');
          item.innerHTML = `
            <strong>${escapeHTML(n.from_username)}</strong> さんがあなたの投稿にいいねしました。<br>
            <small>${new Date(n.created_at).toLocaleString()}</small>
          `;
          item.addEventListener('click', () => {
            window.location.href = 'post_detail.php?id=' + encodeURIComponent(n.post_id);
          });
          notificationList.appendChild(item);
        });
      })
      .catch(() => {
        notificationCount.textContent = '0';
        notificationList.innerHTML = '<p style="padding:10px;">通知取得エラー</p>';
      });
  }

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

  function markAllAsRead() {
    fetch('notifications_mark_read.php', { method: 'POST' })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          notificationCount.textContent = '0';
        }
      });
  }
</script>
</body>
</html>
