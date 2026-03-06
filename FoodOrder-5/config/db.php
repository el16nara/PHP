<?php
$host = "localhost";
$user = "elnara";
$password = "";
$dbname = "food_order_db";

$conn = mysqli_connect($host,$user,$password,$dbname);
if(!$conn){
    die("Ошибка подключения к БД");
}
session_start();
?>