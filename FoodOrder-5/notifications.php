<?php
include("config/db.php");
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}
$user_id=$_SESSION['user_id'];
$res=mysqli_query($conn,"SELECT * FROM notifications WHERE user_id='$user_id'");
?>
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="style.css">
<title>Уведомления</title>
</head>
<body>
<div class="container">
<h2>Уведомления</h2>
<?php if(mysqli_num_rows($res)==0): ?>
<p>Нет уведомлений</p>
<?php else: ?>
<?php while($row=mysqli_fetch_assoc($res)): ?>
<div class="card">
<p><?php echo $row['message']; ?></p>
<p><?php echo $row['created_at']; ?></p>
</div>
<?php endwhile; ?>
<?php endif; ?>
<a href="index.php" class="btn">Главная</a>
</div>
</body>
</html>