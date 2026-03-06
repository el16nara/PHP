<?php
include("config/db.php");
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}
$res=mysqli_query($conn,"SELECT * FROM menu");
?>
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="style.css">
<title>Меню</title>
</head>
<body>
<div class="container">
<h2>Меню</h2>
<?php while($row=mysqli_fetch_assoc($res)): ?>
<div class="card">
<h3><?php echo $row['name']; ?></h3>
<p><?php echo $row['description']; ?></p>
<p class="price"><?php echo $row['price']; ?> сом</p>
<a href="add_to_cart.php?id=<?php echo $row['id']; ?>" class="btn">Добавить</a>
</div>
<?php endwhile; ?>
<a href="cart.php" class="btn">Перейти в корзину</a>
<a href="index.php" class="btn">Главная</a>
</div>
</body>
</html>