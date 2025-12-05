<?php
require_once __DIR__ . '/../Controller/config.php';
require_once __DIR__ . '/../Controller/cart_handler.php';

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    redirect('index.php');
}
?>