<?php
session_start();
require "db.php";

$login = $_POST['login'];
$password = $_POST['password'];

$stmt = $mysql->prepare("SELECT * FROM users WHERE login=?");
$stmt->bind_param("s", $login);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if($user && password_verify($password, $user['password'])) {
    $_SESSION['user'] = $user;
    header("Location: register_yes.php");
} else {
    echo "Неверный логин или пароль!";
}
?>