<?php
require_once('bootstrap.php');
require_once('user_function.php');
require_once('functions/subsideary.php');
require_once('functions/template.php');

$sql_read_open_lots = "SELECT lots.id, lots.name, start_price AS price, url_image AS URL_pict, lots.date_end, categories.name AS category FROM lots JOIN categories
ON lots.category_id = categories.id WHERE (lots.winner_id IS NULL) AND (lots.date_end > NOW()) ORDER BY date_create DESC";
$products = db_read($connection, $sql_read_open_lots);

$products = subsideary_update_price($connection, $products);

print_page('main.php', ['products' => $products, 'categories' => $categories], 'Главная');

/* $content_page = include_template('main.php', ['products' => $products, 'categories' => $categories]);
$page = include_template('layout.php', ['categories' => $categories, 'content_page' => $content_page, 'name_page' => 'Главная']);
print($page); */
?>


