<?php
include("config/db.php");
$error="";
if(isset($_POST['login'])){
    $email=$_POST['email'];
    $password=$_POST['password'];

    $res=mysqli_query($conn,"SELECT * FROM users WHERE email='$email'");
    $user=mysqli_fetch_assoc($res);

    if($user && password_verify($password,$user['password'])){
        $_SESSION['user_id']=$user['id'];
        $_SESSION['role']=$user['role'];
        header("Location: index.php");
        exit();
    } else {
        $error="Неверный логин или пароль";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="style.css">
<title>Вход</title>
</head>
<body>
<div class="container">
<h2>Вход</h2>
<?php if($error) echo "<p class='error'>$error</p>"; ?>
<form method="post">
<input type="email" name="email" placeholder="Email" required>
<input type="password" name="password" placeholder="Пароль" required>
<button type="submit" name="login">Войти</button>
</form>
<a href="register.php">Нет аккаунта? Регистрация</a>
</div>
</body>
</html>