<?php
try {
    $dbn = 'mysql:dbname=balubo_db;charset=utf8mb4;port=3306;host=localhost';
    $user = 'root';
    $pwd = '';

    // PDOインスタンスを生成
    $conn = new PDO($dbn, $user, $pwd);

    // エラーモードを例外に設定
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    echo json_encode(["db error" => "{$e->getMessage()}"]);
    exit();
}
?>