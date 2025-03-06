<?php
session_start();

// Проверка роли пользователя
if (!isset($_SESSION['role'])) { // Ошибка была здесь: лишняя скобка
    header("Location: autification.php");
    exit();
}

if ($_SESSION['role'] !== 'admin') {
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

// Обработка добавления мастера
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_master'])) {
    $fam = $_POST['fam'];
    $name = $_POST['name'];
    $lastfam = $_POST['lastfam'];
    
    $foto = $_POST['foto'];

    $stmt = $pdo->prepare("INSERT INTO master (Fam, Name, Lastfam, Fhoto) VALUES (?, ?, ?, ?)");
    $stmt->execute([$fam, $name, $lastfam, $foto]);
}

// Обработка удаления мастера
if (isset($_GET['delete_master'])) {
    $id = $_GET['delete_master'];
    $stmt = $pdo->prepare("DELETE FROM master WHERE Id = ?");
    $stmt->execute([$id]);
}

// Обработка добавления категории
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $name = $_POST['name'];

    $stmt = $pdo->prepare("INSERT INTO category (Name) VALUES (?)");
    $stmt->execute([$name]);
}

// Обработка удаления категории
if (isset($_GET['delete_category'])) {
    $id = $_GET['delete_category'];
    $stmt = $pdo->prepare("DELETE FROM category WHERE Id = ?");
    $stmt->execute([$id]);
}

// Обработка добавления услуги
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_service'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];

    $stmt = $pdo->prepare("INSERT INTO service (Name, description, Price, Id_category) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $description, $price, $category_id]);
}

// Обработка удаления услуги
if (isset($_GET['delete_service'])) {
    $id = $_GET['delete_service'];
    $stmt = $pdo->prepare("DELETE FROM service WHERE Id = ?");
    $stmt->execute([$id]);
}

// Обработка удаления услуги
if (isset($_GET['delete_note'])) {
    $id = $_GET['delete_note'];
    $stmt = $pdo->prepare("DELETE FROM note WHERE Id = ?");
    $stmt->execute([$id]);
}

// Обработка добавления записи на услугу
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_note'])) {
    $clientId = $_POST['client_id'];
    $serviceId = $_POST['service_id'];
    $masterId = $_POST['master_id'];
    $time = $_POST['time']; // Формат: YYYY-MM-DDTHH:MM
    $phone = $_POST['phone'];

    // Преобразование формата времени
    $time = DateTime::createFromFormat('Y-m-d\TH:i', $time);
    if (!$time) {
        die("Неверный формат времени.");
    }
    $time = $time->format('Y-m-d H:i:s'); // Преобразуем в формат MySQL

    // Валидация данных
    if (empty($clientId) || empty($serviceId) || empty($masterId) || empty($time) || empty($phone)) {
        die("Все поля обязательны для заполнения.");
    }

    // Сохранение записи в базу данных
    $stmt = $pdo->prepare("INSERT INTO note (Id_client, Id_service, Id_master, Time, Phone_client) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$clientId, $serviceId, $masterId, $time, $phone]);
}

// ПОЛУЧЕНИЕ МАСТЕРОВ КАТЕГОРИЙ УСЛУГ ЗАПИСЕЙ
$masters = $pdo->query("SELECT * FROM master")->fetchAll(PDO::FETCH_ASSOC);
$categories = $pdo->query("SELECT * FROM category")->fetchAll(PDO::FETCH_ASSOC);
$services = $pdo->query("SELECT * FROM service")->fetchAll(PDO::FETCH_ASSOC);

//ОБЪЕДИНЕНИЕ
$notes = $pdo->query("  
    SELECT note.*, client.Fam AS client_fam, client.Name AS client_name 
    FROM note 
    JOIN client ON note.Id_client = client.Id
")->fetchAll(PDO::FETCH_ASSOC);

// Обработка экспорта записей в Excel
if (isset($_GET['export_notes'])) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="notes.csv"');

    // Открываем поток вывода
    $output = fopen('php://output', 'w');

    // BOM для корректного отображения кириллицы в Excel
    fwrite($output, "\xEF\xBB\xBF");

    fputcsv($output, array('ID', 'Клиент', 'Услуга', 'Мастер', 'Время', 'Телефон'), ';');

    foreach ($notes as $note) {
        $clientName = $note['client_fam'] . ' ' . $note['client_name'];
        $serviceName = $services[array_search($note['Id_service'], array_column($services, 'Id'))]['Name'];
        $masterName = $masters[array_search($note['Id_master'], array_column($masters, 'Id'))]['Fam'];

        $row = array(
            $note['Id'],
            $clientName,
            $serviceName,
            $masterName,
            $note['Time'],
            $note['Phone_client']
        );

        // Записываем строку в CSV
        fputcsv($output, $row, ';');
    }

    // Закрываем поток вывода
    fclose($output);
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель</title>
    <link rel="stylesheet" href="admin.css">
    <script>
function updateNoteStatus(checkbox) {
        const noteId = checkbox.getAttribute('data-note-id');
        const statusType = checkbox.getAttribute('name');
        const statusValue = checkbox.checked ? 1 : 0;

        fetch('update_note_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                noteId: noteId,
                statusType: statusType,
                statusValue: statusValue
            })
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                alert('Ошибка при обновлении статуса.');
                checkbox.checked = !checkbox.checked; // Откат изменения
            }
        })
        .catch(error => {
            console.error('Ошибка:', error);
            checkbox.checked = !checkbox.checked; // Откат изменения
        });
    }
</script>
</head>
<body>
    <h1>Админ-панель</h1>

    <!-- Управление мастерами -->
    <div class="section">
        <h2>Мастера</h2>
        <form method="POST" novalidate>
            <input type="text" name="fam" placeholder="Фамилия" required>
            <input type="text" name="name" placeholder="Имя" required>
            <input type="text" name="lastfam" placeholder="Отчество" required>
            
            <input type="text" name="foto" placeholder="Ссылка на фото" required>
            <button type="submit" name="add_master">Добавить мастера</button>
        </form>
        <table>
            <tr>
                <th>Фамилия</th>
                <th>Имя</th>
                <th>Отчество</th>
                
                <th>Действия</th>
            </tr>
            <?php foreach ($masters as $master): ?>
                <tr>
                    <td><?= htmlspecialchars($master['Fam']) ?></td>
                    <td><?= htmlspecialchars($master['Name']) ?></td>
                    <td><?= htmlspecialchars($master['LastFam']) ?></td>
                    
                    <td>
                        <a href="?delete_master=<?= $master['Id'] ?>" class="delete">Удалить</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <!-- Управление категориями -->
    <div class="section">
        <h2>Категории услуг</h2>
        <form method="POST">
            <input type="text" name="name" placeholder="Название категории" required>
            <button type="submit" name="add_category">Добавить категорию</button>
        </form>
        <table>
            <tr>
                <th>Название</th>
                <th>Действия</th>
            </tr>
            <?php foreach ($categories as $category): ?>
                <tr>
                    <td><?= htmlspecialchars($category['Name']) ?></td>
                    <td>
                        <a href="?delete_category=<?= $category['Id'] ?>" class="delete">Удалить</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <!-- Управление услугами -->
    <div class="section">
        <h2>Услуги</h2>
        <form method="POST">
            <input type="text" name="name" placeholder="Название услуги" required>
            <input type="text" name="description" placeholder="Описание услуги" required>
            <input type="number" name="price" step="0.01" placeholder="Цена" required>
            <select name="category_id" required>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['Id'] ?>"><?= htmlspecialchars($category['Name']) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" name="add_service">Добавить услугу</button>
            
        </form>
        <table>
    <tr>
        <th>Название</th>
        <th>Описание</th>
        <th>Цена</th>
        <th>Категория</th>
        <th>Действия</th>
    </tr>
    <?php foreach ($services as $service): ?>
        <tr> 
            <td><?= htmlspecialchars($service['Name']) ?></td>
            <td><?= htmlspecialchars($service['description']) ?></td>
            <td><?= htmlspecialchars($service['Price']) ?> руб.</td>
            <td><?= htmlspecialchars($categories[array_search($service['Id_category'], array_column($categories, 'Id'))]['Name']) ?></td>
            <td>
                <a href="?delete_service=<?= $service['Id'] ?>" class="delete">Удалить</a>
                <a href="edit_service.php?id=<?= $service['Id'] ?>">Редактировать</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
    </div>
    

    <!-- Управление записями на услуги -->
<div class="section">
    <h2>Записи на услуги</h2>
    <form method="POST">
        <select name="client_id" required>
            <option value="">Выберите клиента</option>
            <?php
            $clients = $pdo->query("SELECT Id, Fam, Name, Lastfam FROM client")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($clients as $client): ?>
                <option value="<?= $client['Id'] ?>">
                    <?= htmlspecialchars($client['Fam'] . ' ' . $client['Name'] . ' ' . $client['Lastfam']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <select name="service_id" required>
            <option value="">Выберите услугу</option>
            <?php foreach ($services as $service): ?>
                <option value="<?= $service['Id'] ?>">
                <?= htmlspecialchars($service['Name']) ?> (<?= htmlspecialchars($categories[array_search($service['Id_category'], array_column($categories, 'Id'))]['Name']) ?>)
                </option>
            <?php endforeach; ?>
        </select>
        <select name="master_id" required>
            <option value="">Выберите мастера</option>
            <?php foreach ($masters as $master): ?>
                <option value="<?= $master['Id'] ?>">
                    <?= htmlspecialchars($master['Fam'] . ' ' . $master['Name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <input type="datetime-local" name="time" required>
        <input type="tel" name="phone" placeholder="Номер телефона" required>
        <button type="submit" name="add_note">Добавить запись</button>
    </form>

<?php //подсчёт суммы за месяц
if (isset($_GET['month']) && isset($_GET['year'])) {
    $month = $_GET['month'];
    $year = $_GET['year'];

    $stmt = $pdo->prepare("
        SELECT SUM(service.Price) AS TotalAmount
        FROM note
        JOIN service ON note.Id_service = service.Id
        WHERE IsPaid = 1
        AND YEAR(note.Time) = ? AND MONTH(note.Time) = ?
    ");
    $stmt->execute([$year, $month]);
    $totalAmount = $stmt->fetchColumn();

    echo "<p>Сумма выполненных и оплаченных услуг за выбранный месяц: <strong>$totalAmount руб.</strong></p>";
}

//сумма за год
if (isset($_GET['year'])) {
    $year = $_GET['year'];

    $stmt = $pdo->prepare("
        SELECT SUM(service.Price) AS TotalAmount
        FROM note
        JOIN service ON note.Id_service = service.Id
        WHERE IsPaid = 1
        AND YEAR(note.Time) = ?
    ");
    $stmt->execute([$year]);
    $totalAmount = $stmt->fetchColumn();

    echo "<p>Сумма выполненных и оплаченных услуг за год: <strong>$totalAmount руб.</strong></p>";
}
?>
    <table>
        <tr>
            <th>Клиент</th>
            <th>Услуга</th>
            <th>Мастер</th>
            <th>Время</th>
            <th>Телефон</th>
            <th>Оплачено</th>
            <th>Действия</th>
        </tr>
        <?php foreach ($notes as $note): ?>
            <tr>
                <td><?= htmlspecialchars($note['client_fam'] . ' ' . $note['client_name']) ?></td>
                <td><?= htmlspecialchars($services[array_search($note['Id_service'], array_column($services, 'Id'))]['Name']) ?></td>
                <td><?= htmlspecialchars($masters[array_search($note['Id_master'], array_column($masters, 'Id'))]['Fam']) ?></td>
                <td><?= htmlspecialchars($note['Time']) ?></td>
                <td><?= htmlspecialchars($note['Phone_client']) ?></td>
                <td>
                    <input type="checkbox" name="is_paid" data-note-id="<?= $note['Id'] ?>" 
                        <?= $note['IsPaid'] ? 'checked' : '' ?> onchange="updateNoteStatus(this)">
                </td>
                <td>
                    <a href="edit_note.php?id=<?= $note['Id'] ?>">Редактировать</a>
                    <a href="?delete_note=<?= $note['Id'] ?>" class="delete">Удалить</a>
                    <a href="?export_notes=1">Экспорт в Excel</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <form method="GET" action="">
        <label for="month">Месяц:</label>
        <select name="month" id="month">
            <option value="">Выберите месяц</option>
            <?php for ($i = 1; $i <= 12; $i++): ?>
                <option value="<?= $i ?>" <?= isset($_GET['month']) && $_GET['month'] == $i ? 'selected' : '' ?>>
                    <?= DateTime::createFromFormat('!m', $i)->format('F') ?>
                </option>
            <?php endfor; ?>
        </select>
        <label for="year">Год:</label>
        <input type="number" name="year" id="year" value="<?= $_GET['year'] ?? date('Y') ?>" required>
        <button type="submit">Показать</button>
    </form>
</div>
</body>
</html>