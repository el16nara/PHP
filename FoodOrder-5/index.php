<?php include("config/db.php"); ?>
<!DOCTYPE html>
<html>
<head>
<title>Food Delivery</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">

<h1>Food Delivery System</h1>

<?php if(!isset($_SESSION['user_id'])): ?>
<a href="register.php" class="btn">Регистрация</a>
<a href="login.php" class="btn">Вход</a>
<?php else: ?>
<a href="menu.php" class="btn">Меню</a>
<a href="cart.php" class="btn">Корзина</a>
<a href="orders.php" class="btn">Мои заказы</a>
<a href="notifications.php" class="btn">Уведомления</a>
<?php if($_SESSION['role']=="admin"): ?>
<a href="admin_orders.php" class="btn">Админ панель</a>
<?php endif; ?>
<a href="logout.php" class="btn">Выход</a>
<?php endif; ?>

</div>
</body>
</html>