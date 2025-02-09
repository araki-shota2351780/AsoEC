<?php
session_start();

$pdo = new PDO(
    'mysql:host=mysql309.phy.lolipop.lan;dbname=LAA1554909-asoec;charset=utf8',
    'LAA1554909',
    'G1100584a'
);

$items = [];
$total_price = 0;

if (isset($_SESSION['user_id'])) {
    // ログインユーザーの場合、データベースからカート情報を取得
    $user_id = $_SESSION['user_id'];
    try {
        $stmt = $pdo->prepare("SELECT m.merch_id, m.name, m.price, m.image_url, c.quantity 
                               FROM cart c
                               INNER JOIN merchandise m ON c.merch_id = m.merch_id
                               WHERE c.user_id = :user_id");
        $stmt->execute([':user_id' => $user_id]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($items as $item) {
            $total_price += $item['price'] * $item['quantity'];
        }

        echo json_encode(['success' => true, 'items' => $items, 'total_price' => $total_price]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    // ゲストユーザーの場合、セッションから取得
    if (isset($_SESSION['guest_cart'])) {
        foreach ($_SESSION['guest_cart'] as $merch_id => $item) {
            $items[] = $item;
            $total_price += $item['price'] * $item['quantity'];
        }
        echo json_encode(['success' => true, 'items' => $items, 'total_price' => $total_price]);
    } else {
        echo json_encode(['success' => false, 'message' => 'カートは空です']);
    }
}
?>
