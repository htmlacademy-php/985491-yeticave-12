<?php
/**
 * Выводит на экран страницу
 *
 *
 * @param string $name_template_content Имя шаблона выводимой страницы
 * @param array $array_arguments Массив с аргументами для заполнения шаблона страницы
 * @param string $name_page Имя страницы (будет отображаться как имя вкладки)
 *
 * @return void
 */
function print_page(string $name_template_content, array $array_arguments, string $name_page) : void
{
    $content_page = include_template($name_template_content, $array_arguments);
    $page = include_template('layout.php', ['categories' => $array_arguments['categories'], 'content_page' => $content_page, 'name_page' => $name_page]);
    print($page);
}
