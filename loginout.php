<?php
session_start();
$_SESSION = array(); // セッション変数を空にする
session_destroy();   // セッションを破棄する
header("Location: user_login.php"); // ログインページにリダイレクト
exit;
?>