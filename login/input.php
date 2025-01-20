<!-- 登録画面 -->
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>balubo - ログイン</title>
  <link rel="stylesheet" href="signup.css">
    <!-- Firebase用のスクリプトを追加 -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
    <header>
        <a href="/" class="logo">balubo</a>
    </header>
    <main>
        <!-- 入力した情報をcreate.phpに送る。method="POST": データを安全に送る方法を指定-->
        <form action="create.php" method="POST" class="signup-container">
            <section class="welcome-section">
                <h1>baluboにようこそ！</h1>
                <p>クリエイターの可能性を解き放つ場所</p>
            </section>
        <section class="signup-section">
                <div class="signup-box">
                    <h2>baluboに登録</h2>
                    <p class="login-link">アカウントをお持ちの方は<a href="login.php">ログイン</a>へ</p>
                    <!-- ソーシャルボタン -->
                    <div class="social-buttons">
                        <button type="button" id="googleLogin" class="social-btn google">
                            <img src="./img/googleicon.jpg" alt="">
                            <span>Googleで新規登録</span>
                        </button>
                        <button type="button" class="social-btn x">
                            <img src="./img/x-icon.png" alt="">
                            <span>Xで新規登録</span>
                        </button>
                    </div>
                    <div class="divider">または</div>
                    <!-- メールアドレス登録フォーム -->
                    <div class="form-group">
                        <input type="text" name="email" placeholder="メールアドレス" required>
                    </div>
                    <div class="form-group">
                        <input type="text" name="pw" placeholder="パスワード" required>
                        <span class="password-hint">※半角英数含む6-18文字</span>
                    </div>
                    <!-- <form action="read.php" method="POST">-->
                        <button type="submit" class="submit-btn">新規登録</button>
                </div>
            </section>
        </form>
    </main>

    <!-- Firebase関連のスクリプト -->
    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/11.1.0/firebase-app.js";
        import { getAuth, signInWithPopup, GoogleAuthProvider } 
        from "https://www.gstatic.com/firebasejs/11.1.0/firebase-auth.js";

        const firebaseConfig = {
            apiKey: "AIzaSyBivhvk5LOdAzfIVQD6WgtZSnPBtR8VpKk",
            authDomain: "port-f8f77.firebaseapp.com",
            projectId: "port-f8f77",
            storageBucket: "port-f8f77.firebasestorage.app",
            messagingSenderId: "885240223225",
            appId: "1:885240223225:web:170905f2e79d8b5f4a5928"
        };

        // Initialize Firebase
        const app = initializeApp(firebaseConfig);
        const auth = getAuth(app);
        const provider = new GoogleAuthProvider();

        // Google認証ボタンのクリックイベント
        document.getElementById('googleLogin').addEventListener('click', function() {
            signInWithPopup(auth, provider)
            .then((result) => {
                // ログイン成功時の処理
                const user = result.user;
                // ユーザー情報をサーバーに送信
                fetch('create.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        email: user.email,
                        name: user.displayName,
                        google_id: user.uid
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === "success") {
                        window.location.href = 'http://localhost/portfolio_balubo/mypage/mypage.php';
                         } else {
                         alert('登録に失敗しました');
                        }
                    })
        });
    </script>

</html>

</div>
<a href="read.php">一覧画面</a>
     </div>



    


