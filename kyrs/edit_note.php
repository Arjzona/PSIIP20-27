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

// Получение данных записи
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM note WHERE Id = ?");
    $stmt->execute([$id]);
    $note = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$note) {
        die("Запись не найдена.");
    }
}

// Обработка сохранения изменений
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_note'])) {
    $id = $_POST['id'];
    $clientId = $_POST['client_id'];
    $serviceId = $_POST['service_id'];
    $masterId = $_POST['master_id'];
    $time = $_POST['time'];
    $phone = $_POST['phone'];

    $stmt = $pdo->prepare("UPDATE note SET Id_client = ?, Id_service = ?, Id_master = ?, Time = ?, Phone_client = ? WHERE Id = ?");
    $stmt->execute([$clientId, $serviceId, $masterId, $time, $phone, $id]);

    header("Location: admin.php");
    exit();
}

// Получение списка клиентов, услуг и мастеров
$clients = $pdo->query("SELECT Id, Fam, Name, Lastfam FROM client")->fetchAll(PDO::FETCH_ASSOC);
$services = $pdo->query("SELECT * FROM service")->fetchAll(PDO::FETCH_ASSOC);
$masters = $pdo->query("SELECT * FROM master")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование записи</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <h1>Редактирование записи</h1>
    <form method="POST">
        <input type="hidden" name="id" value="<?= $note['Id'] ?>">
        <select name="client_id" required>
            <option value="">Выберите клиента</option>
            <?php foreach ($clients as $client): ?>
                <option value="<?= $client['Id'] ?>" <?= $client['Id'] == $note['Id_client'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($client['Fam'] . ' ' . $client['Name'] . ' ' . $client['Lastfam']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <select name="service_id" required>
            <option value="">Выберите услугу</option>
            <?php foreach ($services as $service): ?>
                <option value="<?= $service['Id'] ?>" <?= $service['Id'] == $note['Id_service'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($service['Name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <select name="master_id" required>
            <option value="">Выберите мастера</option>
            <?php foreach ($masters as $master): ?>
                <option value="<?= $master['Id'] ?>" <?= $master['Id'] == $note['Id_master'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($master['Fam'] . ' ' . $master['Name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <input type="datetime-local" name="time" value="<?= date('Y-m-d\TH:i', strtotime($note['Time'])) ?>" required>
        <input type="tel" name="phone" value="<?= htmlspecialchars($note['Phone_client']) ?>" required>
        <button type="submit" name="edit_note">Сохранить изменения</button>
    </form>
</body>
</html>