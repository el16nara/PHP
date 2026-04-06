<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/functions.php';

$isNested = strpos($_SERVER['PHP_SELF'], '/admin/') !== false
    || strpos($_SERVER['PHP_SELF'], '/logs/') !== false;

$basePath = $isNested ? '../' : '';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flower Shop</title>
    <link rel="stylesheet" href="<?= $basePath ?>assets/css/style.css">
</head>
<body>

<header class="topbar">
    <div class="container topbar-inner">
        <a href="<?= $basePath ?>index.php" class="logo">FLOWER SHOP</a>

        <nav class="nav">
            <a href="<?= $basePath ?>index.php">Главная</a>
            <a href="<?= $basePath ?>catalog.php">Каталог</a>
            <a href="<?= $basePath ?>favorites.php">Избранное</a>
            <a href="<?= $basePath ?>cart.php">Корзина (<?= cartCount() ?>)</a>

            <?php if (isLoggedIn()): ?>
                <a href="<?= $basePath ?>account.php">Личный кабинет</a>
                <?php if (isModeratorOrAdmin()): ?>
                    <a href="<?= $basePath ?>logs/index.php">Журнал</a>
                <?php endif; ?>
                <?php if (isAdmin()): ?>
                    <a href="<?= $basePath ?>admin/index.php">Админка</a>
                <?php endif; ?>
                <a href="<?= $basePath ?>logout.php">Выход</a>
            <?php else: ?>
                <a href="<?= $basePath ?>login.php">Вход</a>
                <a href="<?= $basePath ?>register.php">Регистрация</a>
            <?php endif; ?>
        </nav>
    </div>
</header>