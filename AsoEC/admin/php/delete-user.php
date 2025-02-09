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

    // ユーザーリスト取得
    $stmt = $pdo->query("SELECT user_id, username, email FROM users WHERE status = 1");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ユーザー削除処理
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user_id = $_POST['user_id'];

        // ユーザーを削除
        $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $user_id]);

        $message = 'ユーザーが削除されました。';
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
    <title>ユーザー削除</title>
    <link rel="stylesheet" href="/AsoEC/admin/css/delete-user.css">
</head>
<body>
    <div class="container">
        <div class="input-section">
            <h1>ユーザー削除</h1>
            <?php if (!empty($message)) echo "<p class='message'>{$message}</p>"; ?>
            <form id="deleteUserForm" method="POST" action="">
                <div class="form-group">
                    <label for="user_id">削除するユーザーを選択:</label>
                    <select id="user_id" name="user_id" required>
                        <option value="">ユーザーを選択</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= htmlspecialchars($user['user_id'], ENT_QUOTES, 'UTF-8') ?>">
                                <?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?>)
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
    <script src="/AsoEC/admin/js/delete-user.js"></script>
</body>
</html>
