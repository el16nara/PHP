<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isLoggedIn()) {
    redirect('login.php');
}

$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$back = trim($_GET['back'] ?? 'catalog.php');

if ($productId <= 0) {
    redirect('catalog.php');
}

$stmt = $pdo->prepare("SELECT id FROM products WHERE id = ? AND is_active = 1 LIMIT 1");
$stmt->execute([$productId]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    redirect('catalog.php');
}

$userId = $_SESSION['user']['id'];

if (isFavorite($pdo, $userId, $productId)) {
    $delete = $pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND product_id = ?");
    $delete->execute([$userId, $productId]);
} else {
    $insert = $pdo->prepare("INSERT INTO favorites (user_id, product_id) VALUES (?, ?)");
    $insert->execute([$userId, $productId]);
}

redirect($back);