<?php
session_start();

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => '入力データがありません']);
    exit;
}

$card_number = $data['card_number'] ?? null;
$expiry_date = $data['expiry_date'] ?? null;
$security_code = $data['security_code'] ?? null;
$card_holder_name = $data['card_holder_name'] ?? null;
$delivery_method = $data['delivery_method'] ?? null;

if (!$card_number || !$expiry_date || !$security_code || !$card_holder_name || !$delivery_method) {
    echo json_encode(['success' => false, 'message' => '必須項目が未入力です']);
    exit;
}

// カード情報をセッションに保存
$_SESSION['card_info'] = [
    'card_number' => $card_number,
    'expiry_date' => $expiry_date,
    'security_code' => $security_code,
    'card_holder_name' => $card_holder_name,
    'delivery_method' => $delivery_method,
];

echo json_encode(['success' => true, 'message' => 'カード情報がセッションに保存されました']);
?>
