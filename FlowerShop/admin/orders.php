<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isModeratorOrAdmin()) {
    redirect('../login.php');
}

$search = trim($_GET['search'] ?? '');
$status = trim($_GET['status'] ?? '');
$userId = trim($_GET['user_id'] ?? '');

$sql = "SELECT o.*, u.name AS user_name FROM orders o LEFT JOIN users u ON u.id = o.user_id WHERE 1=1";
$params = [];

if ($search !== '') {
    $sql .= " AND (o.customer_name LIKE ? OR o.customer_phone LIKE ? OR o.customer_email LIKE ?)";
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
}

if ($status !== '') {
    $sql .= " AND o.status = ?";
    $params[] = $status;
}

if ($userId !== '' && isAdmin()) {
    $sql .= " AND o.user_id = ?";
    $params[] = $userId;
}

$sql .= " ORDER BY o.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$users = $pdo->query("SELECT id, name FROM users ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

require_once '../includes/header.php';
?>

<section class="section">
    <div class="container">
        <h1 class="page-title">Заказы</h1>

        <form method="get" class="admin-menu" style="margin-bottom:20px;">
            <input type="text" name="search" placeholder="Поиск" value="<?= e($search) ?>">

            <select name="status">
                <option value="">Все статусы</option>
                <option value="new" <?= $status === 'new' ? 'selected' : '' ?>>Новый</option>
                <option value="submitted" <?= $status === 'submitted' ? 'selected' : '' ?>>Отправлен</option>
                <option value="in_review" <?= $status === 'in_review' ? 'selected' : '' ?>>На рассмотрении</option>
                <option value="approved" <?= $status === 'approved' ? 'selected' : '' ?>>Одобрен</option>
                <option value="rejected" <?= $status === 'rejected' ? 'selected' : '' ?>>Отклонён</option>
                <option value="completed" <?= $status === 'completed' ? 'selected' : '' ?>>Завершён</option>
                <option value="cancelled" <?= $status === 'cancelled' ? 'selected' : '' ?>>Отменён</option>
            </select>

            <?php if (isAdmin()): ?>
                <select name="user_id">
                    <option value="">Все пользователи</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?= (int)$user['id'] ?>" <?= $userId == $user['id'] ? 'selected' : '' ?>>
                            <?= e($user['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>

            <button class="btn" type="submit">Применить</button>
        </form>

        <table class="admin-table">
            <tr>
                <th>ID</th>
                <th>Клиент</th>
                <th>Телефон</th>
                <th>Пользователь</th>
                <th>Дата</th>
                <th>Статус</th>
                <th>Сумма</th>
                <th>Действия</th>
            </tr>

            <?php foreach ($orders as $order): ?>
                <tr>
                    <td>#<?= (int)$order['id'] ?></td>
                    <td><?= e($order['customer_name']) ?></td>
                    <td><?= e($order['customer_phone']) ?></td>
                    <td><?= e($order['user_name'] ?? '-') ?></td>
                    <td><?= e($order['created_at']) ?></td>
                    <td>
                        <span class="status-badge status-<?= e($order['status']) ?>">
                            <?= e(orderStatusLabel($order['status'])) ?>
                        </span>
                    </td>
                    <td><?= formatPrice($order['total']) ?></td>
                    <td>
                        <a class="small-btn blue" href="order_view.php?id=<?= (int)$order['id'] ?>">Открыть</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>