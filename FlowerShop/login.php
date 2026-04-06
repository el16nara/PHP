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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user;
        redirect('index.php');
    } else {
        $error = 'Неверный email или пароль.';
    }
}

require_once 'includes/header.php';
?>

<section class="section">
    <div class="container auth-wrapper">
        <div class="auth-box">
            <h1>Вход</h1>

            <?php if ($error): ?>
                <div class="alert alert-error"><?= e($error) ?></div>
            <?php endif; ?>

            <form method="post">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>

                <div class="form-group">
                    <label>Пароль</label>
                    <input type="password" name="password" required>
                </div>

                <button class="btn" type="submit">Войти</button>
            </form>

            <div class="auth-footer-text">
                Нет аккаунта? <a href="register.php">Зарегистрироваться</a>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>