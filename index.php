<?php
require_once('bootstrap.php');
require_once('functions/subsidiary.php');
require_once('functions/template.php');
require_once('functions/datetime.php');
require_once('functions/get_from_get_or_post.php');

$sql_read_open_lots = "SELECT lots.id, lots.name, start_price AS price, url_image AS URL_pict, lots.date_end, categories.name AS category FROM lots JOIN categories
ON lots.category_id = categories.id WHERE (lots.winner_id IS NULL) AND (lots.date_end > NOW()) ORDER BY date_create DESC";
$products = db_read_all($connection, $sql_read_open_lots);

$products = update_price($connection, $products);

print_page('main.php', ['products' => $products, 'categories' => $categories], 'Главная');
?>


