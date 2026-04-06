<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isLoggedIn()) {
    redirect('index.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $password2 = trim($_POST['password2'] ?? '');

    if ($name === '' || $email === '' || $password === '' || $password2 === '') {
        $error = 'Заполните все обязательные поля.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Введите корректный email.';
    } elseif ($password !== $password2) {
        $error = 'Пароли не совпадают.';
    } elseif (mb_strlen($password) < 6) {
        $error = 'Пароль должен содержать минимум 6 символов.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $error = 'Пользователь с таким email уже существует.';
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("
                INSERT INTO users (name, email, password, phone, role)
                VALUES (?, ?, ?, ?, 'user')
            ");
            $stmt->execute([$name, $email, $hashedPassword, $phone]);

            $success = 'Регистрация прошла успешно. Теперь вы можете войти.';
        }
    }
}

require_once 'includes/header.php';
?>

<section class="section">
    <div class="container auth-wrapper">
        <div class="auth-box">
            <h1>Регистрация</h1>

            <?php if ($error): ?>
                <div class="alert alert-error"><?= e($error) ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success"><?= e($success) ?></div>
            <?php endif; ?>

            <form method="post">
                <div class="form-group">
                    <label>Имя</label>
                    <input type="text" name="name" required>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>

                <div class="form-group">
                    <label>Телефон</label>
                    <input type="text" name="phone">
                </div>

                <div class="form-group">
                    <label>Пароль</label>
                    <input type="password" name="password" required>
                </div>

                <div class="form-group">
                    <label>Повторите пароль</label>
                    <input type="password" name="password2" required>
                </div>

                <button class="btn" type="submit">Зарегистрироваться</button>
            </form>

            <div class="auth-footer-text">
                Уже есть аккаунт? <a href="login.php">Войти</a>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>