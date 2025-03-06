<?php
$array = [5, 2, 8, 3, 6];
echo "Исходный массив: " . implode(", ", $array) . "<br>";

$minElement = min($array);
echo "Минимальный элемент: " . $minElement . "<br>";

$array[count($array) - 1] = $minElement;
echo "Измененный массив: " . implode(", ", $array) . "<br>"
?>