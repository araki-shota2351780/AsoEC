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

    // 商品リスト取得
    $stmt = $pdo->query("SELECT merch_id, name FROM merchandise");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 削除処理
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $merch_id = $_POST['merch_id'];

        // 削除クエリ
        $stmt = $pdo->prepare("DELETE FROM merchandise WHERE merch_id = :merch_id");
        $stmt->execute([':merch_id' => $merch_id]);

        $message = '商品が削除されました。';
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
    <title>商品削除</title>
    <link rel="stylesheet" href="/AsoEC/admin/css/delete-merchandise.css">
</head>
<body>
    <div class="container">
        <div class="input-section">
            <h1>商品削除</h1>
            <?php if (!empty($message)) echo "<p class='message'>{$message}</p>"; ?>
            <form id="deleteForm" method="POST" action="">
                <div class="form-group">
                    <label for="merch_id">削除する商品を選択:</label>
                    <select id="merch_id" name="merch_id" required>
                        <option value="">商品を選択</option>
                        <?php foreach ($products as $product): ?>
                            <option value="<?= htmlspecialchars($product['merch_id'], ENT_QUOTES, 'UTF-8') ?>">
                                <?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button id="deleteButton" type="submit" disabled>削除</button>
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
    <script src="/AsoEC/admin/js/delete-merchandise.js"></script>
</body>
</html>
