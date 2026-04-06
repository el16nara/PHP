<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isLoggedIn()) {
    redirect('login.php');
}

$stmt = $pdo->prepare("
    SELECT p.*
    FROM favorites f
    INNER JOIN products p ON p.id = f.product_id
    WHERE f.user_id = ? AND p.is_active = 1
    ORDER BY f.id DESC
");
$stmt->execute([$_SESSION['user']['id']]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once 'includes/header.php';
?>

<section class="section">
    <div class="container">
        <h1 class="page-title">Избранное</h1>

        <?php if (!$products): ?>
            <div class="alert alert-error">У вас пока нет избранных товаров.</div>
        <?php else: ?>
            <div class="grid">
                <?php foreach ($products as $product): ?>
                    <div class="card">
                        <?php if ($product['is_hit']): ?>
                            <div class="badge-hit">ХИТ</div>
                        <?php endif; ?>

                        <a class="favorite-btn active" href="favorite_toggle.php?id=<?= (int)$product['id'] ?>&back=<?= urlencode('favorites.php') ?>" title="Убрать из избранного">♥</a>

                        <a href="product.php?id=<?= (int)$product['id'] ?>">
                            <img src="<?= e(getProductImage($product['image'])) ?>" alt="<?= e($product['name']) ?>">
                        </a>

                        <div class="card-body">
                            <div class="card-title"><?= e($product['name']) ?></div>
                            <div class="price"><?= formatPrice($product['price']) ?></div>
                            <div class="actions" style="justify-content:center;">
                                <a class="btn" href="add_to_cart.php?id=<?= (int)$product['id'] ?>">В КОРЗИНУ</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>