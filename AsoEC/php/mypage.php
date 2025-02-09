<?php
$pdo = new PDO(
    'mysql:host=mysql309.phy.lolipop.lan;dbname=LAA1554909-asoec;charset=utf8',
    'LAA1554909',
    'G1100584a'
);

session_start();

// ログイン状態を確認
if (!isset($_SESSION['user_id']) && !isset($_SESSION['is_guest'])) {
    echo json_encode(['success' => false, 'message' => 'ログインしてください']);
    exit;
}

// 通常のユーザー情報を取得
if (isset($_SESSION['user_id'])) {
    $query = $pdo->prepare("SELECT username, email, created_at FROM users WHERE user_id = :user_id");
    $query->execute(['user_id' => $_SESSION['user_id']]);
    $user = $query->fetch();

    if ($user) {
        echo json_encode(['success' => true, 'data' => $user]);
    } else {
        echo json_encode(['success' => false, 'message' => 'ユーザー情報が見つかりません']);
    }
} else {
    // ゲストユーザーの固定情報を返す
    echo json_encode(['success' => true, 'data' => [
        'username' => 'ゲスト',
        'email' => '未登録',
        'created_at' => '未登録'
    ]]);
}
?>
