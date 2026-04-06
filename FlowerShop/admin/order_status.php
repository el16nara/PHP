<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isModeratorOrAdmin()) {
    redirect('../login.php');
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$newStatus = trim($_GET['status'] ?? '');

$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    redirect('orders.php');
}

$allowed = allowedOrderTransitions($_SESSION['user']['role'], $order['status']);

if (!in_array($newStatus, $allowed, true)) {
    redirect('order_view.php?id=' . $id);
}

$update = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
$update->execute([$newStatus, $id]);

logAction(
    $pdo,
    $_SESSION['user']['id'],
    $id,
    'status_change',
    'Статус изменён: ' . $order['status'] . ' -> ' . $newStatus
);

redirect('order_view.php?id=' . $id);