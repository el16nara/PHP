<?php
session_start();
require_once 'functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $profile = [
        'name' => clean($_POST['name']),
        'age' => (int)$_POST['age'],
        'city' => clean($_POST['city']),
        'email' => clean($_POST['email']),
        'language' => clean($_POST['language']),
        'experience' => (int)$_POST['experience'],
        'photo' => uploadPhoto($_FILES['photo'])
    ];

    $_SESSION['profiles'][] = $profile;
}

header('Location: index.php');
exit;