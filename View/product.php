<?php
require_once __DIR__ . '/../Controller/config.php';
require_once __DIR__ . '/../Controller/cart_handler.php';
require_once __DIR__ . '/../Controller/auth.php';

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    redirect('index.php');
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name']) ?></title>
    <link rel="stylesheet" href="style.css">
    <script src="main.js" defer></script>
</head>
<body>
<header>
    <nav class="container">
        <a href="index.php" class="logo">Nate's Webshop</a>
        <button class="nav-toggle" aria-label="Toggle navigation" onclick="toggleMobileMenu()">
            <span></span>
            <span></span>
            <span></span>
        </button>
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
    <div class="container" style="margin-top: 2rem;">
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

        <a href="index.php" style="display: inline-block; margin-bottom: 1rem; color: var(--secondary); text-decoration: none;">← Zurück</a>

        <div class="product-detail-layout">
            <div>
                <div class="product-image product-detail-image">📦</div>
            </div>

            <div>
                <h1 style="color: var(--primary); margin-bottom: 1rem;"><?= htmlspecialchars($product['name']) ?></h1>

                <div class="product-price" style="margin-bottom: 1.5rem;">€<?= number_format($product['price'], 2, ',', '.') ?></div>

                <div class="product-stock" style="font-size: 1rem; margin-bottom: 2rem;">
                    <?php if ($product['stock'] > 0): ?>
                        <span style="color: var(--success); font-weight: 600;">✓ Auf Lager</span>
                        <span class="text-muted">(<?= $product['stock'] ?> verfügbar)</span>
                    <?php else: ?>
                        <span style="color: var(--accent); font-weight: 600;">✗ Nicht verfügbar</span>
                    <?php endif; ?>
                </div>

                <div style="margin-bottom: 2rem; padding: 1.5rem; background: var(--light); border-radius: 8px;">
                    <h3 style="margin-bottom: 1rem; color: var(--dark);">Beschreibung</h3>
                    <p style="line-height: 1.8;"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                </div>

                <?php if ($product['stock'] > 0): ?>
                    <form method="POST" action="index.php">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">

                        <div class="form-group">
                            <label>Menge</label>
                            <input type="number" name="quantity" class="form-control" value="1" min="1" max="<?= $product['stock'] ?>" style="max-width: 150px;">
                        </div>

                        <button type="submit" name="add_to_cart" class="btn btn-primary btn-block">In den Warenkorb</button>
                    </form>
                <?php else: ?>
                    <div class="alert alert-error">
                        Dieses Produkt ist derzeit nicht verfügbar.
                    </div>
                <?php endif; ?>
            </div>
        </div>
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