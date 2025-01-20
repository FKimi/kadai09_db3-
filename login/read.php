<?php

// 1.データベースの接続情報を設定（場所、名前、文字コード、ポート番号）
$dbn ='mysql:dbname=balubo_db;charset=utf8mb4;port=3306;host=localhost';
$user = 'root';
$pwd = '';

// 1.2 PDOを使って実際に接続を試みる。接続に失敗した場合はエラーメッセージを表示して処理を終了
try {
  $db_name = 'fuuu_profile_table';  // データベース名
  $db_host = 'mysql3104.db.sakura.ne.jp';  
  $db_id   = 'fuuu_profile_table';      // データベース名と同じ
  $db_pw   = '134097Fu';      // 設定したデータベースパスワード
  
  $pdo = new PDO(
      'mysql:dbname=' . $db_name . ';charset=utf8;host=' . $db_host,
      $db_id,
      $db_pw
  );
} catch (PDOException $e) {
  exit('DB Connection Error:' . $e->getMessage());
}

// 2.データの取得準備（id, name, email, dateofbirth,username,occupation,created_at, updated_at）
// 2.1 SELECT文でデータを取得するよう指定
$sql = 'SELECT * FROM profile_table';
// 2.2 SQLを実行する準備（prepare）
$stmt = $pdo->prepare($sql);

// 3.データの取得実行 
try {
  $status = $stmt->execute();
} catch (PDOException $e) {
  echo json_encode(["sql error" => "{$e->getMessage()}"]);
  exit();
}

// SQL実行の処理
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
$output = "";
foreach ($result as $record) {
  $output .= "
    <tr>
      <td>{$record["email"]}</td>  
      <td>{$record["pw"]}</td>   
    </tr>
  ";
}
// <td>{$record["name"]}</td>
// <td>{$record["dateofbirth"]}</td>
// <td>{$record["username"]}</td>
// <td>{$record["occupation"]}</td>

?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>プロフィールリスト（登録画面） 
    ※一旦登録画面に飛ばしていますが、遷移は調整予定</title>
  <link rel="stylesheet" href="style.css"> 
</head>

<body>
  <header>
    <h1>balubo</h1>
  </header>
  <main>
  <fieldset>
    <legend>プロフィールリスト（登録画面）</legend>
    <table>
      <thead>
        <tr>
          <th>メールアドレス</th>
          <th>パスワード</th>
        </tr>
      </thead>
      <tbody>
        <!-- ここにデータが入る -->
        <?= $output ?>
      </tbody>
    </table>
  </fieldset>
  </main>
</body>

</html>

<div>
    <a href="input.php">入力画面</a>
    </div>