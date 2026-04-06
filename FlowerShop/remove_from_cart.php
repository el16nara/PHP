<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'includes/functions.php';

ensureCart();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0 && isset($_SESSION['cart'][$id])) {
    unset($_SESSION['cart'][$id]);
}

redirect('cart.php');