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

    // 選択された商品のデータ取得
    $selectedProduct = null;
    if (isset($_GET['merch_id'])) {
        $merch_id = $_GET['merch_id'];
        $stmt = $pdo->prepare("SELECT * FROM merchandise WHERE merch_id = :merch_id");
        $stmt->execute([':merch_id' => $merch_id]);
        $selectedProduct = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 更新処理
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $merch_id = $_POST['merch_id'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $stock_quantity = $_POST['stock'];

        // 画像処理
        $image_url = $_POST['current_image_url']; // 初期値として既存の画像URLを使用
        if (!empty($_FILES['image']['name'])) {
            $uploadDir = __DIR__ . '/../../assets/merchandise/';
            $imageName = basename($_FILES['image']['name']);
            $targetFile = $uploadDir . $imageName;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                $image_url = "/AsoEC/assets/merchandise/$imageName";
            } else {
                die('画像アップロードに失敗しました。');
            }
        }

        // 更新クエリ
        $stmt = $pdo->prepare("UPDATE merchandise 
            SET name = :name, description = :description, price = :price, 
            stock_quantity = :stock_quantity, image_url = :image_url 
            WHERE merch_id = :merch_id");

        $stmt->execute([
            ':name' => $name,
            ':description' => $description,
            ':price' => $price,
            ':stock_quantity' => $stock_quantity,
            ':image_url' => $image_url,
            ':merch_id' => $merch_id,
        ]);

        $message = '商品情報が更新されました。';
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
    <title>商品更新</title>
    <link rel="stylesheet" href="/AsoEC/admin/css/update-merchandise.css">
</head>
<body>
    <div class="container">
        <div class="input-section">
            <h1>商品更新</h1>
            <?php if (!empty($message)) echo "<p class='message'>{$message}</p>"; ?>
            <form id="updateForm" enctype="multipart/form-data" method="POST" action="">
                <div class="form-group">
                    <label for="merch_id">商品を選択:</label>
                    <select id="merch_id" name="merch_id" required>
                        <option value="">商品を選択</option>
                        <?php foreach ($products as $product): ?>
                            <option value="<?= htmlspecialchars($product['merch_id'], ENT_QUOTES, 'UTF-8') ?>" 
                                <?= (isset($selectedProduct) && $selectedProduct['merch_id'] == $product['merch_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="image">新しい画像 (任意):</label>
                    <input type="file" id="image" name="image" accept="image/*">
                </div>
                <div class="form-group">
                    <label>現在の商品画像:</label>
                    <div id="currentImage">
                        <?php if (isset($selectedProduct['image_url'])): ?>
                            <img src="<?= htmlspecialchars($selectedProduct['image_url'], ENT_QUOTES, 'UTF-8') ?>" alt="現在の商品画像" style="max-width: 100px;">
                        <?php else: ?>
                            <p>画像は登録されていません。</p>
                        <?php endif; ?>
                    </div>
                    <input type="hidden" id="current_image_url" name="current_image_url" value="<?= htmlspecialchars($selectedProduct['image_url'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="form-group">
                    <label for="name">商品名:</label>
                    <input type="text" id="name" name="name" required value="<?= htmlspecialchars($selectedProduct['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="form-group">
                    <label for="description">説明:</label>
                    <textarea id="description" name="description" rows="4" required><?= htmlspecialchars($selectedProduct['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>
                <div class="form-group">
                    <label for="price">価格 (円):</label>
                    <input type="number" id="price" name="price" step="0.01" required value="<?= htmlspecialchars($selectedProduct['price'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="form-group">
                    <label for="stock">在庫:</label>
                    <input type="number" id="stock" name="stock" required value="<?= htmlspecialchars($selectedProduct['stock_quantity'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
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
    <script src="/AsoEC/admin/js/update-merchandise.js"></script>
</body>
</html>
