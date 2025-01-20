<?php
// ===================================================================
// login_process.php - ログイン処理の実装
// 目的：通常ログインとGoogle認証、両方のログイン処理を実装
// ===================================================================
session_start();
include('../functions.php');  // 一つ上の階層のfunctions.phpを参照するように修正

// JSONリクエストの確認（Google認証からのリクエストの場合）
if ($_SERVER["CONTENT_TYPE"] === "application/json") {
    // Google認証からのデータを取得
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    $email = $data['email'];
    $name = $data['name'];
    $google_id = $data['google_id'];
    
    $pdo = connect_to_db();
    
    // Google IDですでに登録されているかチェック
    $sql = 'SELECT * FROM users_table WHERE google_id = :google_id AND deleted_at IS NULL';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':google_id', $google_id, PDO::PARAM_STR);

    try {
        $status = $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // 既存ユーザーの場合：セッション作成とログイン処理
            $_SESSION = array();
            $_SESSION['session_id'] = session_id();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['name'] = $user['name'];
            
            echo json_encode(["status" => "success"]);
            exit();

// 1. POSTデータの取得
if (
    !isset($_POST['email']) || $_POST['email'] === '' ||
    !isset($_POST['pw']) || $_POST['pw'] === ''
) {
    exit('データがありません');
}

} else {
    // 新規ユーザー登録の場合
    $sql = 'INSERT INTO users_table (email, name, google_id, created_at) VALUES (:email, :name, :google_id, now())';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    $stmt->bindValue(':name', $name, PDO::PARAM_STR);
    $stmt->bindValue(':google_id', $google_id, PDO::PARAM_STR);
    
    try {
        $status = $stmt->execute();
        $_SESSION = array();
        $_SESSION['session_id'] = session_id();
        $_SESSION['user_id'] = $pdo->lastInsertId();
        $_SESSION['email'] = $email;
        $_SESSION['name'] = $name;
        
        echo json_encode(["status" => "success"]);
        exit();
    } catch (PDOException $e) {
        echo json_encode(["error" => $e->getMessage()]);
        exit();
    }
}
} catch (PDOException $e) {
echo json_encode(["error" => $e->getMessage()]);
exit();
}
} else {
// 通常のログインフォームからのPOSTリクエストの処理
// 入力値のバリデーション
if (
!isset($_POST['email']) || $_POST['email'] === '' ||
!isset($_POST['pw']) || $_POST['pw'] === ''
) {
// バリデーションエラー時はログインページに戻す
echo "<script>
    alert('メールアドレスとパスワードを入力してください');
    location.href='login.php';
</script>";
exit();
}

    // POSTデータの取得
    $email = $_POST['email'];
    $password = $_POST['pw'];  // 本番環境ではハッシュ化必須

    $pdo = connect_to_db();

    // ユーザー情報の取得（論理削除されていないユーザーのみ）
    $sql = 'SELECT * FROM users_table WHERE email = :email AND deleted_at IS NULL';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);

    try {
        $status = $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // ユーザー認証の確認
        if (!$user || $password !== $user['password']) {  // 本番環境ではpassword_verify()を使用
            echo "<script>
                alert('メールアドレスまたはパスワードが間違っています');
                location.href='login.php';
            </script>";
            exit();
        }

        // ログイン成功時のセッション作成
        $_SESSION = array();
        $_SESSION['session_id'] = session_id();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['name'] = $user['name'];

        // マイページへリダイレクト
        header("Location:../mypage/mypage.php");
        exit();

    } catch (PDOException $e) {
        // データベースエラー時の処理
        echo "<script>
            alert('エラーが発生しました');
            location.href='login.php';
        </script>";
        exit();
    }
}
?>

<?php
// ===================================================================
// 【セキュリティ対策メモ】
// 1. セッション管理
//    - セッションIDの再生成（セッションハイジャック対策）
//    - セッション変数の適切な管理
//
// 2. パスワードセキュリティ
//    - 本番環境ではパスワードのハッシュ化が必須
//    - password_hash()とpassword_verify()の使用推奨
//
// 3. SQLインジェクション対策
//    - プリペアドステートメントの使用
//    - バインド変数による安全なクエリ実行
//
// 4. XSS対策
//    - 出力時のエスケープ処理
//    - Content-Security-Policyの設定
//
// 【改善ポイント】
// 1. ログイン試行回数の制限実装
// 2. パスワードリセット機能の追加
// 3. 2要素認証の実装
// 4. ログイン情報の保持機能
// ===================================================================
?>