<?php
/**
 * Получает данные из $_POST по ключу
 *
 *
 * @param string $name Ключ
 *
 * @return string
 */
function get_post_val(string $name): string {
    return $_POST[$name] ?? "";
}

/**
 * Получает данные из $_POST по ключу
 * С исключением опасных символов для защиты от XSS вставки JS
 *
 * @param string $name Ключ
 *
 * @return string
 */
function get_filtered_post_val(string $name): string {
    return htmlspecialchars(get_post_val($name)) ?? "";
}

/**
 * Получает данные из $_GET по ключу
 * С исключением опасных символов для защиты от XSS вставки JS
 *
 * @param string $name Ключ
 *
 * @return string
 */
function get_filtered_get_val(string $name): string {
    return htmlspecialchars($_GET[$name]) ?? "";
}
