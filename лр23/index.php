<?php
// 1. Вывод текущей даты и времени в три строки
$date = date('j. m. Y');
$time = date('H:i:s');
$day_of_week = date('l');

echo "Дата: $date <br>";
echo "Время: $time <br>";
echo "День недели: $day_of_week <br>";

// 2. Функция для возврата дня недели на русском языке
function getRussianDayOfWeek($date) {
    $days = ['Mon' => 'понедельник', 'Tue' => 'вторник', 'Wed' => 'среда', 'Thu' => 'четверг', 'Fri' => 'пятница', 'Sat' => 'суббота', 'Sun' => 'воскресенье'];
    $weekday = date('D', strtotime($date));
    return $days[$weekday];
}

// Проверка функции для текущей даты
$today = date('Y-m-d');
$russian_day_of_week = getRussianDayOfWeek($today);
echo "Сегодня $russian_day_of_week <br>";

// 3. Текст программы для вывода текущей даты в числовом формате и формы с одной кнопкой
?>
<!DOCTYPE html>
<html>
<head>
    <title>Вывод даты и дня недели</title>
</head>
<body>
    <form method="post">
        <label>Текущая дата: <?php echo date('Y-m-d'); ?></label>
        <button type="submit" name="submit">Показать день недели</button>
    </form>
    <?php
    // Обработка нажатия кнопки
    if(isset($_POST['submit'])) {
        // Вывод дня недели на русском языке
        $russian_day_of_week = getRussianDayOfWeek(date('Y-m-d'));
        echo "<p>Сегодня $russian_day_of_week</p>";
    }
    ?>
</body>
</html>