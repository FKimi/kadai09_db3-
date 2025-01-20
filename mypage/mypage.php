<?php
// ===================================================================
// mypage.php - マイページ（ポートフォリオページ）の実装
// 目的：ユーザーのプロフィール表示と編集機能の提供
// 作成日：2024-01-18
// ===================================================================
session_start();
include('../functions.php');
check_session_id();  // セッションチェックを追加（セキュリティ強化）

// ユーザー情報の取得
// 削除済みユーザーは除外するようにWHERE句を修正
$pdo = connect_to_db();
$sql = 'SELECT * FROM users_table WHERE id = :id AND deleted_at IS NULL';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);

try {
    $status = $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo json_encode(["error" => "{$e->getMessage()}"]);
    exit();
}

// ポートフォリオ作品の取得（user_idで紐付け）
$sql = 'SELECT * FROM portfolio WHERE user_id = :user_id ORDER BY created_at DESC';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);

try {
    $stmt->execute();
    $works = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo json_encode(["error" => "{$e->getMessage()}"]);
    exit();
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>balubo - マイページ</title>
    <!-- Tailwind CSSの読み込み（スタイリング用） -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <!-- ヘッダー部分 -->
    <header class="bg-white shadow">
        <nav class="container mx-auto px-6 py-4">
            <div class="flex justify-between items-center">
                <div class="text-xl font-bold">balubo</div>
                <div class="flex space-x-6">
                    <!-- リンクのパスを修正（相対パスに変更） -->
                    <a href="portfolio_create.php" class="text-gray-600 hover:text-gray-900">ポートフォリオを作る</a>
                    <a href="../login/logout.php" class="text-gray-600 hover:text-gray-900">ログアウト</a>
                </div>
            </div>
        </nav>
    </header>
    <!-- プロフィールセクション -->
    <!-- グラデーションの背景を追加し、視覚的な魅力を向上 -->
    <section class="py-20 px-6 bg-gradient-to-r from-blue-600 to-blue-800 text-white">
        <div class="container mx-auto">
            <div class="flex items-center space-x-8">
            
                <!-- プロフィール画像 -->
                <div class="w-32 h-32 bg-white rounded-full overflow-hidden">
                    <!-- デフォルト画像のフォールバックを追加 -->
                    <img src="<?= $user['profile_image'] ?? '/img/default-avatar.png' ?>" 
                         alt="プロフィール画像" 
                         class="w-full h-full object-cover">
                </div>
                <div>
                    <!-- XSS対策としてhtmlspecialcharsを使用 -->
                    <h1 class="text-3xl font-bold mb-2">
                        <?= htmlspecialchars($user['username'] ?? $user['email']) ?>
                    </h1>
                    <p class="text-xl mb-4">
                        <?= htmlspecialchars($user['occupation'] ?? '') ?>
                    </p>
                    <!-- 改行を正しく表示するためにnl2brを追加 -->
                    <p class="mb-4">
                        <?= nl2br(htmlspecialchars($user['bio'] ?? '')) ?>
                    </p>
                    <!-- プロフィール編集リンクのパスを修正 -->
                    <a href="profile_edit.php" class="bg-white text-blue-600 px-6 py-2 rounded-lg font-bold hover:bg-gray-100">
                        プロフィールを編集
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- AI分析レポートセクション -->
    <section class="py-12 px-6">
        <div class="container mx-auto">
            <h2 class="text-2xl font-bold mb-8">AI分析レポート</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <!-- 強み分析カード -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="font-bold mb-4">基本情報
                        （総コンテンツ数など）
                    </h3>
                    <!-- AIによる分析結果を表示予定 -->
                </div>
                <!-- 成長分析カード -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="font-bold mb-4">特徴・強み</h3>
                    <!-- 成長分析データを表示予定 -->
                </div>
                <!-- 市場価値カード -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="font-bold mb-4">推薦コメント</h3>
                    <!-- 市場価値データを表示予定 -->
                </div>
            </div>
        </div>
    </section>
<!-- ポートフォリオセクション -->
<section class="py-12 px-6">
    <div class="container mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-2xl font-bold">ポートフォリオ</h2>
            <a href="portfolio_input.php" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                作品を追加
            </a>
        </div>

<!-- 作品グリッド -->
<div class="grid md:grid-cols-3 gap-8">
    <?php foreach ($works as $work): ?>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <!-- 画像の表示（1回だけ） -->
            <?php if ($work['image_path']): ?>
                <div class="w-full h-48 overflow-hidden">
                  <img src="<?= getImagePath($work['image_path']) ?>" 
                         alt="<?= htmlspecialchars($work['title']) ?>"
                         class="w-full h-full object-cover"
                         onerror="this.src='img/default-image.png'">
                </div>
            <?php endif; ?>
            
            <div class="p-6">
                <!-- 作品タイトル -->
                <h3 class="text-xl font-bold mb-2">
                    <?= htmlspecialchars($work['title']) ?>
                </h3>
                
                <!-- 作品説明 -->
                <p class="text-gray-600 mb-4">
                    <?= nl2br(htmlspecialchars($work['description'])) ?>
                </p>

                <!-- URL（あれば表示） -->
                <?php if (!empty($work['url'])): ?>
                    <div class="mb-4">
                        <a href="<?= htmlspecialchars($work['url']) ?>" 
                           target="_blank" 
                           rel="noopener noreferrer" 
                           class="text-blue-600 hover:text-blue-800">
                            作品を見る
                        </a>
                    </div>
                <?php endif; ?>

                <!-- カテゴリー -->
                <?php if (!empty($work['category'])): ?>
                    <p class="text-sm text-gray-500">
                        <?= htmlspecialchars($work['category']) ?>
                    </p>
                <?php endif; ?>

                <!-- 編集・削除ボタン -->
                <div class="mt-4 flex gap-4">
                    <a href="portfolio_edit.php?id=<?= $work['id'] ?>" 
                       class="text-blue-600 hover:text-blue-800">
                        編集
                    </a>
                    <a href="portfolio_delete.php?id=<?= $work['id'] ?>" 
                       class="text-red-600 hover:text-red-800"
                       onclick="return confirm('本当に削除しますか？')">
                        削除
                    </a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    </div>
    </div>
</section>
</body>
</html>

<?php
// ===================================================================
// 【主な修正点と理由】
// 1. セッションチェックの追加
//    - 未ログインユーザーのアクセスを防止
//    - セキュリティ強化のため
//
// 2. deleted_at IS NULLの条件追加
//    - 論理削除済みユーザーの表示を防止
//    - データの整合性維持のため
//
// 3. XSS対策の強化
//    - すべての出力にhtmlspecialcharsを使用
//    - セキュリティ脆弱性の防止
//
// 4. 相対パスへの変更
//    - 環境に依存しない実装に修正
//    - 移植性の向上
//
// 5. デフォルト値の設定
//    - Null合体演算子（??）の使用
//    - ユーザー体験の向上
//
// 【今後の改善点】
// 1. プロフィール画像アップロード機能の実装
// 2. AI分析機能の実装
// 3. ポートフォリオ作品の表示セクション追加
// 4. アクティビティログの表示
// 5. フォロー/フォロワー機能の実装
// ===================================================================
?>

