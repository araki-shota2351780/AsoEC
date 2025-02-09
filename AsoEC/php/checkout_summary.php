<?php
session_start();
header('Content-Type: application/json');

try {
    $pdo = new PDO(
        'mysql:host=mysql309.phy.lolipop.lan;dbname=LAA1554909-asoec;charset=utf8',
        'LAA1554909',
        'G1100584a'
    );

    // デバッグ開始
    error_log("購入処理開始: セッション情報 -> " . print_r($_SESSION, true));

    // セッションから配送情報を取得
    $checkout_data = $_SESSION['checkout_data'] ?? [
        'first_name' => 'ゲスト',
        'last_name' => 'ユーザー',
        'country' => '不明',
        'postal_code' => '',
        'prefecture' => '',
        'city' => '',
        'address' => '',
        'building' => '',
        'delivery_date' => '未指定',
        'delivery_time' => '未指定',
        'payment_method' => '未指定',
    ];

    // セッション内の初期合計金額を取得
    $session_total_amount = $_SESSION['total_amount'] ?? 0;

    $cart_total_amount = 0; // カート内商品の合計金額初期化
    $cart_items = [];

    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];

        // カート内のアイテムを取得
        $stmt_cart = $pdo->prepare("
            SELECT m.name, m.price, c.quantity 
            FROM cart c
            INNER JOIN merchandise m ON c.merch_id = m.merch_id
            WHERE c.user_id = :user_id
        ");
        $stmt_cart->execute([':user_id' => $user_id]);
        $cart_items = $stmt_cart->fetchAll(PDO::FETCH_ASSOC);

        // デバッグ：カート内容
        error_log("カート内容 -> " . print_r($cart_items, true));

        // カート内商品の金額を計算
        foreach ($cart_items as $item) {
            $cart_total_amount += $item['price'] * $item['quantity'];
        }

        // デバッグ：カート内の合計金額
        error_log("カート内合計金額 -> {$cart_total_amount}");

        // カートの合計金額とセッションの金額を加算
        $total_amount = $session_total_amount + $cart_total_amount;

        // デバッグ：最終合計金額
        error_log("最終合計金額 -> {$total_amount}");

        // 購入履歴を保存
        if (!isset($_SESSION['purchase_logged']) || $_SESSION['purchase_logged'] === false) {
            $shipping_address = "{$checkout_data['country']} {$checkout_data['postal_code']} {$checkout_data['prefecture']} {$checkout_data['city']} {$checkout_data['address']} {$checkout_data['building']}";
            $stmt_purchase = $pdo->prepare("
                INSERT INTO purchases (user_id, purchase_date, total_amount, payment_method, shipping_address, shipping_date)
                VALUES (:user_id, CURRENT_TIMESTAMP, :total_amount, :payment_method, :shipping_address, :shipping_date)
            ");
            $stmt_purchase->execute([
                ':user_id' => $user_id,
                ':total_amount' => $total_amount, // 合計金額
                ':payment_method' => $checkout_data['payment_method'],
                ':shipping_address' => $shipping_address,
                ':shipping_date' => $checkout_data['delivery_date'],
            ]);

            // セッションにフラグをセット
            $_SESSION['purchase_logged'] = true;

            // カートをクリア
            $stmt_clear_cart = $pdo->prepare("DELETE FROM cart WHERE user_id = :user_id");
            $stmt_clear_cart->execute([':user_id' => $user_id]);

            // デバッグ：購入履歴に保存成功
            error_log("購入履歴保存成功: user_id -> {$user_id}");
        }
    } else {
        // ゲストユーザーの場合はセッションの金額をそのまま使用
        $total_amount = $session_total_amount;
        error_log("ゲストユーザー: 合計金額 -> {$total_amount}");
    }

    // フロントエンドにデータを返す
    echo json_encode([
        'success' => true,
        'checkout_data' => $checkout_data,
        'cart_info' => $cart_items,
        'total_amount' => $total_amount,
    ]);
} catch (PDOException $e) {
    error_log("エラー -> " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
