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

// Получение списка категорий и услуг
$categories = $pdo->query("SELECT Id, Name FROM category")->fetchAll(PDO::FETCH_ASSOC);
$services = $pdo->query("SELECT Id, Id_category, Name, description, Price FROM service")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Наши услуги</title>
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

        .search-filter-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .search-filter-container input, .search-filter-container select {
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #444;
            background-color: #333;
            color: #fff;
        }

        .category-container {
            margin-bottom: 40px;
        }

        .category-container h2 {
            color: #cf54bf;
        }

        .service-card {
            background-color: #333;
            padding: 15px;
            border-radius: 10px;
            margin: 10px 0;
        }

        .service-card h3 {
            margin: 0;
        }

        .service-card p {
            margin: 5px 0;
        }

        .service-card .price {
            color: #cf54bf;
            font-weight: bold;
        }

        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <h1>Наши услуги</h1>

    <!-- Поиск и фильтрация -->
    <div class="search-filter-container">
        <input type="text" id="searchInput" placeholder="Поиск по услугам..." oninput="filterServices()">
        <select id="categoryFilter" onchange="filterServices()">
            <option value="all">Все категории</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= htmlspecialchars($category['Id']) ?>"><?= htmlspecialchars($category['Name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Список категорий и услуг -->
    <?php foreach ($categories as $category): ?>
        <div class="category-container" data-category-id="<?= htmlspecialchars($category['Id']) ?>">
            <h2><?= htmlspecialchars($category['Name']) ?></h2>
            <?php foreach ($services as $service): ?>
                <?php if ($service['Id_category'] == $category['Id']): ?>
                    <div class="service-card" data-service-name="<?= htmlspecialchars(strtolower(trim($service['Name']))) ?>">
                        <h3><?= htmlspecialchars($service['Name']) ?></h3>
                        <p><?= htmlspecialchars($service['description']) ?></p>
                        <p class="price">Цена: <?= htmlspecialchars($service['Price']) ?> руб.</p>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>

    <script>
        function filterServices() {
            const searchQuery = document.getElementById('searchInput').value.trim(); // Удаляем лишние пробелы и приводим к нижнему регистру
            const selectedCategory = document.getElementById('categoryFilter').value;

            document.querySelectorAll('.category-container').forEach(category => {
                const categoryId = category.getAttribute('data-category-id');
                let hasVisibleServices = false;

                category.querySelectorAll('.service-card').forEach(service => {
                    const serviceName = service.getAttribute('data-service-name'); // Название услуги в нижнем регистре
                    const matchesSearch = serviceName.includes(searchQuery); // Поиск по любой части названия
                    const matchesCategory = selectedCategory === 'all' || selectedCategory === categoryId;

                    if (matchesSearch && matchesCategory) {
                        service.classList.remove('hidden');
                        hasVisibleServices = true;
                    } else {
                        service.classList.add('hidden');
                    }
                });

                // Скрыть категорию, если в ней нет видимых услуг
                if (hasVisibleServices) {
                    category.classList.remove('hidden');
                } else {
                    category.classList.add('hidden');
                }
            });
        }
    </script>
</body>
</html>