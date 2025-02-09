<?php
// データベース接続設定
$dsn = 'mysql:host=mysql309.phy.lolipop.lan;dbname=LAA1554909-asoec;charset=utf8';
$username = 'LAA1554909';
$password = 'G1100584a';

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // パラメータを取得
    $category = isset($_GET['category']) ? $_GET['category'] : null;

    // SQLクエリの作成
    $sql = "SELECT * FROM merchandise WHERE 1=1";

    // カテゴリフィルタ（1桁目）
    if ($category) {
        $sql .= " AND LEFT(merch_id, 1) = :category";
    }

    $stmt = $pdo->prepare($sql);

    // パラメータをバインド
    if ($category) {
        $stmt->bindValue(':category', $category, PDO::PARAM_STR);
    }

    $stmt->execute();
    $merchandise = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 結果を JSON 形式で返す
    header('Content-Type: application/json');
    echo json_encode($merchandise);
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
