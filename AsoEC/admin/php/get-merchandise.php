<?php
session_start();

// 管理者ログイン確認
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403); // Forbidden
    echo json_encode(['error' => '管理者ログインが必要です。']);
    exit;
}

// 必要なパラメータ確認
if (!isset($_GET['merch_id']) || empty($_GET['merch_id'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => '商品IDが指定されていません。']);
    exit;
}

$merch_id = $_GET['merch_id'];

try {
    // データベース接続
    $pdo = new PDO('mysql:host=mysql309.phy.lolipop.lan;dbname=LAA1554909-asoec;charset=utf8', 'LAA1554909', 'G1100584a');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 商品データ取得
    $stmt = $pdo->prepare("SELECT * FROM merchandise WHERE merch_id = :merch_id");
    $stmt->execute([':merch_id' => $merch_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        echo json_encode($product, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    } else {
        http_response_code(404); // Not Found
        echo json_encode(['error' => '指定された商品は存在しません。']);
    }
} catch (PDOException $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'データベースエラー: ' . $e->getMessage()]);
}
