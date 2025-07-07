<?php
session_start();
session_destroy(); // セッション情報を削除
header("Location: login.php"); // ログイン画面にリダイレクト
exit;
