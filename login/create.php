<?php
session_start();
include('../functions.php');  // 一つ上のディレクトリにあるfunctions.phpを参照
// JSON形式のデータ受け取りを追加（Google認証用）
$input = json_decode(file_get_contents('php://input'), true);
// 1.POSTデータ確認。以下の条件に合致する場合は以降の処理を中止してエラー画面を表示する。!は反対の意味になる。

if ($input) {
  // Google認証からのデータを処理
  $email = $input['email'];
  $name = $input['name'];
  $google_id = $input['google_id'];
  $password = null; // パスワードは不要（Google認証のため）
} else {
  // 既存のフォームからのデータ処理
  if (
      !isset($_POST['email']) || $_POST['email'] === '' ||
      !isset($_POST['pw']) || $_POST['pw'] === ''
  ) {
      exit('データがありません');
  }
  // 2.データの受け取り
  $email = $_POST['email'];
  $password = $_POST['pw'];
}

// 3.データベースへの接続準備.各種項目設定。データベース、ユーザー名、PWの確認
$pdo = connect_to_db();
// メールアドレスの重複チェック
$sql = 'SELECT COUNT(*) FROM users_table WHERE email = :email AND deleted_at IS NULL';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':email', $email, PDO::PARAM_STR);

// 4.データベースへの接続
try {
  $stmt->execute();
  if ($stmt->fetchColumn() > 0) {
      if ($input) {
          // Google認証の場合は既存ユーザーとしてログイン処理
          $sql = 'SELECT * FROM users_table WHERE email = :email';
          $stmt = $pdo->prepare($sql);
          $stmt->bindValue(':email', $email, PDO::PARAM_STR);
          $stmt->execute();
          $user = $stmt->fetch(PDO::FETCH_ASSOC);

          $_SESSION['session_id'] = session_id();
          $_SESSION['user_id'] = $user['id'];
          $_SESSION['email'] = $user['email'];
          
          echo json_encode(["status" => "success"]);
          exit();
      } else {
          // 通常登録の場合はエラーメッセージを表示
          echo "<script>
              alert('このメールアドレスは既に登録されています');
              location.href='signup.php';
          </script>";
          exit();
      }
  }

    // 新規ユーザー登録用SQL
    if ($input) {
      // Google認証の場合
      $sql = 'INSERT INTO users_table (email, name, google_id, created_at) VALUES (:email, :name, :google_id, NOW())';
      $stmt = $pdo->prepare($sql);
      $stmt->bindValue(':email', $email, PDO::PARAM_STR);
      $stmt->bindValue(':name', $name, PDO::PARAM_STR);
      $stmt->bindValue(':google_id', $google_id, PDO::PARAM_STR);
  } else {
      // 通常登録の場合
      $sql = 'INSERT INTO users_table (email, password, created_at) VALUES (:email, :password, NOW())';
      $stmt = $pdo->prepare($sql);
      $stmt->bindValue(':email', $email, PDO::PARAM_STR);
      $stmt->bindValue(':password', $password, PDO::PARAM_STR);
  }

  $status = $stmt->execute();

  // セッションの作成
  $_SESSION['session_id'] = session_id();
  $_SESSION['user_id'] = $pdo->lastInsertId();
  $_SESSION['email'] = $email;

  if ($input) {
    echo json_encode(["status" => "success"]);
} else {
    header('Location: ../mypage/mypage.php');  // パスを修正
}
  exit();

} catch (PDOException $e) {
  echo json_encode(["error" => "{$e->getMessage()}"]);
  exit();
}
?>
