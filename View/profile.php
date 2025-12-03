<?php
require_once __DIR__ . '/../Controller/config.php';
require_once __DIR__ . '/../Controller/cart_handler.php';

if (!isLoggedIn()) {
    redirect('index.php');
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();
?>