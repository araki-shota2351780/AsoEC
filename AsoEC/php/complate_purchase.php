<?php
session_start();
header('Content-Type: application/json');

try {
    $pdo = new PDO(
        'mysql:host=mysql309.phy.lolipop.lan;dbname=LAA1554909-asoec;charset=utf8',
        'LAA1554909',
        'G1100584a'
    );

    $user_id = $_SESSION['user_id'] ?? null; // ゲストの場合はnull
    $checkout_data = $_SESSION['checkout_data'] ?? [];
    $session_total_amount = $_SESSION['total_amount'] ?? 0;

    $shipping_address = "{$checkout_data['country']} {$checkout_data['postal_code']} {$checkout_data['prefecture']} {$checkout_data['city']} {$checkout_data['address']} {$checkout_data['building']}";

    $pdo->beginTransaction();

    // ゲストユーザーの場合は履歴を保存しない
    if ($user_id) {
        $stmt = $pdo->prepare("
            INSERT INTO purchases (user_id, purchase_date, total_amount, payment_method, shipping_address, shipping_date)
            VALUES (:user_id, CURRENT_TIMESTAMP, :total_amount, :payment_method, :shipping_address, :shipping_date)
        ");
        $stmt->execute([
            ':user_id' => $user_id,
            ':total_amount' => $session_total_amount,
            ':payment_method' => $checkout_data['payment_method'],
            ':shipping_address' => $shipping_address,
            ':shipping_date' => $checkout_data['delivery_date']
        ]);
    }

    // 在庫の減算とカート削除はユーザーのみ対象
    if ($user_id) {
        $stmt_cart = $pdo->prepare("
            SELECT c.merch_id, c.quantity, m.price, m.stock_quantity 
            FROM cart c
            INNER JOIN merchandise m ON c.merch_id = m.merch_id
            WHERE c.user_id = :user_id
        ");
        $stmt_cart->execute([':user_id' => $user_id]);
        $cart_items = $stmt_cart->fetchAll(PDO::FETCH_ASSOC);

        foreach ($cart_items as $item) {
            if ($item['stock_quantity'] < $item['quantity']) {
                throw new Exception("在庫が不足しています: 商品ID {$item['merch_id']}");
            }

            $stmt_update_stock = $pdo->prepare("
                UPDATE merchandise 
                SET stock_quantity = stock_quantity - :quantity 
                WHERE merch_id = :merch_id
            ");
            $stmt_update_stock->execute([
                ':quantity' => $item['quantity'],
                ':merch_id' => $item['merch_id']
            ]);
        }

        $stmt_clear_cart = $pdo->prepare("DELETE FROM cart WHERE user_id = :user_id");
        $stmt_clear_cart->execute([':user_id' => $user_id]);
    }

    $pdo->commit();

    echo json_encode(['success' => true, 'message' => '購入が完了しました']);

    unset($_SESSION['checkout_data'], $_SESSION['total_amount']);
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
