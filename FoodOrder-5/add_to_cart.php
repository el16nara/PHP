<?php
include("config/db.php");
$id=$_GET['id'];
if(!isset($_SESSION['cart'])) $_SESSION['cart']=[];
if(isset($_SESSION['cart'][$id])){
    $_SESSION['cart'][$id]++;
}else{
    $_SESSION['cart'][$id]=1;
}
header("Location: menu.php");
exit();
?>