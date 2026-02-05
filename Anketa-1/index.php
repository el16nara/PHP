<?php
session_start();
require_once 'functions.php';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Анкеты</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h2>Добавить анкету</h2>

<form method="post" action="handler.php" enctype="multipart/form-data">
    <input type="text" name="name" placeholder="Имя" required>
    <input type="file" name="photo" accept="image/*">
    <input type="number" name="age" placeholder="Возраст" required>
    <input type="text" name="city" placeholder="Город" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="text" name="language" placeholder="Любимый язык программирования" required>
    <input type="number" name="experience" placeholder="Опыт (лет)" required>
    <button type="submit">Добавить</button>
</form>

<h2>Список анкет</h2>

<?php renderProfiles(); ?>

</body>
</html>