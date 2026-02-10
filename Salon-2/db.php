<?php
$host = "localhost";
$user = "elnara";
$password = "";
$dbname = "beuty_salon";

$link = new mysqli($host, $user, $password, $dbname);

if ($link->connect_error) {
    die("Ошибка подключения: " . $link->connect_error);
}

$link->set_charset("utf8mb4");
?>