<?php
require_once 'config/db.php';
require_once 'includes/header.php';

$flowerCategories = $pdo->query("
    SELECT * FROM categories
    WHERE id IN (1, 2)
    ORDER BY id ASC
")->fetchAll(PDO::FETCH_ASSOC);

$giftCategory = $pdo->query("
    SELECT * FROM categories
    WHERE id = 3
    LIMIT 1
")->fetch(PDO::FETCH_ASSOC);

$hitProducts = $pdo->query("
    SELECT * FROM products
    WHERE is_active = 1 AND is_hit = 1
    ORDER BY sort_order ASC
    LIMIT 8
")->fetchAll(PDO::FETCH_ASSOC);

$newProducts = $pdo->query("
    SELECT * FROM products
    WHERE is_active = 1
    ORDER BY id DESC
    LIMIT 4
")->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="hero-banner">
    <div class="container hero-banner-inner">
        <div class="hero-content">
            <span class="hero-label">FLOWER SHOP</span>
            <h1>Свежие цветы и подарки с доставкой</h1>
            <p>
                Авторские букеты, монобукеты, сладкие наборы и подарки для особенных моментов.
                Оформите заказ быстро и удобно.
            </p>
            <div class="hero-actions">
                <a class="btn" href="catalog.php">Перейти в каталог</a>
                <a class="btn btn-outline" href="catalog.php?group=gifts">Смотреть подарки</a>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="home-block">
            <h2 class="section-title">Категории</h2>

            <div class="category-groups">
                <div class="category-group-card">
                    <h3>Цветы</h3>
                    <p>Нежные букеты и стильные монобукеты для любого случая.</p>

                    <div class="category-links">
                        <?php foreach ($flowerCategories as $category): ?>
                            <a href="catalog.php?category=<?= (int)$category['id'] ?>">
                                <?= e($category['name']) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="category-group-card">
                    <h3>Подарки</h3>
                    <p>Сладости, корзины, открытки и приятные дополнения к букету.</p>

                    <div class="category-links">
                        <?php if ($giftCategory): ?>
                            <a href="catalog.php?category=<?= (int)$giftCategory['id'] ?>">
                                <?= e($giftCategory['name']) ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="home-block">
            <h2 class="section-title">Хиты продаж</h2>

            <div class="grid">
                <?php foreach ($hitProducts as $product): ?>
                    <div class="card">
                        <?php if ($product['is_hit']): ?>
                            <div class="badge-hit">ХИТ</div>
                        <?php endif; ?>

                        <a href="product.php?id=<?= (int)$product['id'] ?>">
                            <img src="<?= e(getProductImage($product['image'])) ?>" alt="<?= e($product['name']) ?>">
                        </a>

                        <div class="card-body">
                            <div class="card-title"><?= e($product['name']) ?></div>
                            <div class="price"><?= formatPrice($product['price']) ?></div>
                            <a class="btn" href="add_to_cart.php?id=<?= (int)$product['id'] ?>">В КОРЗИНУ</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="home-features">
            <div class="feature-card">
                <h3>Быстрая доставка</h3>
                <p>Оперативная доставка по городу в удобное для клиента время.</p>
            </div>
            <div class="feature-card">
                <h3>Свежие цветы</h3>
                <p>Только свежие композиции и аккуратное оформление каждого заказа.</p>
            </div>
            <div class="feature-card">
                <h3>Подарки к букету</h3>
                <p>Добавляй сладости, открытки и наборы, чтобы сделать подарок полнее.</p>
            </div>
        </div>

        <div class="home-block">
            <h2 class="section-title">Новинки и интересные предложения</h2>

            <div class="mini-grid">
                <?php foreach ($newProducts as $product): ?>
                    <div class="mini-card">
                        <a href="product.php?id=<?= (int)$product['id'] ?>">
                            <img src="<?= e(getProductImage($product['image'])) ?>" alt="<?= e($product['name']) ?>">
                        </a>
                        <div class="mini-card-body">
                            <h4><?= e($product['name']) ?></h4>
                            <p><?= formatPrice($product['price']) ?></p>
                            <a href="product.php?id=<?= (int)$product['id'] ?>">Подробнее</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>