<?php
// portfolio_create.php
session_start();
include('../functions.php');
check_session_id();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'] ?? '';
    $url = $_POST['url'] ?? '';
    $category = $_POST['category'] ?? '';
    $user_id = $_SESSION['user_id'];
    
    // 画像パスの初期化
    $image_path = '';
    
    // ファイルアップロードがある場合
    if (!empty($_FILES['image']['name'])) {
        try {
            $image_path = handleImageUpload($_FILES['image']);
        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
            header('Location: portfolio_input.php');
            exit();
        }
    } 
    // URLからの画像取得がある場合
    elseif (!empty($url)) {
        $metadata = fetch_url_metadata($url);
        if (!empty($metadata['image'])) {
            // 外部URLの画像をダウンロードして保存
            try {
                $image_content = file_get_contents($metadata['image']);
                if ($image_content !== false) {
                    $ext = pathinfo(parse_url($metadata['image'], PHP_URL_PATH), PATHINFO_EXTENSION);
                    $ext = $ext ?: 'jpg'; // 拡張子がない場合はjpgとする
                    
                    $filename = uniqid() . '_feed.' . $ext;
                    $save_path = 'uploads/' . $filename;
                    
                    if (file_put_contents($save_path, $image_content)) {
                        $image_path = $save_path;
                    }
                }
            } catch (Exception $e) {
                // 画像の取得に失敗しても処理は続行
                error_log("Feed image download failed: " . $e->getMessage());
            }
        }
    }

    // データベースに保存
    $pdo = connect_to_db();
    $sql = 'INSERT INTO portfolio (title, description, image_path, url, category, user_id, created_at) 
            VALUES (:title, :description, :image_path, :url, :category, :user_id, NOW())';
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':title', $title, PDO::PARAM_STR);
    $stmt->bindValue(':description', $description, PDO::PARAM_STR);
    $stmt->bindValue(':image_path', $image_path, PDO::PARAM_STR);
    $stmt->bindValue(':url', $url, PDO::PARAM_STR);
    $stmt->bindValue(':category', $category, PDO::PARAM_STR);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);

    try {
        $status = $stmt->execute();
        $_SESSION['success_message'] = '作品を登録しました！';
        header('Location: mypage.php');
        exit();
    } catch (PDOException $e) {
        $_SESSION['error_message'] = '登録に失敗しました：' . $e->getMessage();
        header('Location: portfolio_input.php');
        exit();
    }
}

// POST以外のリクエストは入力ページにリダイレクト
header('Location: portfolio_input.php');
exit();