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

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header("Location: autification.php"); // Перенаправление на страницу регистрации
    exit();
}

// Получение данных пользователя
$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT Fam, Name, Lastfam, Login FROM client WHERE Id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Пользователь не найден.");
}

// Выход из аккаунта
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

// Получение записей пользователя
$stmt = $pdo->prepare("
    SELECT note.Id, note.Time, note.Phone_client, 
           service.Name AS service_name, 
           master.Fam AS master_fam, master.Name AS master_name 
    FROM note 
    JOIN service ON note.Id_service = service.Id 
    JOIN master ON note.Id_master = master.Id 
    WHERE note.Id_client = ?
");
$stmt->execute([$userId]);
$notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет</title>
    <link rel="stylesheet" href="account.css">
</head>
<body>
    <div class="account-container">
        <h2>Личный кабинет</h2>
        <p><strong>Фамилия:</strong> <?= htmlspecialchars($user['Fam']) ?></p>
        <p><strong>Имя:</strong> <?= htmlspecialchars($user['Name']) ?></p>
        <p><strong>Отчество:</strong> <?= htmlspecialchars($user['Lastfam']) ?></p>
        <p><strong>Почта:</strong> <?= htmlspecialchars($user['Login']) ?></p>

        <form method="POST">
            <button type="submit" name="logout">Выйти</button>
        </form>
        <a href="indexsesion.php" class="back-button">На главную</a>
    </div>

    <!-- Отображение записей пользователя -->
    <div class="account-container">
        <h2>Мои записи</h2>
        <?php if (count($notes) > 0): ?>
            <table>
                <tr>
                    <th>Услуга</th>
                    <th>Мастер</th>
                    <th>Дата и время</th>
                    <th>Телефон</th>
                </tr>
                <?php foreach ($notes as $note): ?>
                    <tr>
                        <td><?= htmlspecialchars($note['service_name']) ?></td>
                        <td><?= htmlspecialchars($note['master_fam'] . ' ' . $note['master_name']) ?></td>
                        <td><?= htmlspecialchars($note['Time']) ?></td>
                        <td><?= htmlspecialchars($note['Phone_client']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>У вас пока нет записей.</p>
        <?php endif; ?>
    </div>
</body>
</html>