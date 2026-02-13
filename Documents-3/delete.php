<?php
require "db.php";

if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    header("Location: index.php");
    exit;
}

$id = intval($_POST['id'] ?? 0);

if ($id > 0) {
    $stmt = mysqli_prepare($link, "DELETE FROM documents WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
}

header("Location: index.php?deleted=1");
exit;
?>