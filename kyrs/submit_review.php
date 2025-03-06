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

// Обработка отправки отзыва
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clientName = $_POST['client_name'];
    $reviewText = $_POST['review_text'];
    $rating = $_POST['rating'];

    // Валидация данных
    if (empty($clientName) || empty($reviewText) || empty($rating)) {
        die("Все поля обязательны для заполнения.");
    }

    // Сохранение отзыва в базу данных
    $stmt = $pdo->prepare("INSERT INTO reviews (ClientName, ReviewText, Rating) VALUES (?, ?, ?)");
    $stmt->execute([$clientName, $reviewText, $rating]);

    // Перенаправление на главную страницу с сообщением об успехе
    header("Location: index.php?review=success");
    exit();
}