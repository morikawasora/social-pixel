<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['followed_id'])) {
  header('Location: index.php');
  exit;
}

$follower_id = $_SESSION['user_id'];
$followed_id = (int)$_POST['followed_id'];

if ($follower_id === $followed_id) {
  // 自分自身はフォローできない
  header("Location: mypage.php?id=$followed_id");
  exit;
}

// フォロー状態の確認
$stmt = $pdo->prepare("SELECT * FROM follows WHERE follower_id = ? AND followed_id = ?");
$stmt->execute([$follower_id, $followed_id]);
$existing = $stmt->fetch();

if ($existing) {
  // フォロー解除
  $stmt = $pdo->prepare("DELETE FROM follows WHERE follower_id = ? AND followed_id = ?");
  $stmt->execute([$follower_id, $followed_id]);
} else {
  // フォロー追加
  $stmt = $pdo->prepare("INSERT INTO follows (follower_id, followed_id) VALUES (?, ?)");
  $stmt->execute([$follower_id, $followed_id]);
}

// リダイレクト
header("Location: mypage.php?id=$followed_id");
exit;
