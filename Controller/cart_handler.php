<?php
require_once __DIR__ . '/../Controller/config.php';

// Warenkorb initialisieren
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Produkt zum Warenkorb hinzufügen
if (isset($_POST['add_to_cart'])) {

    $product_id = (int)$_POST['product_id'];
    $quantity = (int)($_POST['quantity'] ?? 1);

    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();

    if ($product && $product['stock'] >= $quantity) {
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => $quantity,
                'image' => $product['image']
            ];
        }
        $_SESSION['success'] = 'Produkt wurde zum Warenkorb hinzugefügt';
    } else {
        $_SESSION['error'] = 'Produkt nicht verfügbar';
    }

    redirect($_SERVER['HTTP_REFERER'] ?? '/index.php');
}

// Produkt entfernen
if (isset($_GET['remove_from_cart'])) {
    $product_id = (int)$_GET['remove_from_cart'];

    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
        $_SESSION['success'] = 'Produkt entfernt';
    }

    redirect('/cart.php');
}

// Warenkorb aktualisieren
if (isset($_POST['update_cart'])) {

    foreach ($_POST['quantity'] as $product_id => $quantity) {
        $product_id = (int)$product_id;
        $quantity = (int)$quantity;

        if ($quantity <= 0) {
            unset($_SESSION['cart'][$product_id]);
        } else {
            $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = ?");
            $stmt->execute([$product_id]);
            $product = $stmt->fetch();

            if ($product && $product['stock'] >= $quantity) {
                $_SESSION['cart'][$product_id]['quantity'] = $quantity;
            } else {
                $_SESSION['error'] = 'Nicht genügend Lagerbestand';
            }
        }
    }

    $_SESSION['success'] = 'Warenkorb aktualisiert';
    redirect('/cart.php');
}

// Checkout
if (isset($_POST['checkout'])) {
    if (!isLoggedIn()) {
        $_SESSION['error'] = 'Bitte melden Sie sich an';
        redirect('/cart.php');
    }

    if (empty($_SESSION['cart'])) {
        $_SESSION['error'] = 'Warenkorb ist leer';
        redirect('/cart.php');
    }

    try {
        $pdo->beginTransaction();

        $total = 0;
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        $discount = 0;
        if (isPremium()) {
            $discount = $total * 0.10;
        }

        $final_amount = $total - $discount;

        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, discount_amount, final_amount, status) VALUES (?, ?, ?, ?, 'pending')");
        $stmt->execute([$_SESSION['user_id'], $total, $discount, $final_amount]);
        $order_id = $pdo->lastInsertId();

        $stmt_item = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt_stock = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?");

        foreach ($_SESSION['cart'] as $item) {
            $stmt_item->execute([$order_id, $item['id'], $item['quantity'], $item['price']]);

            if (!$stmt_stock->execute([$item['quantity'], $item['id'], $item['quantity']])) {
                throw new Exception('Nicht genügend Lagerbestand');
            }
        }

        $bonus_points = floor($final_amount);
        $stmt = $pdo->prepare("UPDATE users SET bonus_points = bonus_points + ? WHERE id = ?");
        $stmt->execute([$bonus_points, $_SESSION['user_id']]);

        $pdo->commit();

        $_SESSION['cart'] = [];

        $_SESSION['success'] = "Bestellung erfolgreich! Bestellnummer: #$order_id. Sie haben $bonus_points Bonuspunkte erhalten.";
        redirect('/profile.php');

    } catch (Exception $e) {
        $pdo->rollBack();
        logError('Checkout failed', ['error' => $e->getMessage(), 'user_id' => $_SESSION['user_id']]);
        $_SESSION['error'] = 'Bestellung fehlgeschlagen. Bitte versuchen Sie es später erneut.';
        redirect('/cart.php');
    }
}

function getCartTotal()
{
    $total = 0;
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['price'] * $item['quantity'];
        }
    }
    return $total;
}

function getCartCount()
{
    $count = 0;
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $count += $item['quantity'];
        }
    }
    return $count;
}
