<?php
include("config/db.php");
if(!isset($_SESSION['role']) || $_SESSION['role']!='admin'){
    die("Доступ запрещен");
}
$res=mysqli_query($conn,"SELECT * FROM orders");
?>
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="style.css">
<title>Админ панель</title>
</head>
<body>
<div class="container">
<h2>Все заказы</h2>
<?php while($row=mysqli_fetch_assoc($res)): ?>
<div class="card">
<h3>Заказ #<?php echo $row['id']; ?></h3>
<p>Статус: <?php echo $row['status']; ?></p>
<form action="update_status.php" method="post">
<input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
<select name="status">
<option>Новый</option>
<option>Готовится</option>
<option>В пути</option>
<option>Доставлен</option>
<option>Отменён</option>
</select>
<input type="text" name="comment" placeholder="Комментарий">
<button type="submit">Обновить</button>
</form>
</div>
<?php endwhile; ?>
<a href="index.php" class="btn">Главная</a>
</div>
</body>
</html>