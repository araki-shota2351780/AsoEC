<?php
session_start();

$pdo = new PDO(
    'mysql:host=mysql309.phy.lolipop.lan;dbname=LAA1554909-asoec;charset=utf8',
    'LAA1554909',
    'G1100584a'
);

$data = json_decode(file_get_contents('php://input'), true);
$merch_id = $data['merch_id'] ?? null;
$quantity = $data['quantity'] ?? 1;

if (!$merch_id || $quantity <= 0) {
    echo json_encode(['success' => false, 'message' => '商品情報が不正です']);
    exit;
}

if (isset($_SESSION['user_id'])) {
    // ログイン済みユーザーの場合、データベースに保存
    $user_id = $_SESSION['user_id'];
    try {
        $stmt = $pdo->prepare("INSERT INTO cart (user_id, merch_id, quantity, created_at) 
                               VALUES (:user_id, :merch_id, :quantity, CURRENT_TIMESTAMP)
                               ON DUPLICATE KEY UPDATE quantity = quantity + :quantity");
        $stmt->execute([
            ':user_id' => $user_id,
            ':merch_id' => $merch_id,
            ':quantity' => $quantity,
        ]);

        echo json_encode(['success' => true, 'message' => 'カートに追加しました']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    // ゲストユーザーの場合、セッションに保存
    if (!isset($_SESSION['guest_cart'])) {
        $_SESSION['guest_cart'] = [];
    }

    // カートに商品を追加
    if (isset($_SESSION['guest_cart'][$merch_id])) {
        $_SESSION['guest_cart'][$merch_id]['quantity'] += $quantity;
    } else {
        // 商品情報をデータベースから取得
        $stmt = $pdo->prepare("SELECT name, price, image_url FROM merchandise WHERE merch_id = :merch_id");
        $stmt->execute([':merch_id' => $merch_id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$item) {
            echo json_encode(['success' => false, 'message' => '商品が見つかりません']);
            exit;
        }

        $_SESSION['guest_cart'][$merch_id] = [
            'name' => $item['name'],
            'price' => $item['price'],
            'image_url' => $item['image_url'],
            'quantity' => $quantity,
        ];
    }

    echo json_encode(['success' => true, 'message' => 'カートに追加しました']);
}
?>
