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

    // 管理者追加処理
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $admin_id = uniqid('admin_'); // 一意の管理者IDを生成
        $admin_name = $_POST['admin_name'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $authority = (int)$_POST['authority'];

        // 新しい管理者を追加
        $stmt = $pdo->prepare("INSERT INTO admins (admin_id, admin_name, email, password, authority) 
                               VALUES (:admin_id, :admin_name, :email, :password, :authority)");
        $stmt->execute([
            ':admin_id' => $admin_id,
            ':admin_name' => $admin_name,
            ':email' => $email,
            ':password' => $password,
            ':authority' => $authority,
        ]);

        $message = '新しい管理者が追加されました。';
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
    <title>管理者追加</title>
    <link rel="stylesheet" href="/AsoEC/admin/css/add-admin.css">
</head>
<body>
    <div class="container">
        <!-- 左側: 入力フォーム -->
        <div class="input-section">
            <h1>管理者追加</h1>
            <?php if (!empty($message)) echo "<p class='message'>{$message}</p>"; ?>
            <form id="addAdminForm" method="POST" action="">
                <div class="form-group">
                    <label for="admin_name">管理者名:</label>
                    <input type="text" id="admin_name" name="admin_name" required>
                </div>
                <div class="form-group">
                    <label for="email">メールアドレス:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">パスワード:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="authority">権限 (1: 一般, 2: 管理者):</label>
                    <select id="authority" name="authority" required>
                        <option value="1">一般</option>
                        <option value="2">管理者</option>
                    </select>
                </div>
                <button id="addAdminButton" type="submit" disabled>追加</button>
            </form>
        </div>

        <!-- 右側: 確認画面 -->
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
    <script src="/AsoEC/admin/js/add-admin.js"></script>
</body>
</html>
