<?php
session_start();

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => '入力データがありません']);
    exit;
}

// 必須フィールドを確認
$required_fields = ['delivery_date', 'delivery_time', 'country', 'first_name', 'last_name', 'postal_code', 'prefecture', 'city', 'address', 'payment_method'];
foreach ($required_fields as $field) {
    if (empty($data[$field])) {
        echo json_encode(['success' => false, 'message' => "$field は必須項目です"]);
        exit;
    }
}

// 仮にカートの合計料金を計算 (例: カート内容から取得)
$cart_items = $_SESSION['guest_cart'] ?? [];
$total_amount = 0;

foreach ($cart_items as $item) {
    $total_amount += $item['price'] * $item['quantity'];
}

// 配送料や手数料を追加
if ($data['payment_method'] === 'cash_on_delivery' || $data['payment_method'] === 'convenience_store') {
    $total_amount += 330; // 手数料: ¥330
}

// セッションにデータを保存
$_SESSION['checkout_data'] = $data;
$_SESSION['total_amount'] = $total_amount;

echo json_encode(['success' => true, 'message' => 'データがセッションに保存されました']);
?>
