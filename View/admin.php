<?php
require_once __DIR__ . '/../Controller/config.php';
require_once __DIR__ . '/../Controller/cart_handler.php';
require_once __DIR__ . '/../Controller/admin_handler.php';

$products = $pdo->query("SELECT * FROM products ORDER BY created_at DESC")->fetchAll();
$orders = $pdo->query("SELECT o.*, u.username FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 20")->fetchAll();
$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin Dashboard - Webshop</title>
    <link rel="stylesheet" href="style.css">
    <script src="main.js" defer></script>
</head>
<body>


</body>
</html>
