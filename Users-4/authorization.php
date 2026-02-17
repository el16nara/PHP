<?php
session_start();
require "db.php";

$login = $_POST['login'] ?? '';
$password = $_POST['password'] ?? '';

if($login && $password){
    $stmt = $mysql->prepare("SELECT * FROM users WHERE login=?");
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user;
        header("Location: register_yes.php");
        exit();
    } else {
        $error = "Неверный логин или пароль!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Авторизация</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
<h2>Авторизация</h2>
<?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
<form action="authorization.php" method="post">
<input type="text" name="login" placeholder="Логин" required>
<input type="password" name="password" placeholder="Пароль" required>
<button type="submit">Войти</button>
</form>
<a href="register.php">Регистрация</a>
</div>
</body>
</html>