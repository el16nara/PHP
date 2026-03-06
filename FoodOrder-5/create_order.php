<?php
include("config/db.php");
if(!isset($_SESSION['cart']) || empty($_SESSION['cart'])){
    header("Location: menu.php");
    exit();
}
$user=$_SESSION['user_id'];
mysqli_query($conn,"INSERT INTO orders(user_id) VALUES('$user')");
$order_id=mysqli_insert_id($conn);

foreach($_SESSION['cart'] as $id=>$qty){
    $res=mysqli_query($conn,"SELECT price FROM menu WHERE id=$id");
    $row=mysqli_fetch_assoc($res);
    $price=$row['price'];
    mysqli_query($conn,"INSERT INTO order_items(order_id,menu_id,quantity,price)
        VALUES('$order_id','$id','$qty','$price')");
}
unset($_SESSION['cart']);
header("Location: orders.php");
exit();
?>