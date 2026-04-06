<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isModeratorOrAdmin()) {
    redirect('../login.php');
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT o.*, u.name AS user_name FROM orders o LEFT JOIN users u ON u.id = o.user_id WHERE o.id = ?");
$stmt->execute([$id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    redirect('orders.php');
}

$itemStmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
$itemStmt->execute([$id]);
$items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);

$logStmt = $pdo->prepare("
    SELECT l.*, u.name
    FROM logs l
    LEFT JOIN users u ON u.id = l.user_id
    WHERE l.order_id = ?
    ORDER BY l.id DESC
");
$logStmt->execute([$id]);
$logs = $logStmt->fetchAll(PDO::FETCH_ASSOC);

$availableTransitions = allowedOrderTransitions($_SESSION['user']['role'], $order['status']);

require_once '../includes/header.php';
?>

<section class="section">
    <div class="container">
        <h1 class="page-title">Заказ #<?= (int)$order['id'] ?></h1>

        <div class="admin-menu">
            <a href="orders.php">Назад к заказам</a>
        </div>

        <div class="form-box" style="max-width:100%; margin-bottom:20px;">
            <p><strong>Клиент:</strong> <?= e($order['customer_name']) ?></p>
            <p><strong>Телефон:</strong> <?= e($order['customer_phone']) ?></p>
            <p><strong>Email:</strong> <?= e($order['customer_email']) ?></p>
            <p><strong>Пользователь:</strong> <?= e($order['user_name'] ?? '-') ?></p>
            <p><strong>Получатель:</strong> <?= e($order['recipient_name']) ?></p>
            <p><strong>Телефон получателя:</strong> <?= e($order['recipient_phone']) ?></p>
            <p><strong>Адрес:</strong> <?= e($order['delivery_address']) ?></p>
            <p><strong>Дата доставки:</strong> <?= e($order['delivery_date']) ?></p>
            <p><strong>Время:</strong> <?= e($order['delivery_time']) ?></p>
            <p><strong>Оплата:</strong> <?= e($order['payment_method']) ?></p>
            <p>
                <strong>Статус:</strong>
                <span class="status-badge status-<?= e($order['status']) ?>">
                    <?= e(orderStatusLabel($order['status'])) ?>
                </span>
            </p>
            <p><strong>Комментарий:</strong> <?= nl2br(e($order['comment'])) ?></p>
            <p><strong>Сумма:</strong> <?= formatPrice($order['total']) ?></p>

            <?php if (!empty($order['attachment'])): ?>
                <p><strong>Файл:</strong> <a href="../uploads/<?= e($order['attachment']) ?>" download>Скачать</a></p>
            <?php endif; ?>
        </div>

        <?php if (!empty($availableTransitions)): ?>
            <div class="form-box" style="max-width:100%; margin-bottom:20px;">
                <h3>Переходы статуса</h3>
                <div class="actions">
                    <?php foreach ($availableTransitions as $nextStatus): ?>
                        <a class="btn btn-green" href="order_status.php?id=<?= (int)$order['id'] ?>&status=<?= urlencode($nextStatus) ?>">
                            Перевести в <?= e(orderStatusLabel($nextStatus)) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <table class="admin-table" style="margin-bottom:20px;">
            <tr>
                <th>ID товара</th>
                <th>Название</th>
                <th>Цена</th>
                <th>Количество</th>
                <th>Сумма</th>
            </tr>

            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= (int)$item['product_id'] ?></td>
                    <td><?= e($item['product_name']) ?></td>
                    <td><?= formatPrice($item['price']) ?></td>
                    <td><?= (int)$item['quantity'] ?></td>
                    <td><?= formatPrice($item['subtotal']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <table class="admin-table">
            <tr>
                <th>Кто</th>
                <th>Действие</th>
                <th>Описание</th>
                <th>Дата</th>
            </tr>

            <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?= e($log['name'] ?? 'Система') ?></td>
                    <td><?= e($log['action']) ?></td>
                    <td><?= e($log['details']) ?></td>
                    <td><?= e($log['created_at']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>