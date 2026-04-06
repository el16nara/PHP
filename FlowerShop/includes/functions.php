<?php

function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function formatPrice($price)
{
    return number_format((float)$price, 0, '.', ' ') . ' сом';
}

function slugify($text)
{
    $map = [
        'а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'e','ж'=>'zh','з'=>'z',
        'и'=>'i','й'=>'y','к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r',
        'с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'h','ц'=>'c','ч'=>'ch','ш'=>'sh','щ'=>'sch',
        'ъ'=>'','ы'=>'y','ь'=>'','э'=>'e','ю'=>'yu','я'=>'ya',
        'А'=>'a','Б'=>'b','В'=>'v','Г'=>'g','Д'=>'d','Е'=>'e','Ё'=>'e','Ж'=>'zh','З'=>'z',
        'И'=>'i','Й'=>'y','К'=>'k','Л'=>'l','М'=>'m','Н'=>'n','О'=>'o','П'=>'p','Р'=>'r',
        'С'=>'s','Т'=>'t','У'=>'u','Ф'=>'f','Х'=>'h','Ц'=>'c','Ч'=>'ch','Ш'=>'sh','Щ'=>'sch',
        'Ъ'=>'','Ы'=>'y','Ь'=>'','Э'=>'e','Ю'=>'yu','Я'=>'ya'
    ];

    $text = strtr($text, $map);
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9]+/i', '-', $text);
    $text = trim($text, '-');

    return $text ?: 'item';
}

function getProductImage($imageName)
{
    $extensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    foreach ($extensions as $ext) {
        $relativePath = 'uploads/' . $imageName . '.' . $ext;
        $absolutePath = __DIR__ . '/../' . $relativePath;

        if (file_exists($absolutePath)) {
            return $relativePath;
        }
    }

    return 'https://via.placeholder.com/600x700?text=No+Image';
}

function isLoggedIn()
{
    return isset($_SESSION['user']);
}

function isAdmin()
{
    return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';
}

function isModerator()
{
    return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'moderator';
}

function isModeratorOrAdmin()
{
    return isModerator() || isAdmin();
}

function ensureCart()
{
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
}

function cartCount()
{
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        return 0;
    }

    return array_sum($_SESSION['cart']);
}

function redirect($url)
{
    if (!headers_sent()) {
        header('Location: ' . $url);
        exit;
    }

    echo '<script>window.location.href=' . json_encode($url) . ';</script>';
    exit;
}

function logAction(PDO $pdo, $userId, $orderId, $action, $details = null)
{
    $stmt = $pdo->prepare("
        INSERT INTO logs (user_id, order_id, action, details)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$userId, $orderId, $action, $details]);
}

function allowedOrderTransitions($role, $currentStatus)
{
    $map = [
        'user' => [
            'new' => ['submitted']
        ],
        'moderator' => [
            'submitted' => ['in_review'],
            'in_review' => ['approved', 'rejected']
        ],
        'admin' => [
            'submitted' => ['in_review'],
            'in_review' => ['approved', 'rejected'],
            'approved' => ['completed'],
            'completed' => [],
            'rejected' => [],
            'cancelled' => [],
            'new' => ['submitted']
        ]
    ];

    return $map[$role][$currentStatus] ?? [];
}

function uploadOrderFile($fieldName)
{
    if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $allowed = [
        'application/pdf' => 'pdf',
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'application/msword' => 'doc',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx'
    ];

    $maxSize = 5 * 1024 * 1024;
    $tmp = $_FILES[$fieldName]['tmp_name'];
    $size = $_FILES[$fieldName]['size'];
    $type = mime_content_type($tmp);

    if ($size > $maxSize) {
        return ['error' => 'Файл слишком большой. Максимум 5 МБ.'];
    }

    if (!isset($allowed[$type])) {
        return ['error' => 'Недопустимый тип файла.'];
    }

    $extension = $allowed[$type];
    $newName = time() . '_' . mt_rand(1000, 9999) . '.' . $extension;
    $destination = __DIR__ . '/../uploads/' . $newName;

    if (!move_uploaded_file($tmp, $destination)) {
        return ['error' => 'Не удалось загрузить файл.'];
    }

    return ['success' => $newName];
}

function orderStatusLabel($status)
{
    $statuses = [
        'new' => 'Новый',
        'submitted' => 'Отправлен',
        'in_review' => 'На рассмотрении',
        'approved' => 'Одобрен',
        'rejected' => 'Отклонён',
        'completed' => 'Завершён',
        'cancelled' => 'Отменён'
    ];

    return $statuses[$status] ?? $status;
}

function isFavorite(PDO $pdo, $userId, $productId)
{
    $stmt = $pdo->prepare("SELECT id FROM favorites WHERE user_id = ? AND product_id = ? LIMIT 1");
    $stmt->execute([$userId, $productId]);
    return (bool)$stmt->fetch(PDO::FETCH_ASSOC);
}