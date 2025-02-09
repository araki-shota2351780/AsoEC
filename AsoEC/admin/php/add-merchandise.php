<?php
session_start();
// 管理者ログイン確認
if (!isset($_SESSION['admin_id'])) {
    header('Location: /AsoEC/admin/login.html');
    exit;
}

$loggedInAdminId = $_SESSION['admin_id'];

// POSTリクエストの処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['merch_id']) && !empty($_POST['merch_id'])) {
        $merch_id = $_POST['merch_id'];
    } else {
        die('merch_idが設定されていません。');
    }

    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock_quantity = $_POST['stock'];
    $release_date = date('Y-m-d');
    $admin_id = $loggedInAdminId;

    // 画像ファイル処理
    $uploadDir = __DIR__ . '/../../assets/merchandise/';
    $imageName = basename($_FILES['image']['name']);
    $targetFile = $uploadDir . $imageName;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
        try {
            $pdo = new PDO('mysql:host=mysql309.phy.lolipop.lan;dbname=LAA1554909-asoec;charset=utf8', 'LAA1554909', 'G1100584a');

            $stmt = $pdo->prepare("INSERT INTO merchandise 
                (merch_id, name, description, price, stock_quantity, release_date, admin_id, image_url)
                VALUES (:merch_id, :name, :description, :price, :stock_quantity, :release_date, :admin_id, :image_url)");

            $stmt->execute([
                ':merch_id' => $merch_id,
                ':name' => $name,
                ':description' => $description,
                ':price' => $price,
                ':stock_quantity' => $stock_quantity,
                ':release_date' => $release_date,
                ':admin_id' => $admin_id,
                ':image_url' => "/AsoEC/assets/merchandise/$imageName"
            ]);

            $message = '商品登録が成功しました！';
        } catch (PDOException $e) {
            $message = 'エラー: ' . $e->getMessage();
        }
    } else {
        $message = '画像アップロードに失敗しました。';
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品追加</title>
    <link rel="stylesheet" href="/AsoEC/admin/css/add-merchandise.css">
</head>
<body>
    <div class="container">
        <!-- 左側: 入力フォーム -->
        <div class="input-section">
            <h1>商品登録</h1>
            <?php if (isset($message)) echo "<p class='message'>{$message}</p>"; ?>
            <form id="merchForm" enctype="multipart/form-data" method="POST" action="">
                <div class="form-group">
                    <label for="category">カテゴリ選択:</label>
                    <select id="category" name="category" required>
                        <option value="a">アクセサリー</option>
                        <option value="m">男物</option>
                        <option value="w">女物</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="size">サイズ選択:</label>
                    <select id="size" name="size" required>
                        <option value="O">OnlySize</option>
                        <option value="S">Small</option>
                        <option value="M">Medium</option>
                        <option value="L">Large</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="image">写真追加:</label>
                    <input type="file" id="image" name="image" accept="image/*" required>
                </div>

                <div class="form-group">
                    <label for="name">商品名:</label>
                    <input type="text" id="name" name="name" required>
                </div>

                <div class="form-group">
                    <label for="description">説明:</label>
                    <textarea id="description" name="description" rows="4" required></textarea>
                </div>

                <div class="form-group">
                    <label for="price">価格 (円):</label>
                    <input type="number" id="price" name="price" step="0.01" required>
                </div>

                <div class="form-group">
                    <label for="stock">初期在庫:</label>
                    <input type="number" id="stock" name="stock" required>
                </div>

                <!-- 隠しフィールド -->
                <input type="hidden" id="merch_id" name="merch_id">

                <button id="registerButton" type="submit" disabled>登録</button>
            </form>
        </div>

        <!-- 右側: 確認画面 -->
        <div class="preview-section">
    <h2>管理者確認</h2>
    <div class="form-group">
        <label for="adminCheck">ログイン中の管理者ID:</label>
        <input type="text" id="adminCheck" placeholder="<?php echo htmlspecialchars($loggedInAdminId, ENT_QUOTES, 'UTF-8'); ?>" />
    </div>
    <p id="adminStatus" class="status-text">管理者IDを入力してください。</p>
    
    <!-- 画像リンク -->
    <div class="home-link">
    <a href="/AsoEC/admin/dashboard.html">
        <img src="/AsoEC/admin/assets/icons/home.svg" alt="ホーム" class="home-image">
    </a>
    <p class="home-text">ホームへ</p>
</div>

</div>

    <script>
    document.getElementById('merchForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const category = document.getElementById('category').value;
        const size = document.getElementById('size').value;

        const randomString = Math.random().toString(36).substring(2, 7);
        const merchId = `${category}${size}${randomString}`;

        document.getElementById('merch_id').value = merchId;

        this.submit();
    });

    // 管理者ID確認処理
    const adminId = document.getElementById('adminCheck').placeholder;
    const adminInput = document.getElementById('adminCheck');
    const registerButton = document.getElementById('registerButton');
    const adminStatus = document.getElementById('adminStatus');

    adminInput.addEventListener('input', () => {
        if (adminInput.value === adminId) {
            adminStatus.textContent = "管理者IDが確認されました。";
            adminStatus.style.color = "green";
            registerButton.disabled = false;
        } else {
            adminStatus.textContent = "管理者IDが一致しません。";
            adminStatus.style.color = "red";
            registerButton.disabled = true;
        }
    });
    </script>
</body>
</html>
