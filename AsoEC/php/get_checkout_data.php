<?php
session_start();

if (!isset($_SESSION['checkout_data'])) {
    echo json_encode(['success' => false, 'message' => 'セッションデータが見つかりません']);
    exit;
}

$checkout_data = $_SESSION['checkout_data'];
$total_amount = $_SESSION['total_amount'] ?? 0;

echo json_encode(['success' => true, 'checkout_data' => $checkout_data, 'total_amount' => $total_amount]);
?>
