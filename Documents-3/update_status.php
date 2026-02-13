<?php
require "db.php";

$allowedStatuses = [
    "Черновик",
    "На согласовании",
    "Одобрен",
    "Отклонен"
];

if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    header("Location: index.php");
    exit;
}

$id = intval($_POST['id'] ?? 0);
$status = $_POST['status'] ?? '';

if (!$id || !in_array($status, $allowedStatuses)) {
    header("Location: index.php?error=Некорректные данные");
    exit;
}

$stmt = mysqli_prepare(
    $link,
    "UPDATE documents 
     SET status=?, updated_at=NOW()
     WHERE id=?"
);

mysqli_stmt_bind_param($stmt, "si", $status, $id);
mysqli_stmt_execute($stmt);

header("Location: index.php?updated=1");
exit;
?>