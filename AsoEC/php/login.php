<?php
session_start();

$pdo = new PDO(
    'mysql:host=mysql309.phy.lolipop.lan;dbname=LAA1554909-asoec;charset=utf8',
    'LAA1554909',
    'G1100584a',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || empty($data['email']) || empty($data['password'])) {
    echo json_encode(['success' => false, 'message' => '入力が不足しています']);
    exit;
}

$email = $data['email'];
$password = $data['password'];

$query = $pdo->prepare("SELECT * FROM users WHERE email = :email AND status = 1");
$query->execute(['email' => $email]);
$user = $query->fetch();

if ($user && $password === $user['password']) {
    // ユーザーIDをセッションに保存
    $_SESSION['user_id'] = $user['user_id'];

    // セッション情報をセッションテーブルに登録
    $session_id = bin2hex(random_bytes(16)); // ランダムなセッションID生成
    $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour')); // セッション有効期限（1時間後）

    $insertSession = $pdo->prepare("INSERT INTO sessions (session_id, user_id, created_at, expires_at) VALUES (:session_id, :user_id, CURRENT_TIMESTAMP, :expires_at)");
    $insertSession->execute([
        'session_id' => $session_id,
        'user_id' => $user['user_id'],
        'expires_at' => $expires_at
    ]);

    // セッションIDをクライアントに返す
    echo json_encode(['success' => true, 'message' => 'ログイン成功', 'session_id' => $session_id]);
} else {
    echo json_encode(['success' => false, 'message' => 'ログイン情報が正しくありません']);
}
?>
