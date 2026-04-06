<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isLoggedIn()) {
    redirect('login.php');
}

$search = trim($_GET['search'] ?? '');
$status = trim($_GET['status'] ?? '');

$sql = "SELECT * FROM orders WHERE user_id = ?";
$params = [$_SESSION['user']['id']];

if ($search !== '') {
    $sql .= " AND (customer_name LIKE ? OR customer_phone LIKE ? OR customer_email LIKE ?)";
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
}

if ($status !== '') {
    $sql .= " AND status = ?";
    $params[] = $status;
}

$sql .= " ORDER BY id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once 'includes/header.php';
?>

<section class="section">
    <div class="container">
        <h1 class="page-title">Личный кабинет</h1>

        <div class="form-box" style="max-width:100%; margin-bottom:20px;">
            <p><strong>Имя:</strong> <?= e($_SESSION['user']['name']) ?></p>
            <p><strong>Email:</strong> <?= e($_SESSION['user']['email']) ?></p>
            <p><strong>Роль:</strong> <?= e($_SESSION['user']['role']) ?></p>
        </div>

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

            <button class="btn" type="submit">Применить</button>
        </form>

        <h2>Мои заказы</h2>

        <?php if (!$orders): ?>
            <div class="alert alert-error">У вас пока нет заказов.</div>
        <?php else: ?>
            <table class="admin-table">
                <tr>
                    <th>ID</th>
                    <th>Дата</th>
                    <th>Статус</th>
                    <th>Сумма</th>
                    <th>Адрес</th>
                    <th>Файл</th>
                </tr>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td>#<?= (int)$order['id'] ?></td>
                        <td><?= e($order['created_at']) ?></td>
                        <td>
                            <span class="status-badge status-<?= e($order['status']) ?>">
                                <?= e(orderStatusLabel($order['status'])) ?>
                            </span>
                        </td>
                        <td><?= formatPrice($order['total']) ?></td>
                        <td><?= e($order['delivery_address']) ?></td>
                        <td>
                            <?php if (!empty($order['attachment'])): ?>
                                <a href="uploads/<?= e($order['attachment']) ?>" download>Скачать</a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>