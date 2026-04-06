<?php
require_once 'config/db.php';
require_once 'includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("
    SELECT p.*, c.name AS category_name
    FROM products p
    LEFT JOIN categories c ON c.id = p.category_id
    WHERE p.id = ? AND p.is_active = 1
");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo '<div class="container section"><div class="alert alert-error">Товар не найден.</div></div>';
    require_once 'includes/footer.php';
    exit;
}

$isFav = false;
if (isLoggedIn()) {
    $isFav = isFavorite($pdo, $_SESSION['user']['id'], $product['id']);
}
?>

<section class="section">
    <div class="container">
        <div class="product-page">
            <div style="position:relative;">
                <?php if (isLoggedIn()): ?>
                    <a
                        class="favorite-btn <?= $isFav ? 'active' : '' ?>"
                        href="favorite_toggle.php?id=<?= (int)$product['id'] ?>&back=<?= urlencode($_SERVER['REQUEST_URI']) ?>"
                        title="<?= $isFav ? 'Убрать из избранного' : 'Добавить в избранное' ?>"
                    >♥</a>
                <?php else: ?>
                    <a class="favorite-btn" href="login.php" title="Войдите, чтобы добавить в избранное">♥</a>
                <?php endif; ?>

                <img src="<?= e(getProductImage($product['image'])) ?>" alt="<?= e($product['name']) ?>">
            </div>

            <div>
                <h1><?= e($product['name']) ?></h1>
                <p><strong>Категория:</strong> <?= e($product['category_name'] ?? 'Без категории') ?></p>
                <p style="font-size: 26px; font-weight: 700;"><?= formatPrice($product['price']) ?></p>

                <?php if (!empty($product['description'])): ?>
                    <p><strong>Описание:</strong><br><?= nl2br(e($product['description'])) ?></p>
                <?php endif; ?>

                <?php if (!empty($product['composition'])): ?>
                    <p><strong>Состав:</strong><br><?= nl2br(e($product['composition'])) ?></p>
                <?php endif; ?>

                <div class="actions" style="margin-top:24px;">
                    <a class="btn" href="add_to_cart.php?id=<?= (int)$product['id'] ?>">В КОРЗИНУ</a>

                    <?php if (isLoggedIn()): ?>
                        <a
                            class="btn btn-outline"
                            href="favorite_toggle.php?id=<?= (int)$product['id'] ?>&back=<?= urlencode($_SERVER['REQUEST_URI']) ?>"
                        >
                            <?= $isFav ? 'Убрать из избранного' : 'В избранное' ?>
                        </a>
                    <?php else: ?>
                        <a class="btn btn-outline" href="login.php">В избранное</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>