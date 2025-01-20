<?php
// edit.php - 更新画面
session_start();

// DB接続処理（既存のコードを流用）
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
$id = $_GET['id'] ?? exit('IDが指定されていません。');

// データの取得
$sql = 'SELECT * FROM profile_table WHERE id = :id';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);

try {
    $status = $stmt->execute();
    $record = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    exit('エラー：' . $e->getMessage());
}

// CSRFトークンの生成
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>balubo - プロフィール編集</title>
    <link rel="stylesheet" href="signup.css">
</head>
<body>
    <header>
        <a href="/" class="logo">balubo</a>
    </header>
    <main>
        <form action="update.php" method="POST" class="edit-container">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="hidden" name="id" value="<?= $record['id'] ?>">
            <div class="form-group">
                <label for="email">メールアドレス:</label>
                <input type="email" name="email" value="<?= htmlspecialchars($record['email']) ?>" required>
            </div>
            <div class="form-group">
                <label for="pw">新しいパスワード（変更する場合のみ）:</label>
                <input type="password" name="pw" placeholder="変更する場合のみ入力">
                <span class="password-hint">※半角英数含む6-18文字</span>
            </div>
            <button type="submit" class="submit-btn">更新</button>
        </form>
        <div class="button-group">
            <a href="read.php" class="cancel-btn">キャンセル</a>
        </div>
    </main>
</body>
</html>