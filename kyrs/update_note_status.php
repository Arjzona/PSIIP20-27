<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die(json_encode(['success' => false, 'message' => 'Доступ запрещён.']));
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
    die(json_encode(['success' => false, 'message' => 'Ошибка подключения к базе данных.']));
}

// Получение данных из запроса
$data = json_decode(file_get_contents('php://input'), true);
$noteId = $data['noteId'];
$statusType = $data['statusType'];
$statusValue = $data['statusValue'];

// Обновление статуса оплаты
try {
    $stmt = $pdo->prepare("UPDATE note SET IsPaid = ? WHERE Id = ?");
    $stmt->execute([$statusValue, $noteId]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Ошибка при обновлении статуса оплаты.']);
}