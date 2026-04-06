<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ensureCart();

require_once 'includes/header.php';

$items = [];
$total = 0;

if (!empty($_SESSION['cart'])) {
    $ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders) AND is_active = 1");
    $stmt->execute($ids);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($items as $item) {
        $qty = $_SESSION['cart'][$item['id']] ?? 0;
        $total += $item['price'] * $qty;
    }
}
?>

<section class="section">
    <div class="container">
        <h1 class="page-title">Корзина</h1>

        <!-- КНОПКА КАТАЛОГА -->
        <div style="margin-bottom:20px;">
            <a class="btn btn-outline" href="catalog.php">← Вернуться в каталог</a>
        </div>

        <?php if (!isLoggedIn()): ?>
            <div class="alert alert-error">
                Добавлять товары и оформлять заказ можно только после входа.
            </div>
            <p><a class="btn" href="login.php">Войти</a></p>

        <?php elseif (empty($items)): ?>
            <div class="alert alert-error">Корзина пуста.</div>

        <?php else: ?>
            <table class="cart-table">
                <tr>
                    <th>Фото</th>
                    <th>Товар</th>
                    <th>Цена</th>
                    <th>Количество</th>
                    <th>Сумма</th>
                    <th></th>
                </tr>

                <?php foreach ($items as $item): ?>
                    <?php $qty = $_SESSION['cart'][$item['id']]; ?>
                    <tr>
                        <td style="width:120px;">
                            <img src="<?= e(getProductImage($item['image'])) ?>" style="width:100px;height:100px;object-fit:cover;">
                        </td>
                        <td><?= e($item['name']) ?></td>
                        <td><?= formatPrice($item['price']) ?></td>
                        <td><?= (int)$qty ?></td>
                        <td><?= formatPrice($item['price'] * $qty) ?></td>
                        <td>
                            <a class="small-btn danger" href="remove_from_cart.php?id=<?= (int)$item['id'] ?>">Удалить</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>

            <div style="margin-top: 20px; font-size: 24px; font-weight: 700;">
                Итого: <?= formatPrice($total) ?>
            </div>

            <div style="margin-top: 20px; display:flex; gap:12px;">
                <a class="btn btn-outline" href="catalog.php">Продолжить покупки</a>
                <a class="btn" href="checkout.php">Оформить заказ</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>