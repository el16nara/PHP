<?php
require_once 'config/db.php';

$name = 'Администратор';
$email = 'admin@flowershop.kg';
$phone = '+996702160405';
$password = password_hash('123456', PASSWORD_DEFAULT);

$check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$check->execute([$email]);

if ($check->fetch()) {
    echo 'Администратор уже существует.';
    exit;
}

$stmt = $pdo->prepare("INSERT INTO users (name, email, password, phone, role) VALUES (?, ?, ?, ?, 'admin')");
$stmt->execute([$name, $email, $password, $phone]);

echo 'Администратор создан.';