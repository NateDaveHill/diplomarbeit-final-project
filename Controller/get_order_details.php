<?php
require_once __DIR__ . '/../Controller/config.php';

if (!isLoggedIn()) {
    echo '<p class="text-danger">Bitte melden Sie sich an.</p>';
    exit;
}

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (isAdmin()) {
    $stmt = $pdo->prepare("SELECT o.*, u.username FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ?");
    $stmt->execute([$order_id]);
} else {
    $stmt = $pdo->prepare("SELECT o.*, u.username FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ? AND o.user_id = ?");
    $stmt->execute([$order_id, $_SESSION['user_id']]);
}

$order = $stmt->fetch();

if (!$order) {
    echo '<p class="text-danger">Bestellung nicht gefunden.</p>';
    exit;
}

$stmt = $pdo->prepare("SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll();

$status_labels = [
    'pending' => ['Ausstehend', 'status-pending'],
    'paid' => ['Bezahlt', 'status-paid'],
    'shipped' => ['Versandt', 'status-shipped'],
    'delivered' => ['Zugestellt', 'status-delivered'],
    'cancelled' => ['Storniert', 'status-cancelled']
];
$status_info = $status_labels[$order['status']] ?? ['Unbekannt', 'status-pending'];
?>

<div style="margin-bottom: 1.5rem;">
    <h3>Bestellung #<?= $order['id'] ?></h3>
    <p><strong>Datum:</strong> <?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></p>
    <?php if (isAdmin()): ?>
        <p><strong>Kunde:</strong> <?= htmlspecialchars($order['username']) ?></p>
    <?php endif; ?>
    <p><strong>Status:</strong> <span class="status-badge <?= $status_info[1] ?>"><?= $status_info[0] ?></span></p>
</div>

<h4 style="margin-bottom: 1rem;">Bestellpositionen</h4>
<table style="width: 100%; margin-bottom: 1.5rem;">
    <thead style="background: var(--light);">
        <tr>
            <th style="padding: 0.75rem; text-align: left;">Produkt</th>
            <th style="padding: 0.75rem; text-align: center;">Menge</th>
            <th style="padding: 0.75rem; text-align: right;">Preis</th>
            <th style="padding: 0.75rem; text-align: right;">Gesamt</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($items as $item): ?>
            <tr style="border-bottom: 1px solid var(--light);">
                <td style="padding: 0.75rem;"><?= htmlspecialchars($item['name']) ?></td>
                <td style="padding: 0.75rem; text-align: center;"><?= $item['quantity'] ?></td>
                <td style="padding: 0.75rem; text-align: right;">€<?= number_format($item['price'], 2, ',', '.') ?></td>
                <td style="padding: 0.75rem; text-align: right;">€<?= number_format($item['price'] * $item['quantity'], 2, ',', '.') ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div style="border-top: 2px solid var(--primary); padding-top: 1rem;">
    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
        <span>Zwischensumme:</span>
        <span>€<?= number_format($order['total_amount'], 2, ',', '.') ?></span>
    </div>

    <?php if ($order['discount_amount'] > 0): ?>
        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; color: var(--success);">
            <span>Rabatt:</span>
            <span>-€<?= number_format($order['discount_amount'], 2, ',', '.') ?></span>
        </div>
    <?php endif; ?>

    <div style="display: flex; justify-content: space-between; font-size: 1.25rem; font-weight: 700; color: var(--primary);">
        <span>Gesamt:</span>
        <span>€<?= number_format($order['final_amount'], 2, ',', '.') ?></span>
    </div>
</div>