<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Результаты заданий</title>
</head>
<body>
    <h1>Результаты заданий</h1>

    <h2>Задание 1: Порядковый номер дня в году</h2>
    <?php include 't1.php'; ?>
    <?php echo "Порядковый номер дня для даты : " . getDayOfYear("2025-06-03") . "<br>"; ?>

    <h2>Задание 2: Фамилия и Имя</h2>
    <?php include 't2.php'; ?>

    <h2>Задание 3: Работа с массивами</h2>
    <?php include 't3.php'; ?>

    <h2>Задание 4: Работа со строками</h2>
    <?php include 't4.php'; ?>

    <h2>Задание 5: Пользовательская функция</h2>
    <?php include 't5.php'; ?>
</body>
</html>