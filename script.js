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
// script.js

// ページ読み込み時にダークモードを適用
document.addEventListener('DOMContentLoaded', () => {
  const darkMode = localStorage.getItem('darkMode');
  if (darkMode === 'enabled') {
    document.body.classList.add('dark-mode');
  }

  const toggleBtn = document.getElementById('toggle-dark-mode');
  if (toggleBtn) {
    toggleBtn.addEventListener('click', () => {
      document.body.classList.toggle('dark-mode');
      if (document.body.classList.contains('dark-mode')) {
        localStorage.setItem('darkMode', 'enabled');
      } else {
        localStorage.setItem('darkMode', 'disabled');
      }
    });
  }

  // サイドバー開閉
  const menuToggle = document.getElementById('menuToggle');
  const sidebar = document.querySelector('.sidebar');
  const mainContent = document.querySelector('.main');
  if (menuToggle && sidebar && mainContent) {
    menuToggle.addEventListener('click', () => {
      sidebar.classList.toggle('open');
      mainContent.classList.toggle('shifted');
    });
  }

  // 現在ページのactiveクラス付与
  const links = document.querySelectorAll('.sidebar a');
  const current = location.pathname.split("/").pop();
  links.forEach(link => {
    if (link.getAttribute('href') === current) {
      link.classList.add('active');
    }
  });

  // 投稿処理（投稿フォームがあるページだけ）
  const form = document.getElementById('postForm');
  const textarea = document.getElementById('postContent');
  const timeline = document.getElementById('timeline');
  if (form && textarea && timeline) {
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      const text = textarea.value.trim();
      if (text === '') return;
      // 投稿作成処理...
      // （省略）
    });
  }

  // ダークモード切替ボタンイベント登録
  const themeButton = document.getElementById('toggle-dark-mode');
  if (themeButton) {
    themeButton.addEventListener('click', toggleTheme);
  }
});
