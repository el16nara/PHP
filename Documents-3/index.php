<?php
require "db.php";

$filter = $_GET['filter'] ?? '';

if ($filter && in_array($filter, ["Черновик","На согласовании","Одобрен","Отклонен"])) {
    $stmt = mysqli_prepare($link, "SELECT * FROM documents WHERE status=? ORDER BY id DESC");
    mysqli_stmt_bind_param($stmt, "s", $filter);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $countStmt = mysqli_prepare($link, "SELECT COUNT(*) as cnt FROM documents WHERE status=?");
    mysqli_stmt_bind_param($countStmt, "s", $filter);
    mysqli_stmt_execute($countStmt);
    $countRes = mysqli_stmt_get_result($countStmt);
    $count = mysqli_fetch_assoc($countRes)['cnt'];
} else {
    $result = mysqli_query($link, "SELECT * FROM documents ORDER BY id DESC");
    $countRes = mysqli_query($link, "SELECT COUNT(*) as cnt FROM documents");
    $count = mysqli_fetch_assoc($countRes)['cnt'];
}

function statusColor($status) {
    if ($status == 'Черновик') return '#9aa6c9';
    if ($status == 'На согласовании') return '#6ec5ff';
    if ($status == 'Одобрен') return '#7ed9b6';
    if ($status == 'Отклонен') return '#ff9bb3';
    return '#444';
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Система согласования документов</title>
<link rel="stylesheet" href="styles.css">
</head>
<body>

<h1>Система согласования документов</h1>

<p>Всего документов: <b><?= $count ?></b></p>

<form method="GET" style="margin-bottom:15px;">
<select name="filter">
<option value="">Все статусы</option>
<option value="Черновик" <?= $filter=='Черновик'?'selected':''?>>Черновик</option>
<option value="На согласовании" <?= $filter=='На согласовании'?'selected':''?>>На согласовании</option>
<option value="Одобрен" <?= $filter=='Одобрен'?'selected':''?>>Одобрен</option>
<option value="Отклонен" <?= $filter=='Отклонен'?'selected':''?>>Отклонен</option>
</select>
<button type="submit">Фильтровать</button>
</form>

<?php if (isset($_GET['success'])): ?><div class="success">Документ создан</div><?php endif; ?>
<?php if (isset($_GET['updated'])): ?><div class="success">Статус обновлён</div><?php endif; ?>
<?php if (isset($_GET['deleted'])): ?><div class="success">Документ удалён</div><?php endif; ?>
<?php if (isset($_GET['error'])): ?><div class="error"><?= htmlspecialchars($_GET['error']) ?></div><?php endif; ?>

<h2>Создать документ</h2>
<form action="add.php" method="POST">
<input type="text" name="title" placeholder="Название" required>
<input type="text" name="author" placeholder="Автор" required>
<input type="text" name="responsible" placeholder="Ответственный" required>
<textarea name="description" placeholder="Описание" required></textarea>
<button type="submit">Создать</button>
</form>

<h2>Список документов</h2>
<?php if (mysqli_num_rows($result) > 0): ?>
<?php while ($doc = mysqli_fetch_assoc($result)): ?>
<div class="card">
<strong><?= htmlspecialchars($doc['title']) ?></strong><br>
Автор: <?= htmlspecialchars($doc['author']) ?><br>
Ответственный: <?= htmlspecialchars($doc['responsible']) ?><br>
Описание: <?= htmlspecialchars($doc['description']) ?><br>
Статус:
<b style="color:<?= statusColor($doc['status']) ?>"><?= htmlspecialchars($doc['status']) ?></b><br>
Дата: <?= $doc['created_at'] ?><br><br>

<form style="display:inline-block" action="update_status.php" method="POST" onsubmit="return confirm('Изменить статус?')">
<input type="hidden" name="id" value="<?= $doc['id'] ?>">
<?php if($doc['status'] !== "Одобрен"): ?>
<button type="submit" name="status" value="На согласовании">На согласовании</button>
<button type="submit" name="status" value="Одобрен">Одобрен</button>
<button type="submit" name="status" value="Отклонен">Отклонен</button>
<?php endif; ?>
</form>

<form style="display:inline-block" action="delete.php" method="POST" onsubmit="return confirm('Удалить документ?')">
<input type="hidden" name="id" value="<?= $doc['id'] ?>">
<button type="submit">Удалить</button>
</form>

</div>
<?php endwhile; else: ?>
<p>Документов нет</p>
<?php endif; ?>
</body>
</html>