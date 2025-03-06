<?php
session_start();

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    die("Доступ запрещён. Пожалуйста, войдите в систему.");
}

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

// Обработка формы записи
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clientId = $_SESSION['user_id']; // ID авторизованного пользователя
    $serviceId = $_POST['service_id'];
    $masterId = $_POST['master_id'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $phone = $_POST['phone'];

    // Преобразование даты и времени
    $datetime = DateTime::createFromFormat('Y-m-d H:i', "$date $time");
    if (!$datetime) {
        die("Неверный формат даты или времени.");
    }
    $datetimeFormatted = $datetime->format('Y-m-d H:i:s'); // Преобразуем в формат MySQL

    // Валидация данных
    if (empty($serviceId) || empty($masterId) || empty($datetimeFormatted) || empty($phone)) {
        die("Все поля обязательны для заполнения.");
    }

    // Сохранение записи в базу данных
    $stmt = $pdo->prepare("INSERT INTO note (Id_client, Id_service, Id_master, Time, Phone_client) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$clientId, $serviceId, $masterId, $datetimeFormatted, $phone]);

    // Перенаправление на главную страницу с сообщением об успехе
    header("Location: indexsesion.php?booking=success");
    exit();
} else {
    die("Неверный метод запроса.");
}
?>