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
    return htmlspecialchars(get_post_val($name));
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

    return htmlspecialchars(isset($_GET[$name])) ?? "";
}/*<br /><b>Notice</b>:  Undefined index: search in <b>E:\Download\OpenServer\domains\985491-yeticave-12\functions\get_from_get_or_post.php</b> on line <b>35</b><br />*/
