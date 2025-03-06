<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $password = $_POST['password'];
    $remember = isset($_POST['remember']) ? 'Да' : 'Нет';

    $_SESSION['password'] = $password;
    $_SESSION['remember'] = $remember;

    echo "Пароль: $password<br>";
    echo "Запомнить меня: $remember<br>";
}

if (isset($_SESSION['password']) && isset($_SESSION['remember'])) {
    echo "Данные из сессии:<br>";
    echo "Пароль: " . $_SESSION['password'] . "<br>";
    echo "Запомнить меня: " . $_SESSION['remember'] . "<br>";
    echo "Идентификатор сессии: " . session_id() . "<br>";
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Результат</title>
</head>
<body>
</body>
</html>