<?php
$mysql = new mysqli("localhost", "root", "", "users");
if ($mysql->connect_error) {
    die("Ошибка подключения");
}
?>