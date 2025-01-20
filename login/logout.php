<?php
// logout.php
session_start();

// セッション変数を全て削除
$_SESSION = array();

// セッションを破棄
session_destroy();

// ログインページへ移動
header("Location:login.php");
exit();
?>