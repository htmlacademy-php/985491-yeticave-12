<?php
/**
 * Получает разницу между текущим временем и поданным на вход
 *
 *
 * @param string $date_end Время в виде строки
 *
 * @return array Возвращает часы и минуты в массиве
 */
function get_dt_range(string $date_end): array {
    $diff = strtotime($date_end) - strtotime("now");
    $end_time = [floor($diff/3600), floor(($diff % 3600)/60)];

    if ($end_time[0] < 0 || $end_time[1] < 0) {
        $end_time[0] = 0;
        $end_time[1] = 0;
    }

    if ($end_time[0] <10) {
        $end_time[0] = '0' . $end_time[0];
    }
    if ($end_time[1] <10) {
        $end_time[1] = '0' . $end_time[1];
    }

    return $end_time;
}
/**
 * Получает разницу между текущим временем и поданным на вход.
 * Данная функция возвращает разницу включая секунды
 *
 * @param string $date_end Время в виде строки
 *
 * @return array Возвращает часы, минуты и секунды в массиве
 */
function get_dt_range_with_seconds(string $date_end): array {
    $diff = strtotime($date_end) - strtotime("now");
    $end_time = [floor($diff/3600), floor(($diff % 3600)/60), floor(($diff % 3600)%60)];

    if ($end_time[0] < 0 || $end_time[1] < 0 || $end_time[2] < 0) {
        $end_time[0] = '00';
        $end_time[1] = '00';
        $end_time[2] = '00';
        return $end_time;
    }

    if ($end_time[0] <10) {
        $end_time[0] = '0' . $end_time[0];
    }
    if ($end_time[1] <10) {
        $end_time[1] = '0' . $end_time[1];
    }
    if ($end_time[2] <10) {
        $end_time[2] = '0' . $end_time[2];
    }

    return $end_time;
}
