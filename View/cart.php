<?php
require_once __DIR__ . '/../Controller/config.php';
require_once __DIR__ . '/../Controller/auth.php';
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
    <script src="main.js" defer></script>
</head>
<body>
    <header>
        <nav class="container">
            <a href="index.php" class="logo">🛒 Webshop</a>
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
                <h1>Warenkorb</h1>
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

            <?php if (empty($_SESSION['cart'])): ?>
                <div class="text-center" style="padding: 3rem; background: white; border-radius: 12px; box-shadow: 0 4px 15px var(--shadow);">
                    <h2>Ihr Warenkorb ist leer</h2>
                    <p class="text-muted">Fügen Sie Produkte hinzu, um mit dem Einkauf zu beginnen.</p>
                    <a href="index.php" class="btn btn-primary mt-3">Produkte ansehen</a>
                </div>
            <?php else: ?>
                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
                    <div>
                        <form method="POST" action="cart.php">
                            <?php foreach ($_SESSION['cart'] as $item): ?>
                                <div class="cart-item">
                                    <div class="cart-item-image">📦</div>
                                    <div class="cart-item-details">
                                        <h3><?= htmlspecialchars($item['name']) ?></h3>
                                        <div class="cart-item-price">€<?= number_format($item['price'], 2, ',', '.') ?></div>
                                        <div class="quantity-control mt-2">
                                            <label>Menge:</label>
                                            <input type="number" name="quantity[<?= $item['id'] ?>]" value="<?= $item['quantity'] ?>" min="0" max="99">
                                        </div>
                                    </div>
                                    <div style="text-align: right;">
                                        <div style="font-size: 1.25rem; font-weight: 700; color: var(--primary); margin-bottom: 1rem;">
                                            €<?= number_format($item['price'] * $item['quantity'], 2, ',', '.') ?>
                                        </div>
                                        <a href="cart.php?remove_from_cart=<?= $item['id'] ?>" class="btn btn-danger btn-small" onclick="return confirm('Wirklich entfernen?')">Entfernen</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                            <div style="margin-top: 1rem;">
                                <button type="submit" name="update_cart" class="btn btn-secondary">Warenkorb aktualisieren</button>
                            </div>
                        </form>
                    </div>

                    <div>
                        <div class="cart-summary">
                            <h2 style="margin-bottom: 1.5rem; color: var(--primary);">Zusammenfassung</h2>

                            <div class="summary-row">
                                <span>Zwischensumme:</span>
                                <span>€<?= number_format($cart_total, 2, ',', '.') ?></span>
                            </div>

                            <?php if (isPremium()): ?>
                                <div class="summary-row" style="color: var(--success);">
                                    <span>Premium-Rabatt (10%):</span>
                                    <span>-€<?= number_format($discount, 2, ',', '.') ?></span>
                                </div>
                            <?php endif; ?>

                            <div class="summary-row">
                                <span><strong>Gesamt:</strong></span>
                                <span><strong>€<?= number_format($final_total, 2, ',', '.') ?></strong></span>
                            </div>

                            <?php if (isLoggedIn()): ?>
                                <div class="alert alert-success" style="margin-top: 1rem; font-size: 0.9rem;">
                                    Sie erhalten <?= floor($final_total) ?> Bonuspunkte
                                </div>
                            <?php endif; ?>

                            <?php if (isLoggedIn()): ?>
                                <form method="POST" action="cart.php">
                                    <button type="submit" name="checkout" class="btn btn-success btn-block mt-3">Jetzt bestellen</button>
                                </form>
                            <?php else: ?>
                                <div class="alert alert-error" style="margin-top: 1rem;">
                                    Bitte melden Sie sich an, um zu bestellen.
                                </div>
                                <button onclick="showLoginModal()" class="btn btn-primary btn-block">Jetzt anmelden</button>
                            <?php endif; ?>

                            <a href="index.php" class="btn btn-secondary btn-block mt-2">Weiter einkaufen</a>
                        </div>
                    </div>
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
            <form method="POST" action="cart.php">
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
            <form method="POST" action="cart.php">
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
