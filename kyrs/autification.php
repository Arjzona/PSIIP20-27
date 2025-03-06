<?php
session_start();

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

// Обработка формы входа
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $login = $_POST['login'];
    $password = $_POST['password'];

    // Поиск пользователя в базе данных
    $stmt = $pdo->prepare("SELECT Id, Password, Role FROM client WHERE Login = ?");
    $stmt->execute([$login]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['Password'])) {
        $_SESSION['user_id'] = $user['Id'];
        $_SESSION['role'] = $user['Role']; // Сохраняем роль в сессии

        // Перенаправление в зависимости от роли
        if ($user['Role'] === 'admin') {
            header("Location: admin.php");
        } else {
            header("Location: indexsesion.php");
        }
        exit();
    } else {
        $error = "Неверный логин или пароль.";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход</title>
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
            margin: 10px 0;
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

        .error {
            color: #cf54bf;
            text-align: center;
        }
    </style>
</head>
<body>
    <form method="POST">
        <h2>Вход</h2>
        <?php if (isset($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <input type="email" name="login" placeholder="Почта" required>
        <input type="password" name="password" placeholder="Пароль" required>
        <button type="submit">Войти</button>
        <p>Нет аккаунта? <a href="registration.php">Зарегистрируйтесь</a></p>
    </form>
</body>
</html>