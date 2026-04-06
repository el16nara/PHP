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

$error = '';
$categories = $pdo->query("SELECT * FROM categories ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = (int)($_POST['category_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $composition = trim($_POST['composition'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $image = trim($_POST['image'] ?? '');
    $stock = (int)($_POST['stock'] ?? 100);
    $is_hit = isset($_POST['is_hit']) ? 1 : 0;
    $is_new = isset($_POST['is_new']) ? 1 : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $sort_order = (int)($_POST['sort_order'] ?? 0);

    if ($name === '' || $price <= 0 || $image === '') {
        $error = 'Заполните обязательные поля.';
    } else {
        if ($slug === '') {
            $slug = slugify($name);
        }

        $check = $pdo->prepare("SELECT id FROM products WHERE slug = ?");
        $check->execute([$slug]);

        if ($check->fetch()) {
            $slug .= '-' . time();
        }

        $stmt = $pdo->prepare("
            INSERT INTO products
            (category_id, name, slug, description, composition, price, image, stock, is_hit, is_new, is_active, sort_order)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $category_id ?: null,
            $name,
            $slug,
            $description,
            $composition,
            $price,
            $image,
            $stock,
            $is_hit,
            $is_new,
            $is_active,
            $sort_order
        ]);

        redirect('products.php');
    }
}
?>

<section class="section">
    <div class="container">
        <h1 class="page-title">Добавить товар</h1>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= e($error) ?></div>
        <?php endif; ?>

        <div class="form-box" style="max-width:800px;">
            <form method="post">
                <div class="form-group">
                    <label>Категория</label>
                    <select name="category_id">
                        <option value="0">Без категории</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= (int)$category['id'] ?>"><?= e($category['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Название</label>
                    <input type="text" name="name" required>
                </div>

                <div class="form-group">
                    <label>Slug</label>
                    <input type="text" name="slug">
                </div>

                <div class="form-group">
                    <label>Описание</label>
                    <textarea name="description"></textarea>
                </div>

                <div class="form-group">
                    <label>Состав</label>
                    <textarea name="composition"></textarea>
                </div>

                <div class="form-group">
                    <label>Цена</label>
                    <input type="number" step="0.01" name="price" required>
                </div>

                <div class="form-group">
                    <label>Имя файла картинки без расширения</label>
                    <input type="text" name="image" required>
                </div>

                <div class="form-group">
                    <label>Остаток</label>
                    <input type="number" name="stock" value="100">
                </div>

                <div class="form-group">
                    <label>Порядок сортировки</label>
                    <input type="number" name="sort_order" value="0">
                </div>

                <div class="form-group">
                    <label><input type="checkbox" name="is_hit"> Хит</label>
                    <label><input type="checkbox" name="is_new"> Новый</label>
                    <label><input type="checkbox" name="is_active" checked> Активен</label>
                </div>

                <button class="btn" type="submit">Сохранить</button>
            </form>
        </div>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>