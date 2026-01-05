<?php
require_once __DIR__ . '/../Controller/config.php';
require_once __DIR__ . '/../Controller/auth.php';
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Webshop - Home</title>
    <link rel="stylesheet" href="style.css">
    <script src="main.js" defer></script>
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
<form method="POST" action="index.php" id="logoutForm" style="display: none;">
    <input type="hidden" name="logout" value="1">
</form>
<?php endif; ?>

<main>
    <div class="page-header">
        <div class="container">
            <h1>Unsere Produkte</h1>
            <form method="GET" style="margin-top: 1rem;">
                <div class="search-form">
                    <input type="text" name="search" class="form-control" placeholder="Produkte suchen..." value="<?= htmlspecialchars($search) ?>">
                   <button type="submit" class="btn btn-primary">Suchen</button>
                </div>
            </form>
        </div>
    </div>

    <div class="container">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                ✓ <?= $_SESSION['success'] ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                ✗ <?= $_SESSION['error'] ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (empty($products)): ?>
            <div class="text-center" style="padding: 3rem;">
                <h2>Keine Produkte gefunden</h2>
                <?php if ($search): ?>
                    <a href="index.php" class="btn btn-primary mt-2">Alle Produkte anzeigen</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="products-grid">
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <div class="product-image">📦</div>
                        <div class="product-info">
                            <h3 class="product-name"><?= htmlspecialchars($product['name']) ?></h3>
                            <p class="product-description"><?= htmlspecialchars($product['description']) ?></p>
                            <div class="product-price">€<?= number_format($product['price'], 2, ',', '.') ?></div>
                            <div class="product-stock">
                                <?php if ($product['stock'] > 0): ?>
                                    ✓ Auf Lager (<?= $product['stock'] ?>)
                                <?php else: ?>
                                    <span class="text-danger">✗ Nicht verfügbar</span>
                                <?php endif; ?>
                            </div>
                            <div class="product-actions">
                                <a href="product.php?id=<?= $product['id'] ?>" class="btn btn-secondary btn-small">Details</a>
                                <?php if ($product['stock'] > 0): ?>
                                    <form method="POST" action="index.php" style="flex: 1;">
                                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" name="add_to_cart" class="btn btn-primary btn-small btn-block">In Warenkorb</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<footer>
    <div class="container">
        <p>&copy; 2025 Webshop. Alle Rechte vorbehalten.</p>
    </div>
</footer>

<!-- Login Modal -->
<div id="loginModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Anmelden</h2>
            <span class="modal-close" onclick="closeLoginModal()">&times;</span>
        </div>
        <form method="POST" action="index.php">
            <div class="form-group">
                <label>Benutzername oder E-Mail</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Passwort</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" name="login" class="btn btn-primary btn-block">Anmelden</button>
            <p class="text-center mt-2">
                <a href="#" onclick="closeLoginModal(); showRegisterModal();">Noch kein Konto? Registrieren</a>
            </p>
        </form>
    </div>
</div>

<!-- Register Modal -->
<div id="registerModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Registrieren</h2>
            <span class="modal-close" onclick="closeRegisterModal()">&times;</span>
        </div>
        <form method="POST" action="index.php">
            <div class="form-group">
                <label>Benutzername</label>
                <input type="text" name="username" class="form-control" required minlength="3">
            </div>
            <div class="form-group">
                <label>E-Mail</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Passwort</label>
                <input type="password" name="password" class="form-control" required minlength="8">
            </div>
            <div class="form-group">
                <label>Passwort bestätigen</label>
                <input type="password" name="password_confirm" class="form-control" required>
            </div>
            <button type="submit" name="register" class="btn btn-primary btn-block">Registrieren</button>
        </form>
    </div>
</div>

</body>
</html>
