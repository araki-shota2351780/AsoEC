<?php
session_start();

// 管理者ログイン確認
if (!isset($_SESSION['admin_id'])) {
    header('Location: /AsoEC/admin/login.html');
    exit;
}

$loggedInAdminId = $_SESSION['admin_id'];
$message = '';

try {
    $pdo = new PDO('mysql:host=mysql309.phy.lolipop.lan;dbname=LAA1554909-asoec;charset=utf8', 'LAA1554909', 'G1100584a');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 商品リスト取得（商品名と現在在庫数）
    $stmt = $pdo->query("SELECT merch_id, name, stock_quantity FROM merchandise");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 在庫更新処理
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        foreach ($_POST['stock'] as $merch_id => $stock_add) {
            $stock_add = (int)$stock_add;

            if ($stock_add > 0) {
                // 在庫数を更新
                $stmt = $pdo->prepare("UPDATE merchandise SET stock_quantity = stock_quantity + :stock_add WHERE merch_id = :merch_id");
                $stmt->execute([
                    ':stock_add' => $stock_add,
                    ':merch_id' => $merch_id,
                ]);
            }
        }
        $message = '在庫が更新されました。';
    }
} catch (PDOException $e) {
    $message = 'エラー: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>在庫追加</title>
    <link rel="stylesheet" href="/AsoEC/admin/css/update-stock.css">
</head>
<body>
    <div class="container">
        <div class="input-section">
            <h1>在庫追加</h1>
            <?php if (!empty($message)) echo "<p class='message'>{$message}</p>"; ?>
            <form id="stockForm" method="POST" action="">
                <table>
                    <thead>
                        <tr>
                            <th>商品名</th>
                            <th>現在の在庫数</th>
                            <th>追加する在庫数</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($product['stock_quantity'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <input type="number" name="stock[<?= htmlspecialchars($product['merch_id'], ENT_QUOTES, 'UTF-8') ?>]" min="0" step="1">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <button id="updateButton" type="submit" disabled>更新</button>
            </form>
        </div>
        <div class="preview-section">
            <h2>管理者確認</h2>
            <div class="form-group">
                <label for="adminCheck">ログイン中の管理者ID:</label>
                <input type="text" id="adminCheck" placeholder="<?= htmlspecialchars($loggedInAdminId, ENT_QUOTES, 'UTF-8') ?>" />
            </div>
            <p id="adminStatus" class="status-text">管理者IDを入力してください。</p>
            <div class="home-link">
                <a href="/AsoEC/admin/dashboard.html">
                    <img src="/AsoEC/admin/assets/icons/home.svg" alt="ホーム" class="home-image">
                </a>
                <p class="home-text">ホームへ</p>
            </div>
        </div>
    </div>
    <script src="/AsoEC/admin/js/update-stock.js"></script>
</body>
</html>
