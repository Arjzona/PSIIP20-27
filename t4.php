<?php
$S1 = "Алексейчук";
$S2 = "новооктябрская 14";

echo "Длина строки S2: " . strlen($S2) . "<br>";

$concatenatedString = $S1 . " " . $S2;
echo "Сцепление строк: " . $concatenatedString . "<br>";

$lowerCaseS2 = strtolower($S2);
echo "S2 в нижнем регистре: " . $lowerCaseS2 . "<br>";
?>