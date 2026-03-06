<?php
include("config/db.php");
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}
$total=0;
?>
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="style.css">
<title>Корзина</title>
</head>
<body>
<div class="container">
<h2>Корзина</h2>
<?php if(empty($_SESSION['cart'])): ?>
<p>Корзина пуста</p>
<?php else: ?>
<?php foreach($_SESSION['cart'] as $id=>$qty):
    $res=mysqli_query($conn,"SELECT * FROM menu WHERE id=$id");
    $row=mysqli_fetch_assoc($res);
    $sum=$row['price']*$qty;
    $total+=$sum;
?>
<div class="card">
<?php echo $row['name']; ?> x <?php echo $qty; ?> = <?php echo $sum; ?> сом
</div>
<?php endforeach; ?>
<h3>Итого: <?php echo $total; ?> сом</h3>
<a href="create_order.php" class="btn">Оформить заказ</a>
<?php endif; ?>
<a href="menu.php" class="btn">Меню</a>
</div>
</body>
</html>