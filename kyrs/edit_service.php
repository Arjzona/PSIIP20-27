<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Доступ запрещён.");
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

// Получение данных услуги
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM service WHERE Id = ?");
    $stmt->execute([$id]);
    $service = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$service) {
        die("Услуга не найдена.");
    }
}

// Обработка сохранения изменений
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_service'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];

    $stmt = $pdo->prepare("UPDATE service SET Name = ?, description = ?, Price = ?, Id_category = ? WHERE Id = ?");
    $stmt->execute([$name, $description, $price, $category_id, $id]);

    header("Location: admin.php");
    exit();
}

// Получение списка категорий
$categories = $pdo->query("SELECT * FROM category")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование услуги</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <h1>Редактирование услуги</h1>
    <form method="POST">
        <input type="hidden" name="id" value="<?= $service['Id'] ?>">
        <input type="text" name="name" value="<?= htmlspecialchars($service['Name']) ?>" required>
        <input type="text" name="description" value="<?= htmlspecialchars($service['description']) ?>" required>
        <input type="number" name="price" step="0.01" value="<?= htmlspecialchars($service['Price']) ?>" required>
        <select name="category_id" required>
            <?php foreach ($categories as $category): ?>
                <option value="<?= $category['Id'] ?>" <?= $category['Id'] == $service['Id_category'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($category['Name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" name="edit_service">Сохранить изменения</button>
    </form>
</body>
</html>