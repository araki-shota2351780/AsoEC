<?php
session_start();

$pdo = new PDO(
    'mysql:host=mysql309.phy.lolipop.lan;dbname=LAA1554909-asoec;charset=utf8',
    'LAA1554909',
    'G1100584a'
);

$data = json_decode(file_get_contents('php://input'), true);

$merch_id = $data['merch_id'] ?? null;
$quantity = $data['quantity'] ?? 0;

if (!$merch_id || $quantity <= 0) {
    echo json_encode(['success' => false, 'message' => '購入個数は1以上にしてください']);
    exit;
}

if (isset($_SESSION['user_id'])) {
    // ログイン済みユーザーのカートを更新
    $user_id = $_SESSION['user_id'];

    try {
        $stmt = $pdo->prepare("UPDATE cart SET quantity = :quantity WHERE user_id = :user_id AND merch_id = :merch_id");
        $stmt->execute([
            ':quantity' => $quantity,
            ':user_id' => $user_id,
            ':merch_id' => $merch_id,
        ]);

        echo json_encode(['success' => true, 'message' => 'カートを更新しました']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    // ゲストユーザーのカート処理を「商品検索画面でのカートに入れる」と同じ動作に変更
    if (!isset($_SESSION['guest_cart'])) {
        $_SESSION['guest_cart'] = [];
    }

    if (isset($_SESSION['guest_cart'][$merch_id])) {
        // 既存商品の個数を更新
        $_SESSION['guest_cart'][$merch_id]['quantity'] = $quantity;
    } else {
        // 商品情報をデータベースから取得
        $stmt = $pdo->prepare("SELECT name, price, image_url FROM merchandise WHERE merch_id = :merch_id");
        $stmt->execute([':merch_id' => $merch_id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$item) {
            echo json_encode(['success' => false, 'message' => '商品が見つかりません']);
            exit;
        }

        // 新しい商品を追加
        $_SESSION['guest_cart'][$merch_id] = [
            'name' => $item['name'],
            'price' => $item['price'],
            'image_url' => $item['image_url'],
            'quantity' => $quantity,
        ];
    }

    // デバッグログ
    error_log("ゲストカート更新後: " . print_r($_SESSION['guest_cart'], true));

    echo json_encode(['success' => true, 'message' => 'カートを更新しました']);
}
?>
