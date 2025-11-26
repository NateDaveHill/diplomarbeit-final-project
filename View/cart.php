<?php
require_once __DIR__ . '/../Controller/config.php';
require_once __DIR__ . '/../Controller/cart_handler.php';

$cart_total = getCartTotal();
$discount = 0;

if (isPremium()) {
    $discount = $cart_total * 0.10;
}

$final_total = $cart_total - $discount;
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Warenkorb - Webshop</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header></header>

</body>
</html>
