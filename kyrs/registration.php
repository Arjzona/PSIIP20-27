<?php
// Подключение к базе данных
$host = 'localhost';
$dbname = 'stylist';
$user = 'root';
$password = '8308';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

// Обработка формы регистрации
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fam = $_POST['fam'];
    $name = $_POST['name'];
    $lastfam = $_POST['lastfam'];
    $login = $_POST['login'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Валидация
    if (empty($fam) || empty($name) || empty($lastfam) || empty($login) || empty($_POST['password'])) {
        die("Все поля обязательны для заполнения.");
    }

    if (!filter_var($login, FILTER_VALIDATE_EMAIL)) {
        die("Некорректный формат почты.");
    }

    // Проверка, существует ли пользователь с такой почтой
    $stmt = $pdo->prepare("SELECT Id FROM client WHERE Login = ?");
    $stmt->execute([$login]);
    $existingUser = $stmt->fetch();

    if ($existingUser) {
        die("Пользователь с такой почтой уже зарегистрирован.");
    }

    // Сохранение в базу данных
    $stmt = $pdo->prepare("INSERT INTO client (Fam, Name, Lastfam, Login, Password) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$fam, $name, $lastfam, $login, $password]);

    // Перенаправление на главную страницу 
    header("Location: indexsesion.php?registration=success");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #1a1a1a;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        form {
            background-color: #333;
            padding: 20px;
            border-radius: 10px;
            width: 300px;
        }

        input {
            width: 100%;
            padding: 5px;
            margin: 10px 0px;
            border: 1px solid #cf54bf;
            border-radius: 5px;
            background-color: #444;
            color: #fff;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #cf54bf;
            color: #1a1a1a;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <form method="POST">
        <h2>Регистрация</h2>
        <input type="text" name="fam" placeholder="Фамилия" required>
        <input type="text" name="name" placeholder="Имя" required>
        <input type="text" name="lastfam" placeholder="Отчество" required>
        <input type="email" name="login" placeholder="Почта" required>
        <input type="password" name="password" placeholder="Пароль" required>
        <button type="submit">Зарегистрироваться</button>
    </form>
</body>
</html>