<?php
include("config/db.php");
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}
$user_id=$_SESSION['user_id'];
$res=mysqli_query($conn,"SELECT * FROM orders WHERE user_id='$user_id'");
?>
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="style.css">
<title>Мои заказы</title>
</head>
<body>
<div class="container">
<h2>Мои заказы</h2>
<?php if(mysqli_num_rows($res)==0): ?>
<p>У вас пока нет заказов</p>
<?php else: ?>
<?php while($row=mysqli_fetch_assoc($res)): ?>
<div class="card">
<h3>Заказ #<?php echo $row['id']; ?></h3>
<p>Статус: <?php echo $row['status']; ?></p>
<p>Дата: <?php echo $row['created_at']; ?></p>
</div>
<?php endwhile; ?>
<?php endif; ?>
<a href="menu.php" class="btn">Меню</a>
</div>
</body>
</html>