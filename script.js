// サイドバー開閉処理
const menuToggle = document.getElementById('menuToggle');
const sidebar = document.querySelector('.sidebar');
const mainContent = document.querySelector('.main');

menuToggle.addEventListener('click', () => {
  sidebar.classList.toggle('open');
  mainContent.classList.toggle('shifted');
});

// 現在ページに active クラスを追加
const links = document.querySelectorAll('.sidebar a');
const current = location.pathname.split("/").pop();

links.forEach(link => {
  const href = link.getAttribute('href');
  if (href === current) {
    link.classList.add('active');
  }
});

// 投稿処理
const form = document.getElementById('postForm');
const textarea = document.getElementById('postContent');
const timeline = document.getElementById('timeline');

form.addEventListener('submit', function(e) {
  e.preventDefault();
  const text = textarea.value.trim();
  if (text === '') return;

  const post = document.createElement('div');
  post.className = 'post';

  const content = document.createElement('p');
  content.textContent = text;

  const timestamp = document.createElement('div');
  timestamp.className = 'post-time';
  const now = new Date();
  timestamp.textContent = now.toLocaleString();

  const likeSection = document.createElement('div');
  likeSection.className = 'like-section';

  const likeButton = document.createElement('button');
  likeButton.className = 'like-button';
  likeButton.textContent = '❤️ いいね！';

  const likeCount = document.createElement('span');
  likeCount.className = 'like-count';
  likeCount.textContent = '0';

  likeButton.addEventListener('click', function() {
    likeCount.textContent = parseInt(likeCount.textContent) + 1;
  });

  likeSection.appendChild(likeButton);
  likeSection.appendChild(likeCount);

  post.appendChild(content);
  post.appendChild(timestamp);
  post.appendChild(likeSection);

  timeline.prepend(post);
  textarea.value = '';
  // ダークモード状態の保存・復元
function applyTheme() {
  const mode = localStorage.getItem('theme');
  if (mode === 'dark') {
    document.body.classList.add('dark-mode');
  }
}

function toggleTheme() {
  document.body.classList.toggle('dark-mode');
  const isDark = document.body.classList.contains('dark-mode');
  localStorage.setItem('theme', isDark ? 'dark' : 'light');
}

// 設定ページだけでボタンがある場合にイベント追加
document.addEventListener('DOMContentLoaded', () => {
  applyTheme();

  const themeButton = document.getElementById('toggleTheme');
  if (themeButton) {
    themeButton.addEventListener('click', toggleTheme);
  }
});

});
