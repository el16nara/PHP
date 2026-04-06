<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isLoggedIn()) {
    redirect('login.php');
}

ensureCart();

if (empty($_SESSION['cart'])) {
    redirect('cart.php');
}

$error = '';
$success = '';

$items = [];
$ids = array_keys($_SESSION['cart']);
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders) AND is_active = 1");
$stmt->execute($ids);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = 0;
foreach ($items as $item) {
    $qty = $_SESSION['cart'][$item['id']] ?? 0;
    $total += $item['price'] * $qty;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_name = trim($_POST['customer_name'] ?? '');
    $customer_phone = trim($_POST['customer_phone'] ?? '');
    $customer_email = trim($_POST['customer_email'] ?? '');
    $recipient_name = trim($_POST['recipient_name'] ?? '');
    $recipient_phone = trim($_POST['recipient_phone'] ?? '');
    $delivery_address = trim($_POST['delivery_address'] ?? '');
    $delivery_date = trim($_POST['delivery_date'] ?? '');
    $delivery_time = trim($_POST['delivery_time'] ?? '');
    $payment_method = trim($_POST['payment_method'] ?? 'cash');
    $comment = trim($_POST['comment'] ?? '');
    $attachment = null;

    if ($customer_name === '' || $customer_phone === '' || $delivery_address === '') {
        $error = 'Заполните обязательные поля.';
    } else {
        if (!empty($_FILES['attachment']['name'])) {
            $upload = uploadOrderFile('attachment');

            if (isset($upload['error'])) {
                $error = $upload['error'];
            } else {
                $attachment = $upload['success'];
            }
        }
    }

    if ($error === '') {
        $userId = $_SESSION['user']['id'];

        $stmt = $pdo->prepare("
            INSERT INTO orders
            (user_id, customer_name, customer_phone, customer_email, recipient_name, recipient_phone, delivery_address, delivery_date, delivery_time, payment_method, status, total, comment, attachment)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'new', ?, ?, ?)
        ");
        $stmt->execute([
            $userId,
            $customer_name,
            $customer_phone,
            $customer_email,
            $recipient_name,
            $recipient_phone,
            $delivery_address,
            $delivery_date ?: null,
            $delivery_time,
            $payment_method,
            $total,
            $comment,
            $attachment
        ]);

        $orderId = $pdo->lastInsertId();

        $itemStmt = $pdo->prepare("
            INSERT INTO order_items
            (order_id, product_id, product_name, price, quantity, subtotal)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        foreach ($items as $item) {
            $qty = $_SESSION['cart'][$item['id']] ?? 0;
            $subtotal = $item['price'] * $qty;

            $itemStmt->execute([
                $orderId,
                $item['id'],
                $item['name'],
                $item['price'],
                $qty,
                $subtotal
            ]);
        }

        logAction($pdo, $userId, $orderId, 'create', 'Создан новый заказ');
        $_SESSION['cart'] = [];
        $success = 'Заказ успешно оформлен.';
    }
}

require_once 'includes/header.php';
?>

<section class="section">
    <div class="container">
        <h1 class="page-title">Оформление заказа</h1>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= e($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= e($success) ?></div>
            <p><a class="btn" href="catalog.php">Вернуться в каталог</a></p>
        <?php else: ?>
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                <div class="form-box" style="max-width:100%;">
                    <form method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label>Ваше имя</label>
                            <input type="text" name="customer_name" value="<?= e($_SESSION['user']['name'] ?? '') ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Ваш телефон</label>
                            <input type="text" name="customer_phone" required>
                        </div>

                        <div class="form-group">
                            <label>Ваш email</label>
                            <input type="email" name="customer_email" value="<?= e($_SESSION['user']['email'] ?? '') ?>">
                        </div>

                        <div class="form-group">
                            <label>Имя получателя</label>
                            <input type="text" name="recipient_name">
                        </div>

                        <div class="form-group">
                            <label>Телефон получателя</label>
                            <input type="text" name="recipient_phone">
                        </div>

                        <div class="form-group">
                            <label>Адрес доставки</label>
                            <textarea name="delivery_address" required></textarea>
                        </div>

                        <div class="form-group">
                            <label>Дата доставки</label>
                            <input type="date" name="delivery_date">
                        </div>

                        <div class="form-group">
                            <label>Время доставки</label>
                            <input type="text" name="delivery_time" placeholder="Например: 18:00-20:00">
                        </div>

                        <div class="form-group">
                            <label>Способ оплаты</label>
                            <select name="payment_method">
                                <option value="cash">Наличными</option>
                                <option value="card">Картой</option>
                                <option value="online">Онлайн</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Комментарий</label>
                            <textarea name="comment"></textarea>
                        </div>

                        <div class="form-group">
                            <label>Файл</label>
                            <input type="file" name="attachment">
                        </div>

                        <button class="btn" type="submit">Подтвердить заказ</button>
                    </form>
                </div>

                <div class="form-box" style="max-width:100%;">
                    <h2>Ваш заказ</h2>
                    <?php foreach ($items as $item): ?>
                        <?php $qty = $_SESSION['cart'][$item['id']] ?? 0; ?>
                        <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
                            <span><?= e($item['name']) ?> × <?= (int)$qty ?></span>
                            <strong><?= formatPrice($item['price'] * $qty) ?></strong>
                        </div>
                    <?php endforeach; ?>

                    <hr>
                    <div style="font-size:22px; font-weight:700;">Итого: <?= formatPrice($total) ?></div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>