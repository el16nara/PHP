<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isAdmin()) {
    redirect('../login.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $role = trim($_POST['role'] ?? '');

    if ($id > 0 && in_array($role, ['user', 'moderator', 'admin'], true)) {
        $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->execute([$role, $id]);
    }

    redirect('users.php');
}

$users = $pdo->query("SELECT * FROM users ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);

require_once '../includes/header.php';
?>

<section class="section">
    <div class="container">
        <h1 class="page-title">Пользователи</h1>

        <table class="admin-table">
            <tr>
                <th>ID</th>
                <th>Имя</th>
                <th>Email</th>
                <th>Телефон</th>
                <th>Роль</th>
                <th>Изменить роль</th>
            </tr>

            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= (int)$user['id'] ?></td>
                    <td><?= e($user['name']) ?></td>
                    <td><?= e($user['email']) ?></td>
                    <td><?= e($user['phone']) ?></td>
                    <td><?= e($user['role']) ?></td>
                    <td>
                        <form method="post">
                            <input type="hidden" name="id" value="<?= (int)$user['id'] ?>">
                            <select name="role">
                                <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>user</option>
                                <option value="moderator" <?= $user['role'] === 'moderator' ? 'selected' : '' ?>>moderator</option>
                                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>admin</option>
                            </select>
                            <button class="btn" type="submit">Сохранить</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>