<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/db.php';
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

ensureCart();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    redirect('catalog.php');
}

$stmt = $pdo->prepare("SELECT id FROM products WHERE id = ? AND is_active = 1");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    redirect('catalog.php');
}

if (isset($_SESSION['cart'][$id])) {
    $_SESSION['cart'][$id] += 1;
} else {
    $_SESSION['cart'][$id] = 1;
}

redirect('cart.php');