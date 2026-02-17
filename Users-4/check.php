<?php
require "db.php";

$login = $_POST['login'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$fullname = $_POST['fullname'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$birthdate = $_POST['birthdate'];
$address = $_POST['address'];
$passport = $_POST['passport'];
$education = $_POST['education'];
$experience = $_POST['experience'];

$photoName = time() . $_FILES['photo']['name'];
move_uploaded_file($_FILES['photo']['tmp_name'], "uploads/" . $photoName);

$stmt = $mysql->prepare("INSERT INTO users 
(login,password,fullname,email,phone,birthdate,address,passport,education,experience,photo)
VALUES (?,?,?,?,?,?,?,?,?,?,?)");

$stmt->bind_param("sssssssssss",
$login,$password,$fullname,$email,$phone,$birthdate,$address,$passport,$education,$experience,$photoName);

$stmt->execute();

header("Location: authorization.php");
exit();
?>