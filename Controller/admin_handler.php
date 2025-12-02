<?php


require_once __DIR__ . '/../Controller/config.php';

if (!isAdmin()) {
    redirect('/index.php');
}

// Produkt hinzufügen
if (isset($_POST['add_product'])) {

    $name = sanitizeInput($_POST['name']);
    $description = sanitizeInput($_POST['description']);
    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock'];
    $image = sanitizeInput($_POST['image']);

    if (empty($name) || $price <= 0) {
        $_SESSION['error'] = 'Name und gültiger Preis erforderlich';
        redirect('/admin.php');
    }

    if ($stock < 0) {
        $_SESSION['error'] = 'Lagerbestand kann nicht negativ sein';
        redirect('/admin.php');
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO products (name, description, price, stock, image) VALUES (?, ?, ?, ?, ?)");

        if ($stmt->execute([$name, $description, $price, $stock, $image])) {
            $_SESSION['success'] = 'Produkt hinzugefügt';
        } else {
            throw new Exception('Database insert failed');
        }
    } catch (Exception $e) {
        logError('Add product failed', ['error' => $e->getMessage(), 'product_name' => $name]);
        $_SESSION['error'] = 'Fehler beim Hinzufügen';
    }

    redirect('/admin.php');
}

// Produkt bearbeiten
if (isset($_POST['edit_product'])) {

    $id = (int)$_POST['id'];
    $name = sanitizeInput($_POST['name']);
    $description = sanitizeInput($_POST['description']);
    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock'];
    $image = sanitizeInput($_POST['image']);

    if (empty($name) || $price <= 0) {
        $_SESSION['error'] = 'Name und gültiger Preis erforderlich';
        redirect('/admin.php');
    }

    if ($stock < 0) {
        $_SESSION['error'] = 'Lagerbestand kann nicht negativ sein';
        redirect('/admin.php');
    }

    $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, price = ?, stock = ?, image = ? WHERE id = ?");

    if ($stmt->execute([$name, $description, $price, $stock, $image, $id])) {
        $_SESSION['success'] = 'Produkt aktualisiert';
    }

    redirect('/admin.php');
}

// Produkt löschen
if (isset($_POST['delete_product'])) {

    $id = (int)$_POST['delete_product'];

    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");

    if ($stmt->execute([$id])) {
        $_SESSION['success'] = 'Produkt gelöscht';
    }

    redirect('/admin.php');
}

// Bestellstatus ändern
if (isset($_POST['update_order_status'])) {

    $order_id = (int)$_POST['order_id'];
    $status = $_POST['status'];

    $allowed_statuses = ['pending', 'paid', 'shipped', 'delivered', 'cancelled'];

    if (!in_array($status, $allowed_statuses)) {
        $_SESSION['error'] = 'Ungültiger Status';
        redirect('/admin.php');
    }

    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");

    if ($stmt->execute([$status, $order_id])) {
        $_SESSION['success'] = 'Status aktualisiert';
    }

    redirect('/admin.php');
}

// Benutzerrolle ändern
if (isset($_POST['update_user_role'])) {

    $user_id = (int)$_POST['user_id'];
    $role = $_POST['role'];

    $allowed_roles = ['customer', 'premium', 'admin'];

    if (!in_array($role, $allowed_roles)) {
        $_SESSION['error'] = 'Ungültige Rolle';
        redirect('/admin.php');
    }

    if ($user_id === $_SESSION['user_id']) {
        $_SESSION['error'] = 'Sie können Ihre eigene Rolle nicht ändern';
        redirect('/admin.php');
    }

    $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");

    if ($stmt->execute([$role, $user_id])) {
        $_SESSION['success'] = 'Rolle aktualisiert';
    }

    redirect('/admin.php');
}
