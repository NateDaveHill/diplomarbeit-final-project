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

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mein Profil - Webshop</title>
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
            <li><a href="profile.php">Profil</a></li>
            <?php if (isAdmin()): ?>
                <li><a href="admin.php">Admin</a></li>
            <?php endif; ?>
            <li><a href="#" onclick="event.preventDefault(); document.getElementById('logoutForm').submit();">Abmelden</a></li>
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
            <h1>Mein Profil</h1>
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

        <div class="profile-section">
            <h2>Benutzerdaten</h2>
            <div class="profile-grid">
                <div>
                    <strong>Benutzername:</strong>
                    <p><?= htmlspecialchars($user['username']) ?></p>
                </div>
                <div>
                    <strong>E-Mail:</strong>
                    <p><?= htmlspecialchars($user['email']) ?></p>
                </div>
                <div>
                    <strong>Rolle:</strong>
                    <p>
                        <?php
                        $role_badges = [
                            'customer' => ['Kunde', 'badge-customer'],
                            'premium' => ['Premium-Kunde', 'badge-premium'],
                            'admin' => ['Administrator', 'badge-admin']
                        ];
                        $role_info = $role_badges[$user['role']] ?? ['Gast', 'badge-customer'];
                        ?>
                        <span class="user-badge <?= $role_info[1] ?>"><?= $role_info[0] ?></span>
                    </p>
                </div>
                <div>
                    <strong>Bonuspunkte:</strong>
                    <p style="font-size: 1.25rem; color: var(--success); font-weight: 700;"><?= $user['bonus_points'] ?> Punkte</p>
                </div>
                <div>
                    <strong>Mitglied seit:</strong>
                    <p><?= date('d.m.Y', strtotime($user['created_at'])) ?></p>
                </div>
            </div>
        </div>

        <div class="profile-section">
            <h2>Passwort ändern</h2>
            <form method="POST" action="../Controller/auth.php" style="max-width: 500px;">
                <div class="form-group">
                    <label>Aktuelles Passwort</label>
                    <input type="password" name="old_password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Neues Passwort</label>
                    <input type="password" name="new_password" class="form-control" required minlength="8">
                </div>
                <div class="form-group">
                    <label>Neues Passwort bestätigen</label>
                    <input type="password" name="new_password_confirm" class="form-control" required>
                </div>
                <button type="submit" name="change_password" class="btn btn-primary">Passwort ändern</button>
            </form>
        </div>

        <div class="profile-section">
            <h2>Bestellhistorie</h2>

            <?php if (empty($orders)): ?>
                <p class="text-muted">Sie haben noch keine Bestellungen aufgegeben.</p>
                <a href="index.php" class="btn btn-primary mt-2">Jetzt einkaufen</a>
            <?php else: ?>
                <div class="table-container">
                    <div class="table">
                        <table style="width: 100%;">
                            <thead>
                            <tr>
                                <th>Bestellung #</th>
                                <th>Datum</th>
                                <th>Betrag</th>
                                <th>Status</th>
                                <th>Details</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td>#<?= $order['id'] ?></td>
                                    <td><?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></td>
                                    <td>€<?= number_format($order['final_amount'], 2, ',', '.') ?></td>
                                    <td>
                                        <?php
                                        $status_labels = [
                                            'pending' => ['Ausstehend', 'status-pending'],
                                            'paid' => ['Bezahlt', 'status-paid'],
                                            'shipped' => ['Versandt', 'status-shipped'],
                                            'delivered' => ['Zugestellt', 'status-delivered'],
                                            'cancelled' => ['Storniert', 'status-cancelled']
                                        ];
                                        $status_info = $status_labels[$order['status']] ?? ['Unbekannt', 'status-pending'];
                                        ?>
                                        <span class="status-badge <?= $status_info[1] ?>"><?= $status_info[0] ?></span>
                                    </td>
                                    <td>
                                        <button onclick="showOrderDetails(<?= $order['id'] ?>)" class="btn btn-secondary btn-small">Details</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<footer>
    <div class="container">
        <p>&copy; 2025 Webshop. Alle Rechte vorbehalten.</p>
    </div>
</footer>

<div id="orderModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Bestelldetails</h2>
            <span class="modal-close" onclick="closeOrderModal()">&times;</span>
        </div>
        <div id="orderDetailsContent">
            <p>Lädt...</p>
        </div>
    </div>
</div>


</body>
</html>