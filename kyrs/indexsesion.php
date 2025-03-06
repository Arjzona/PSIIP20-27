<?php
// Подключение к базе данных
$host = 'localhost';
$dbname = 'stylist';
$user = 'root';
$password = '8308';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password); //подключение к PMA
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

$registrationSuccess = isset($_GET['registration']) && $_GET['registration'] === 'success'; //успешная регистрация

// Получение списка услуг
$services = $pdo->query("SELECT * FROM service")->fetchAll(PDO::FETCH_ASSOC);

// Получение списка мастеров
$masters = $pdo->query("SELECT * FROM master")->fetchAll(PDO::FETCH_ASSOC);

$registrationSuccess = isset($_GET['registration']) && $_GET['registration'] === 'success'; // Успешная регистрация

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StyList</title>
    <link rel="stylesheet" href="slide.css">
    <link rel="stylesheet" href="index.css">
    <script src = "indexsesion.js"></script>
</head>
<body>
    <?php if ($registrationSuccess): ?>
        <div class="notification">Регистрация прошла успешно!</div>
    <?php endif; ?>

    <?php if (isset($_GET['review']) && $_GET['review'] === 'success'): ?>
        <div class="notification">Спасибо за ваш отзыв!</div>
    <?php endif; ?>

    <header>
        <div class="logo">StyList</div>
        <nav>
            <div class="burger" onclick="toggleMenu()">
                <div></div>
                <div></div>
                <div></div>
            </div>
            <ul id="nav-menu">
                <li><a href="account.php">Аккаунт</a></li>
                <li><a href="index4.php">Услуги</a></li>
                <li><a href="index3.php">Мастера</a></li>
            </ul>
        </nav>
    </header>

    <!-- Слайдер для новостей и акций -->
    <div id="slider">
        <div class="slides">
            <!-- First slide -->
            <div class="slider">
                <div class="legend"></div>
                <div class="content">
                    <div class="content-txt">
                        <h1>40%</h1>
                        <h2>На первое окрашивание</h2>
                    </div>
                </div>
                <div class="images">
                    <img src="img/1.jpg">
                </div>
            </div>

            <!-- Second slide -->
            <div class="slider">
                <div class="legend"></div>
                <div class="content">
                    <div class="content-txt">
                        <h1>ПОДАРОЧНЫЕ СЕРТИФИКАТЫ</h1>
                        <h2>покупайте в салоне</h2>
                    </div>
                </div>
                <div class="images">
                    <img src="img/2.jpg">
                </div>
            </div>

            <!-- Third slide -->
            <div class="slider">
                <div class="legend"></div>
                <div class="content">
                    <div class="content-txt">
                        <h1>Новые мастера</h1>
                        <h2>Новая услуга: завивка</h2>
                    </div>
                </div>
                <div class="images">
                    <img src="img/3.png">
                </div>
            </div>
        </div>
    </div>

    <main>
    <div class="welcome-message">
        <h1>Добро пожаловать, дорогой гость!</h1>
    </div>
</main>

    <footer>
        &copy; 2023 Парикмахерская. Все права защищены.
    </footer>

    <button class="online-booking" onclick="openModal()">Онлайн запись</button>

    <div class="overlay" id="overlay"></div>
<div class="modal" id="modal">
    <h2>Онлайн запись</h2>
    <form method="POST" action="book_service.php">
        <select name="service_id" required>
            <option value="">Выберите услугу</option>
            <?php foreach ($services as $service): ?>
                <option value="<?= $service['Id'] ?>">
                    <?= htmlspecialchars($service['Name']) ?> (<?= htmlspecialchars($service['Price']) ?> руб.)
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
        <input type="date" name="date" required>
        <input type="time" name="time" required>
        <input type="tel" name="phone" placeholder="Номер телефона" required>
        <button type="submit">Записаться</button>
        <button type="button" onclick="closeModal()">Закрыть</button>
    </form>
</div>

<!-- Форма для отзыва -->
<section class="review-section">
    <h2>Оставьте отзыв</h2>
    <form id="reviewForm" method="POST">
        <input type="text" name="client_name" placeholder="Ваше имя" required>
        <textarea name="review_text" placeholder="Ваш отзыв" required></textarea>
        <select name="rating" required>
            <option value="">Оцените нас</option>
            <option value="5">5 - Отлично</option>
            <option value="4">4 - Хорошо</option>
            <option value="3">3 - Удовлетворительно</option>
            <option value="2">2 - Плохо</option>
            <option value="1">1 - Ужасно</option>
        </select>
        <button type="submit" id="submitReview">Отправить отзыв</button>
    </form>
    <div id="reviewMessage"></div> <!-- Сообщение об успешной отправке или ошибке -->
</section>

<!-- Стили для формы отзыва -->
<style>
    .review-section {
        margin: 20px auto;
        padding: 20px;
        max-width: 600px;
        background: rgb(213, 164, 202);
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .review-section h2 {
        text-align: center;
        margin-bottom: 20px;
    }

    .review-section form {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .review-section input,
    .review-section textarea,
    .review-section select {
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 16px;
    }

    .review-section textarea {
        resize: vertical;
        height: 100px;
    }

    .review-section button {
        padding: 10px;
        background: #cf54bf;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
    }

    .review-section button:hover {
        background: #cf54bf;
    }

    .notification {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 15px;
    background: #4CAF50;
    color: white;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    z-index: 1000;
}
</style>

<?php if (isset($_GET['booking']) && $_GET['booking'] === 'success'): ?>
    <div class="notification">Запись на услугу успешно оформлена!</div>
<?php endif; ?>  
</body>
</html>