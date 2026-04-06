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

$stmt = $pdo->query("
    SELECT p.*, c.name AS category_name
    FROM products p
    LEFT JOIN categories c ON c.id = p.category_id
    ORDER BY p.sort_order ASC, p.id ASC
");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="section">
    <div class="container">
        <h1 class="page-title">Управление товарами</h1>

        <div class="admin-menu">
            <a href="index.php">Админка</a>
            <a href="product_create.php">Добавить товар</a>
            <a href="orders.php">Заказы</a>
        </div>

        <table class="admin-table">
            <tr>
                <th>ID</th>
                <th>Фото</th>
                <th>Название</th>
                <th>Категория</th>
                <th>Цена</th>
                <th>Файл</th>
                <th>Хит</th>
                <th>Действия</th>
            </tr>

            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?= (int)$product['id'] ?></td>
                    <td><img src="<?= e(getProductImage($product['image'])) ?>" alt="" style="width:80px;height:80px;object-fit:cover;"></td>
                    <td><?= e($product['name']) ?></td>
                    <td><?= e($product['category_name'] ?? '-') ?></td>
                    <td><?= formatPrice($product['price']) ?></td>
                    <td><?= e($product['image']) ?></td>
                    <td><?= $product['is_hit'] ? 'Да' : 'Нет' ?></td>
                    <td>
                        <div class="actions">
                            <a class="small-btn blue" href="product_edit.php?id=<?= (int)$product['id'] ?>">Редактировать</a>
                            <a class="small-btn danger" href="product_delete.php?id=<?= (int)$product['id'] ?>" onclick="return confirm('Удалить товар?')">Удалить</a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>