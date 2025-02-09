<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // 入力チェック
    if (empty($username) || empty($email) || empty($password)) {
        echo "すべての項目を入力してください。";
        exit;
    }

    try {
        // データベース接続
        $pdo = new PDO(
            'mysql:host=mysql309.phy.lolipop.lan;dbname=LAA1554909-asoec;charset=utf8',
            'LAA1554909',
            'G1100584a',
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );

        // INSERTクエリ
        $stmt = $pdo->prepare("INSERT INTO users (user_id, username, email, password) VALUES (UUID(), ?, ?, ?)");
        $stmt->execute([$username, $email, $password]);

        echo "登録が成功しました！";
    } catch (PDOException $e) {
        if ($e->getCode() === '23000') {
            echo "メールアドレスが既に登録されています。";
        } else {
            echo "データベースエラー: " . $e->getMessage();
        }
    }
} else {
    echo "不正なリクエストです。";
}
?>
