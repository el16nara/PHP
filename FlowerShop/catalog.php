<?php
require_once 'config/db.php';
require_once 'includes/header.php';

$categoryId = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$search = trim($_GET['search'] ?? '');
$group = trim($_GET['group'] ?? '');

$sql = "SELECT * FROM products WHERE is_active = 1";
$params = [];

if ($categoryId > 0) {
    $sql .= " AND category_id = ?";
    $params[] = $categoryId;
}

if ($group === 'flowers') {
    $sql .= " AND category_id IN (1, 2)";
}

if ($group === 'gifts') {
    $sql .= " AND category_id = 3";
}

if ($search !== '') {
    $sql .= " AND (name LIKE ? OR description LIKE ? OR composition LIKE ?)";
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
}

$sql .= " ORDER BY sort_order ASC, id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$categories = $pdo->query("SELECT * FROM categories ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container hero">
    <h1 class="page-title">Каталог</h1>

    <form method="get" class="catalog-search-form">
        <input
            type="text"
            name="search"
            placeholder="Поиск по названию, описанию или составу"
            value="<?= e($search) ?>"
        >

        <select name="category">
            <option value="0">Все категории</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= (int)$category['id'] ?>" <?= $categoryId === (int)$category['id'] ? 'selected' : '' ?>>
                    <?= e($category['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="group">
            <option value="">Все разделы</option>
            <option value="flowers" <?= $group === 'flowers' ? 'selected' : '' ?>>Цветы</option>
            <option value="gifts" <?= $group === 'gifts' ? 'selected' : '' ?>>Подарки</option>
        </select>

        <button class="btn" type="submit">Найти</button>
    </form>
</div>

<section class="section">
    <div class="container">
        <?php if (!$products): ?>
            <div class="alert alert-error">По вашему запросу ничего не найдено.</div>
        <?php else: ?>
            <div class="grid">
                <?php foreach ($products as $product): ?>
                    <div class="card">
                        <?php if ($product['is_hit']): ?>
                            <div class="badge-hit">ХИТ</div>
                        <?php endif; ?>

                        <?php if (isLoggedIn()): ?>
                            <?php $favorite = isFavorite($pdo, $_SESSION['user']['id'], $product['id']); ?>
                            <a
                                class="favorite-btn <?= $favorite ? 'active' : '' ?>"
                                href="favorite_toggle.php?id=<?= (int)$product['id'] ?>&back=<?= urlencode($_SERVER['REQUEST_URI']) ?>"
                                title="<?= $favorite ? 'Убрать из избранного' : 'Добавить в избранное' ?>"
                            >♥</a>
                        <?php else: ?>
                            <a class="favorite-btn" href="login.php" title="Войдите, чтобы добавить в избранное">♥</a>
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
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>