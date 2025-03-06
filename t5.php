<?php
function calculateY($x) {
    try {
        if (!is_numeric($x)) throw new Exception("Входное значение должно быть числом");
        return pow($x - 2, 2);
    } catch (Exception $e) {
        return "Ошибка: " . $e->getMessage();
    }
}

$x = 5; 
$y = calculateY($x);
echo "Результат расчета y = (x-2)^2 для x={$x}: {$y}";
?>