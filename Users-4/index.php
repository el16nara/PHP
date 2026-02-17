<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Регистрация</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<h2>Регистрация на гос службу</h2>

<form action="check.php" method="post" enctype="multipart/form-data">

<input type="text" name="login" placeholder="Логин" required>
<input type="password" name="password" placeholder="Пароль" required>
<input type="text" name="fullname" placeholder="ФИО" required>
<input type="email" name="email" placeholder="Email" required>
<input type="text" name="phone" placeholder="Телефон" required>
<input type="date" name="birthdate" required>
<input type="text" name="address" placeholder="Адрес" required>
<input type="text" name="passport" placeholder="Паспорт" required>
<input type="text" name="education" placeholder="Образование" required>
<textarea name="experience" placeholder="Опыт работы" required></textarea>

<label>Фото обязательно:</label>
<input type="file" name="photo" accept="image/*" required>

<button type="submit">Зарегистрироваться</button>

</form>

<a href="authorization.php">Уже есть аккаунт?</a>

</body>
</html>