<?php
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

$title = trim($_POST['title'] ?? '');
$author = trim($_POST['author'] ?? '');
$responsible = trim($_POST['responsible'] ?? '');
$description = trim($_POST['description'] ?? '');

if (!$title || !$author || !$responsible || !$description) {
    header("Location: index.php?error=Заполните все поля");
    exit;
}

$stmt = mysqli_prepare(
    $link,
    "INSERT INTO documents 
    (title, author, responsible, description, status, created_at)
    VALUES (?, ?, ?, ?, 'Черновик', NOW())"
);

mysqli_stmt_bind_param(
    $stmt,
    "ssss",
    $title,
    $author,
    $responsible,
    $description
);

if (mysqli_stmt_execute($stmt)) {
    header("Location: index.php?success=1");
} else {
    header("Location: index.php?error=" . urlencode(mysqli_error($link)));
}

exit;
?>