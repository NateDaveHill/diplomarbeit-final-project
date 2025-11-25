<?php
require_once __DIR__ . '/../Controller/config.php';
require_once __DIR__ . '/../Controller/cart_handler.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';

if ($search) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE name LIKE ? OR description LIKE ? ORDER BY created_at DESC");
    $search_term = "%$search%";
    $stmt->execute([$search_term, $search_term]);
} else {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
}

$products = $stmt->fetchAll();
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Webshop - Home</title>
</head>
<body>
<header>
    <nav class="container">
        <a href="index.php" class="logo">Nate's Online Shop</a>
        <ul class="nav-links">
            <li><a href="index.php">Produkte</a></li>
            <li><a href="cart.php">Warenkorb <?php if (getCartCount() > 0): ?><span class="cart-badge"><?= getCartCount() ?></span><?php endif; ?></a></li>
            <?php if (isLoggedIn()): ?>
            <li><a href="profile.php">Profil</a></li>
            <?php if (isAdmin()): ?>
                <li><a href="admin.php">Admin</a></li>
            <?php endif; ?>
            <li><a href="#" onclick="event.preventDefault(); document.getElementById('logoutForm').submit();">Abmelden</a></li>
            <?php else: ?>
                <li><a href="#" onclick="showLoginModal()">Anmelden</a></li>
                <li><a href="#" onclick="showRegisterModal()">Registrieren</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<?php if (isLoggedIn()): ?>
<form action="POST" action="..?Controller?auth.php" id="logoutForm" style="display: none;">
    <input type="hidden" name="logout" value="1">
</form>
<?php endif; ?>

</body>
</html>
