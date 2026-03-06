<?php
include("config/db.php");
$error="";
if(isset($_POST['reg'])){
    $name=$_POST['name'];
    $email=$_POST['email'];
    $password=password_hash($_POST['password'],PASSWORD_DEFAULT);

    $check=mysqli_query($conn,"SELECT id FROM users WHERE email='$email'");
    if(mysqli_num_rows($check)>0){
        $error="Пользователь с таким email уже существует";
    } else {
        mysqli_query($conn,"INSERT INTO users(name,email,password) VALUES('$name','$email','$password')");
        header("Location: login.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="style.css">
<title>Регистрация</title>
</head>
<body>
<div class="container">
<h2>Регистрация</h2>
<?php if($error) echo "<p class='error'>$error</p>"; ?>
<form method="post">
<input type="text" name="name" placeholder="Имя" required>
<input type="email" name="email" placeholder="Email" required>
<input type="password" name="password" placeholder="Пароль" required>
<button type="submit" name="reg">Зарегистрироваться</button>
</form>
<a href="login.php">Уже есть аккаунт? Вход</a>
</div>
</body>
</html>