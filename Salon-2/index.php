<?php include ('db.php');  ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Клиенты салона красоты "Царица"</title>

    <style>
        body{font-family:Arial;background:#f3f3f3;padding:30px;}
        table{border-collapse:collapse;background:white;width:100%;}
        td,th{padding:10px;border:1px solid #ccc;}
        th{background:#ff4d88;color:white;}
        tr:hover{background:#f1f1f1;}
    </style>

</head>
<body>

<h2>База клиентов салона красоты "Царица"</h2>

<table>
<tr>
<th>ID</th>
<th>Имя</th>
<th>Фамилия</th>
<th>Телефон</th>
<th>Email</th>
<th>Пол</th>
<th>Дата рождения</th>
<th>Любимая услуга</th>
<th>Дополнительная информация</th>
</tr>

<?php 
$result = mysqli_query($link, "SELECT * FROM `\"Tsaritsa\" salon`");
$myrow = mysqli_fetch_array($result);

if($myrow){

do{

printf("<tr>
<td>%s</td>
<td>%s</td>
<td>%s</td>
<td>%s</td>
<td>%s</td>
<td>%s</td>
<td>%s</td>
<td>%s</td>
<td>%s</td>
</tr>",
$myrow['id'],
$myrow['first_name'],
$myrow['last_name'],
$myrow['phone'],
$myrow['email'],
$myrow['gender'],
$myrow['birth_date'],   
$myrow['favorite_service'],
$myrow['notes']
);

} while($myrow = mysqli_fetch_array($result));

}else{
echo "<tr><td colspan='10'>Нет клиентов</td></tr>";
}

?>

</table>

</body>
</html>