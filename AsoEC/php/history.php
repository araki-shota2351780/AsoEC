<?php
session_start();

try {
    // ログインしているか確認
    if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] === null) {
        throw new Exception('このページはログインしているユーザーのみ利用可能です。');
    }

    // ゲストログインかどうか確認
    if ($_SESSION['is_guest'] ?? false) {
        throw new Exception('このページはゲストログインでは利用できません。');
    }

    // データベース接続
    $pdo = new PDO(
        'mysql:host=mysql309.phy.lolipop.lan;dbname=LAA1554909-asoec;charset=utf8',
        'LAA1554909',
        'G1100584a'
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ログイン中のユーザーIDを取得
    $user_id = $_SESSION['user_id'];

    // ログインしているユーザーの購入履歴を取得
    $sql = "SELECT purchase_id, user_id, purchase_date, total_amount, payment_method, status, 
                   shipping_address, shipping_date, delivery_date 
            FROM purchases
            WHERE user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':user_id' => $user_id]);
    $purchases = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    // エラーメッセージを格納
    $error_message = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>購入履歴全データ</title>
<style>
    body {
        font-family: Arial, sans-serif;
        line-height: 1.6;
        background-color: #f9f9f9;
        margin: 0;
        padding: 0;
    }

    .container {
        max-width: 800px;
        margin: 20px auto;
        padding: 20px;
        background: #ffffff;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }

    h2 {
        text-align: center;
        color: #333;
        margin-bottom: 20px;
    }

    hr {
        border: none;
        height: 1px;
        background: #ddd;
        margin: 20px 0;
    }

    .purchase-item {
        padding: 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
        background-color: #fefefe;
        margin-bottom: 15px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .purchase-item div {
        margin-bottom: 8px;
    }

    .purchase-item div strong {
        color: #555;
    }

    .purchase-item:last-child {
        margin-bottom: 0;
    }

    .error {
        color: #ff0000;
        text-align: center;
    }

    .back-button {
        display: block;
        text-align: center;
        margin-top: 20px;
    }

    .back-button a {
        display: inline-block;
        padding: 10px 20px;
        color: #fff;
        background-color: #007bff;
        border-radius: 5px;
        text-decoration: none;
        font-weight: bold;
        transition: background-color 0.3s ease;
    }

    .back-button a:hover {
        background-color: #0056b3;
    }

    @media (max-width: 600px) {
        .container {
            padding: 10px;
        }

        .purchase-item {
            padding: 10px;
        }
    }
</style>
</head>
<body>
<div class="container">
    <h2>購入履歴一覧</h2>
    <hr>
    <?php if (!empty($error_message)): ?>
        <p class="error"><?= htmlspecialchars($error_message) ?></p>
    <?php elseif (!empty($purchases)): ?>
        <?php foreach ($purchases as $purchase): ?>
            <div class="purchase-item">
                <div><strong>注文番号:</strong> <?= htmlspecialchars($purchase['purchase_id']) ?></div>
                <div><strong>購入日時:</strong> <?= htmlspecialchars($purchase['purchase_date']) ?></div>
                <!--受け取りがうまくいかず0になってしまう <div><strong>合計金額:</strong> <?= htmlspecialchars(number_format($purchase['total_amount'], 2)) ?> 円</div> -->
                <div><strong>支払い方法:</strong> <?= htmlspecialchars($purchase['payment_method']) ?></div>
                <div><strong>ステータス:</strong> <?= htmlspecialchars($purchase['status']) == 1 ? '処理中' : '完了' ?></div>
                <div><strong>配送先:</strong> <?= htmlspecialchars($purchase['shipping_address']) ?></div>
                <div><strong>配送日時:</strong> <?= htmlspecialchars($purchase['shipping_date']) ?></div>
            </div>
            <hr>
        <?php endforeach; ?>
    <?php else: ?>
        <p>履歴はありません。</p>
    <?php endif; ?>

    <div class="back-button">
        <a href="/AsoEC/home.html">ホームへ戻る</a>
    </div>
</div>
</body>
</html>
