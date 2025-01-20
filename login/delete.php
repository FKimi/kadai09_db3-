// delete.php - 削除処理
<?php
session_start();

// CSRFチェック（POSTメソッドの場合）
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        !isset($_SESSION['csrf_token']) ||
        !isset($_POST['csrf_token']) ||
        $_SESSION['csrf_token'] !== $_POST['csrf_token']
    ) {
        exit('不正なリクエストです。');
    }
}

// DB接続（既存の接続処理を流用）
try {
    $db_name = 'fuuu_profile_table';
    $db_host = 'mysql3104.db.sakura.ne.jp';
    $db_id   = 'fuuu_profile_table';
    $db_pw   = '134097Fu';
    $dsn = "mysql:dbname={$db_name};charset=utf8mb4;host={$db_host}";
    $pdo = new PDO($dsn, $db_id, $db_pw);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    exit('データベース接続エラー：' . $e->getMessage());
}

// IDの受け取り
$id = $_POST['id'] ?? exit('IDが指定されていません。');

// 削除SQL実行
$sql = 'DELETE FROM profile_table WHERE id = :id';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);

try {
    $status = $stmt->execute();
    header('Location: read.php');
} catch (PDOException $e) {
    exit('エラー：' . $e->getMessage());
}
Last edited 15 minutes ago