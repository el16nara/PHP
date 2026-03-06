<?php
include("config/db.php");
if(!isset($_SESSION['role']) || $_SESSION['role']!='admin'){
    die("Доступ запрещен");
}
$order=$_POST['order_id'];
$status=$_POST['status'];
$comment=$_POST['comment'];
$admin=$_SESSION['user_id'];

$res=mysqli_query($conn,"SELECT status,user_id FROM orders WHERE id=$order");
$row=mysqli_fetch_assoc($res);

$old=$row['status'];
$user=$row['user_id'];

mysqli_query($conn,"UPDATE orders SET status='$status' WHERE id=$order");
mysqli_query($conn,"INSERT INTO order_history(order_id,old_status,new_status,admin_id,comment)
VALUES('$order','$old','$status','$admin','$comment')");

$msg="Статус заказа #$order изменен на $status";
mysqli_query($conn,"INSERT INTO notifications(user_id,order_id,message)
VALUES('$user','$order','$msg')");

header("Location: admin_orders.php");
exit();
?>