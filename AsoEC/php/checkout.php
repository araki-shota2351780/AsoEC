<?php
session_start();

$pdo = new PDO(
    'mysql:host=mysql309.phy.lolipop.lan;dbname=LAA1554909-asoec;charset=utf8',
    'LAA1554909',
    'G1100584a'
);

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => '入力データがありません']);
    exit;
}

$delivery_date = $data['delivery_date'] ?? null;
$delivery_time = $data['delivery_time'] ?? null;
$country = $data['country'] ?? '';
$first_name = $data['first_name'] ?? '';
$last_name = $data['last_name'] ?? '';
$postal_code = $data['postal_code'] ?? '';
$prefecture = $data['prefecture'] ?? '';
$city = $data['city'] ?? '';
$address = $data['address'] ?? '';
$building = $data['building'] ?? '';
$payment_method = $data['payment_method'] ?? null;

if (!$delivery_date || !$delivery_time || !$payment_method) {
    echo json_encode(['success' => false, 'message' => '必須項目が未入力です']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO purchases (user_id, purchase_date, total_amount, payment_method, shipping_address, shipping_date)
        VALUES (:user_id, CURRENT_TIMESTAMP, :total_amount, :payment_method, :shipping_address, :shipping_date)
    ");

    $user_id = $_SESSION['user_id'] ?? null; // ゲストの場合は null
    $total_amount = $_SESSION['total_amount'] ?? 0;
    $shipping_address = "$country $postal_code $prefecture $city $address $building";
    $shipping_date = $delivery_date;

    $stmt->execute([
        ':user_id' => $user_id,
        ':total_amount' => $total_amount,
        ':payment_method' => $payment_method,
        ':shipping_address' => $shipping_address,
        ':shipping_date' => $shipping_date,
    ]);

    echo json_encode(['success' => true, 'message' => '注文が正常に完了しました']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
