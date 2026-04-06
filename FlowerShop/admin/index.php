<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isAdmin()) {
    redirect('../login.php');
}

require_once '../includes/header.php';
?>

<section class="section">
    <div class="container">
        <h1 class="page-title">Админ-панель</h1>

        <div class="admin-menu">
            <a href="products.php">Товары</a>
            <a href="orders.php">Заказы</a>
            <a href="../index.php">На сайт</a>
        </div>

        <div class="form-box" style="max-width:100%;">
            <p>Добро пожаловать в админ-панель.</p>
        </div>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>