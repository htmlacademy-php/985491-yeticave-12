<?php
/**
 * Выводит на экран страницу
 * 
 *
 * @param mysqli $connection Ресурс соединения
 * @param string $query Строка запроса на чтение
 *
 * @return array Ассоциативный массив результата запроса
 */
function print_page(string $name_template_content, array $array_arguments, string $name_page) : void
{
    $content_page = include_template($name_template_content, $array_arguments);
    $page = include_template('layout.php', ['categories' => $array_arguments['categories'], 'content_page' => $content_page, 'name_page' => $name_page]);
    print($page);
}