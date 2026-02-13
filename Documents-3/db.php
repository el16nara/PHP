<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'documents';

$link = mysqli_connect($host, $user, $password, $database);

if (!$link){
    die('Ошибка подключения: ' . mysqli_connect_error());
}

mysqli_set_charset($link, "utf8mb4");
?>