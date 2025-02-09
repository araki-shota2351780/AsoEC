<?php
session_start();

$pdo = new PDO(
    'mysql:host=mysql309.phy.lolipop.lan;dbname=LAA1554909-asoec;charset=utf8',
    'LAA1554909',
    'G1100584a',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

// ログイン確認
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'ログインが必要です']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$newUsername = $data['newUsername'] ?? '';
$newEmail = $data['newEmail'] ?? '';

if (empty($newUsername) && empty($newEmail)) {
    echo json_encode(['success' => false, 'message' => '更新するデータを入力してください']);
    exit;
}

try {
    // 更新クエリ
    $updateQuery = "UPDATE users SET ";
    $params = [];
    if (!empty($newUsername)) {
        $updateQuery .= "username = :username, ";
        $params['username'] = $newUsername;
    }
    if (!empty($newEmail)) {
        $updateQuery .= "email = :email, ";
        $params['email'] = $newEmail;
    }
    $updateQuery = rtrim($updateQuery, ', ') . " WHERE user_id = :user_id";
    $params['user_id'] = $_SESSION['user_id'];

    $stmt = $pdo->prepare($updateQuery);
    $stmt->execute($params);

    echo json_encode(['success' => true, 'message' => 'ユーザー情報を更新しました']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => '更新中にエラーが発生しました: ' . $e->getMessage()]);
}
?>
