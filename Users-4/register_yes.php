<?php
session_start();
if(!isset($_SESSION['user'])){
    header("Location: authorization.php");
    exit();
}
$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Профиль</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="profile">
<h2>Добро пожаловать, <?= $user['fullname'] ?></h2>
<img src="uploads/<?= $user['photo'] ?>" width="200">
<p>Логин: <?= $user['login'] ?></p>
<p>Email: <?= $user['email'] ?></p>
<p>Телефон: <?= $user['phone'] ?></p>
<p>Дата рождения: <?= $user['birthdate'] ?></p>
<p>Адрес: <?= $user['address'] ?></p>
<p>Паспорт: <?= $user['passport'] ?></p>
<p>Образование: <?= $user['education'] ?></p>
<p>Опыт работы: <?= $user['experience'] ?></p>
<a href="exit.php">Выйти</a>
</div>
</body>
</html>