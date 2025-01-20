<?php
// ===================================================================
// login.php - ログインページの実装
// 目的：通常ログインとGoogle認証の2つのログイン方法を提供
// ===================================================================

// ログインフォームの基本設計
// 1. formのaction属性：login_process.phpを指定（処理ファイル）
// 2. method属性：POSTを使用（セキュリティ考慮）
// 3. 必須項目：required属性で入力チェック
// login.php → login_process.php → mypage.php
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>balubo - ログイン</title>
    <link rel="stylesheet" href="signup.css">
    <!-- jQueryライブラリの読み込み（Google認証用） -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body>
    <header>
        <a href="/" class="logo">balubo</a>
    </header>

    <main>
        <!-- ログインフォーム -->
        <!-- 入力した情報をlogin_process.phpに送る。method="POST": データを安全に送る方法を指定-->
        <form action="login_process.php" method="POST" class="signup-container">
            <section class="welcome-section">
                <h1>おかえりなさい！</h1>
                <p>メモ：ログイン画面</p>
            </section>

            <section class="signup-section">
                <div class="signup-box">
                    <h2>ログイン</h2>
                    <!-- 新規登録リンク -->
                    <p class="login-link">アカウントをお持ちでない方は<a href="input.php">新規登録</a>へ</p>
                    <!-- Google認証ボタン -->
                    <button id="login" class="social-btn google">
                        <img src="./img/googleicon.jpg" alt="">
                        <span>Googleでログイン</span>
                    </button>

                    <div class="divider">または</div>
                    <!-- メールアドレスログインフォーム -->
                     <!-- required属性：必須項目の指定-->
                    <div class="form-group">
                        <!-- type="email"：メールアドレスの形式チェック -->
                        <input type="email" name="email" placeholder="メールアドレス" required>
                    </div>
                    <div class="form-group">
                        <!-- type="password"：パスワードを●●●で表示 -->
                        <input type="password" name="pw" placeholder="パスワード" required>
                    </div>
                    <button type="submit" class="submit-btn">ログイン</button>
                </div>
            </section>
        </form>
    </main>

    <!-- Firebase認証の実装 -->
    <script type="module">
        // Firebaseモジュールのインポート
        // 認証に必要な機能をインポート
        import { initializeApp } from "https://www.gstatic.com/firebasejs/11.1.0/firebase-app.js";
        import { getAuth, signInWithPopup, GoogleAuthProvider } 
        from "https://www.gstatic.com/firebasejs/11.1.0/firebase-auth.js";
        // Firebaseの設定情報
        // 注意：本番環境では環境変数で管理することを推奨
        const firebaseConfig = {
            apiKey: "AIzaSyBivhvk5LOdAzfIVQD6WgtZSnPBtR8VpKk",
            authDomain: "port-f8f77.firebaseapp.com",
            projectId: "port-f8f77",
            storageBucket: "port-f8f77.firebasestorage.app",
            messagingSenderId: "885240223225",
            appId: "1:885240223225:web:170905f2e79d8b5f4a5928"
        };
        // Firebaseの初期化
        const app = initializeApp(firebaseConfig);
        const auth = getAuth(app);
        const provider = new GoogleAuthProvider();

        // Googleログインボタンのクリックイベント処理
        $("#login").on("click", function(e) {
            e.preventDefault(); // デフォルトのフォーム送信を防止
    
            // Google認証ポップアップを表示
            signInWithPopup(auth, provider)
            .then((result) => {
                const user = result.user;
                // 認証成功後、ユーザー情報をサーバーに送信
                return fetch('login_process.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        email: user.email,
                        name: user.displayName,
                        google_id: user.uid
                    })
                });
            })
            .then(response => response.json())
            .then(data => {
                // 認証成功時はマイページへリダイレクト
                if (data.status === "success") {
                    window.location.href = 'http://localhost/portfolio_balubo/mypage/mypage.php';
                } else {
                    alert('ログインに失敗しました');
                }
            })
            .catch((error) => {
                // エラー発生時の処理
                console.error("ログインエラー：", error);
                alert('ログインに失敗しました');
            });
        });
    </script>
</body>
</html>

