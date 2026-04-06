<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isModeratorOrAdmin()) {
    redirect('../index.php');
}

$stmt = $pdo->query("
    SELECT l.*, u.name, o.customer_name
    FROM logs l
    LEFT JOIN users u ON u.id = l.user_id
    LEFT JOIN orders o ON o.id = l.order_id
    ORDER BY l.id DESC
");
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once '../includes/header.php';
?>

<section class="section">
    <div class="container">
        <h1 class="page-title">Журнал действий</h1>

        <table class="admin-table">
            <tr>
                <th>ID</th>
                <th>Пользователь</th>
                <th>Заказ</th>
                <th>Действие</th>
                <th>Описание</th>
                <th>Дата</th>
            </tr>

            <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?= (int)$log['id'] ?></td>
                    <td><?= e($log['name'] ?? 'Система') ?></td>
                    <td><?= e($log['customer_name'] ?? '-') ?></td>
                    <td><?= e($log['action']) ?></td>
                    <td><?= e($log['details']) ?></td>
                    <td><?= e($log['created_at']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>