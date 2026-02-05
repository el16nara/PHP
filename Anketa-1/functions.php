<?php

if (!isset($_SESSION['profiles'])) {
    $_SESSION['profiles'] = [];
}

function clean($data) {
    return htmlspecialchars(trim($data));
}

function uploadPhoto($file) {
    if (empty($file['name'])) {
        return '';
    }

    if (!is_dir('uploads')) {
        mkdir('uploads', 0777, true);
    }

    $fileName = time() . '_' . $file['name'];
    $path = 'uploads/' . $fileName;

    move_uploaded_file($file['tmp_name'], $path);

    return $path;
}

function renderProfiles() {
    if (empty($_SESSION['profiles'])) {
        echo '<p>Анкет пока нет.</p>';
        return;
    }

    foreach ($_SESSION['profiles'] as $profile) {
        echo '<div class="profile">';

        if ($profile['photo']) {
            echo '<img src="' . $profile['photo'] . '">';
        }

        echo '<strong>Имя:</strong> ' . $profile['name'] . '<br>';
        echo '<strong>Возраст:</strong> ' . $profile['age'] . '<br>';
        echo '<strong>Город:</strong> ' . $profile['city'] . '<br>';
        echo '<strong>Email:</strong> ' . $profile['email'] . '<br>';
        echo '<strong>Язык:</strong> ' . $profile['language'] . '<br>';
        echo '<strong>Опыт:</strong> ' . $profile['experience'] . ' лет';

        echo '</div>';
    }
}