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

// Получение списка мастеров
$stmt = $pdo->query("SELECT Id, Fam, Name, Lastfam, Fhoto FROM master");
$masters = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Наши мастера</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #1a1a1a;
            color: #fff;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
        }

        .master-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .master-card {
            background-color: #333;
            padding: 20px;
            border-radius: 10px;
            width: 200px;
            text-align: center;
        }

        .master-card img {
            width: 100%;
            border-radius: 10px;
        }

        .master-card h3 {
            margin: 10px 0;
        }

        .master-card p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <h1>Наши мастера</h1>
    <div class="master-container">
        <?php foreach ($masters as $master): ?>
            <div class="master-card">
                <img src="<?= htmlspecialchars($master['Fhoto']) ?>" alt="Фото мастера">
                <h3><?= htmlspecialchars($master['Fam']) ?> <?= htmlspecialchars($master['Name']) ?></h3>
                <p><?= htmlspecialchars($master['Lastfam']) ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>