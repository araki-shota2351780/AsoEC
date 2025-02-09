<?php
session_start();

$pdo = new PDO(
    'mysql:host=mysql309.phy.lolipop.lan;dbname=LAA1554909-asoec;charset=utf8',
    'LAA1554909',
    'G1100584a'
);

$data = json_decode(file_get_contents('php://input'), true);
$merch_id = $data['merch_id'] ?? null;

if (!$merch_id) {
    echo json_encode(['success' => false, 'message' => '商品IDが指定されていません']);
    exit;
}

if (isset($_SESSION['user_id'])) {
    // ログイン済みユーザーの場合、データベースから削除
    $user_id = $_SESSION['user_id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = :user_id AND merch_id = :merch_id");
        $stmt->execute([
            ':user_id' => $user_id,
            ':merch_id' => $merch_id,
        ]);

        echo json_encode(['success' => true, 'message' => 'カートから削除しました']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    // ゲストユーザーの場合、セッションから削除
    if (isset($_SESSION['guest_cart'][$merch_id])) {
        unset($_SESSION['guest_cart'][$merch_id]);
        echo json_encode(['success' => true, 'message' => 'カートから削除しました']);
    } else {
        echo json_encode(['success' => false, 'message' => 'カートに該当商品が見つかりません']);
    }
}
?>
