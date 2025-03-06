<?php
function getDayOfYear($dateString) {
    $date = new DateTime($dateString);
    $start = new DateTime($date->format('Y-01-01'));
    $diff = $date->diff($start);
    return $diff->days + 1;
}
?>
