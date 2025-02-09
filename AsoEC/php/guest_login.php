<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
$_SESSION['is_guest'] = 1;
echo json_encode(['success' => true, 'message' => 'ゲストとしてログインしました']);
?>
