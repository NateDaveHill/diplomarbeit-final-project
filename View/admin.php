<?php
require_once __DIR__ . '/../Controller/config.php';
require_once __DIR__ . '/../Controller/cart_handler.php';
require_once __DIR__ . '/../Controller/admin_handler.php';

$products = $pdo->query("SELECT * FROM products ORDER BY created_at DESC")->fetchAll();
$orders = $pdo->query("SELECT o.*, u.username FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 20")->fetchAll();
$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin Dashboard - Webshop</title>
    <link rel="stylesheet" href="style.css">
    <script src="main.js" defer></script>
</head>
<body>
<header>
    <nav class="container">
        <a href="index.php" class="logo">Nate's Online Shop</a>
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

<form method="POST" action="index.php" id="logoutForm" style="display: none;">
    <input type="hidden" name="logout" value="1">
</form>

<main>
    <div class="page-header">
        <div class="container">
            <h1>Admin Dashboard</h1>
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

        <div class="admin-grid">
            <div class="admin-card">
                <h3>📦 Produkte</h3>
                <p style="font-size: 2rem; font-weight: 700; color: var(--primary);"><?= count($products) ?></p>
            </div>
            <div class="admin-card">
                <h3>🛒 Bestellungen</h3>
                <p style="font-size: 2rem; font-weight: 700; color: var(--secondary);"><?= count($orders) ?></p>
            </div>
            <div class="admin-card">
                <h3>👥 Benutzer</h3>
                <p style="font-size: 2rem; font-weight: 700; color: var(--success);"><?= count($users) ?></p>
            </div>
        </div>

        <div class="profile-section">
            <div class="admin-header-controls">
                <h2>Produktverwaltung</h2>
                <button onclick="showAddProductModal()" class="btn btn-primary">Produkt hinzufügen</button>
            </div>

            <div class="table-container">
                <div class="table">
                    <table style="width: 100%;">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Preis</th>
                            <th>Lagerbestand</th>
                            <th>Aktionen</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td>#<?= $product['id'] ?></td>
                                <td><?= htmlspecialchars($product['name']) ?></td>
                                <td>€<?= number_format($product['price'], 2, ',', '.') ?></td>
                                <td><?= $product['stock'] ?></td>
                                <td>
                                    <button onclick='editProduct(<?= json_encode($product) ?>)' class="btn btn-secondary btn-small">Bearbeiten</button>
                                    <button onclick="deleteProduct(<?= $product['id'] ?>)" class="btn btn-danger btn-small">Löschen</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="profile-section">
            <h2>Bestellverwaltung</h2>

            <div class="table-container">
                <div class="table">
                    <table style="width: 100%;">
                        <thead>
                        <tr>
                            <th>Bestellung #</th>
                            <th>Kunde</th>
                            <th>Datum</th>
                            <th>Betrag</th>
                            <th>Status</th>
                            <th>Aktionen</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>#<?= $order['id'] ?></td>
                                <td><?= htmlspecialchars($order['username']) ?></td>
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
                                    <button onclick='updateOrderStatus(<?= $order['id'] ?>, "<?= $order['status'] ?>")' class="btn btn-secondary btn-small">Status ändern</button>
                                    <button onclick="showOrderDetails(<?= $order['id'] ?>)" class="btn btn-primary btn-small">Details</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="profile-section">
            <h2>Benutzerverwaltung</h2>

            <div class="table-container">
                <div class="table">
                    <table style="width: 100%;">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Benutzername</th>
                            <th>E-Mail</th>
                            <th>Rolle</th>
                            <th>Bonuspunkte</th>
                            <th>Aktionen</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td>#<?= $user['id'] ?></td>
                                <td><?= htmlspecialchars($user['username']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td>
                                    <?php
                                    $role_badges = [
                                        'customer' => ['Kunde', 'badge-customer'],
                                        'premium' => ['Premium', 'badge-premium'],
                                        'admin' => ['Admin', 'badge-admin']
                                    ];
                                    $role_info = $role_badges[$user['role']] ?? ['Gast', 'badge-customer'];
                                    ?>
                                    <span class="user-badge <?= $role_info[1] ?>"><?= $role_info[0] ?></span>
                                </td>
                                <td><?= $user['bonus_points'] ?></td>
                                <td>
                                    <?php if ($user['id'] !== $_SESSION['user_id']): ?>
                                        <button onclick='updateUserRole(<?= $user['id'] ?>, "<?= $user['role'] ?>", "<?= htmlspecialchars($user['username']) ?>")' class="btn btn-secondary btn-small">Rolle ändern</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<footer>
    <div class="container">
        <p>&copy; 2025 Webshop. Alle Rechte vorbehalten.</p>
    </div>
</footer>

<div id="productModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="productModalTitle">Produkt hinzufügen</h2>
            <span class="modal-close" onclick="closeProductModal()">&times;</span>
        </div>
        <form method="POST" action="admin.php" id="productForm">
            <input type="hidden" name="id" id="product-id">
            <div class="form-group">
                <label>Produktname</label>
                <input type="text" id="product-name" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Beschreibung</label>
                <textarea id="product-description" name="description" class="form-control"></textarea>
            </div>
            <div class="form-group">
                <label>Preis (€)</label>
                <input type="number" id="product-price" name="price" class="form-control" step="0.01" min="0" required>
            </div>
            <div class="form-group">
                <label>Lagerbestand</label>
                <input type="number" id="product-stock" name="stock" class="form-control" min="0" required>
            </div>
            <div class="form-group">
                <label>Bild</label>
                <input type="text" id="product-image" name="image" class="form-control" placeholder="z.B. product.jpg">
            </div>
            <button type="submit" name="add_product" id="productSubmitBtn" class="btn btn-primary btn-block">Hinzufügen</button>
        </form>
    </div>
</div>

<div id="statusModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Bestellstatus ändern</h2>
            <span class="modal-close" onclick="closeStatusModal()">&times;</span>
        </div>
        <form method="POST" action="admin.php">
            <input type="hidden" name="order_id" id="status-order-id">
            <div class="form-group">
                <label>Neuer Status</label>
                <select id="order-status" name="status" class="form-control">
                    <option value="pending">Ausstehend</option>
                    <option value="paid">Bezahlt</option>
                    <option value="shipped">Versandt</option>
                    <option value="delivered">Zugestellt</option>
                    <option value="cancelled">Storniert</option>
                </select>
            </div>
            <button type="submit" name="update_order_status" class="btn btn-primary btn-block">Status aktualisieren</button>
        </form>
    </div>
</div>

<div id="roleModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Benutzerrolle ändern</h2>
            <span class="modal-close" onclick="closeRoleModal()">&times;</span>
        </div>
        <form method="POST" action="admin.php">
            <input type="hidden" name="user_id" id="role-user-id">
            <p>Benutzer: <strong id="role-username"></strong></p>
            <div class="form-group">
                <label>Neue Rolle</label>
                <select id="user-role" name="role" class="form-control">
                    <option value="customer">Kunde</option>
                    <option value="premium">Premium-Kunde</option>
                    <option value="admin">Administrator</option>
                </select>
            </div>
            <button type="submit" name="update_user_role" class="btn btn-primary btn-block">Rolle aktualisieren</button>
        </form>
    </div>
</div>

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

<form method="POST" action="admin.php" id="deleteForm" style="display: none;">
    <input type="hidden" name="delete_product" id="delete-product-id">
</form>
</body>
</html>
