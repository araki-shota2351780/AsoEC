<?php
session_start();

$pdo = new PDO(
    'mysql:host=mysql309.phy.lolipop.lan;dbname=LAA1554909-asoec;charset=utf8',
    'LAA1554909',
    'G1100584a'
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $admin_name = $_POST['admin_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO admins (admin_id, admin_name, email, password, authority) VALUES (:admin_id, :admin_name, :email, :password, 1)");
    $stmt->bindValue(':admin_id', uniqid());
    $stmt->bindValue(':admin_name', $admin_name);
    $stmt->bindValue(':email', $email);
    $stmt->bindValue(':password', $password);

    if ($stmt->execute()) {
        echo "<script>alert('Registration successful'); window.location.href = '../login.html';</script>";
    } else {
        echo "<script>alert('Registration failed'); window.history.back();</script>";
    }
}
?>
