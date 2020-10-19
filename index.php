<?php
require_once('config.php');
require_once('user_function.php');

$sql_read_open_lots = "SELECT lots.id, lots.name, start_price AS price, url_image AS URL_pict, date_end, categories.name AS category FROM lots JOIN categories 
ON lots.category = categories.id WHERE lots.winner IS NULL ORDER BY date_create DESC";
$result_open_lots = mysqli_query($connect, $sql_read_open_lots);
$products = mysqli_fetch_all($result_open_lots, MYSQLI_ASSOC);

$sql_read_categories = "SELECT * FROM categories";
$result_categories = mysqli_query($connect, $sql_read_categories);
$categories = mysqli_fetch_all($result_categories, MYSQLI_ASSOC);


$content_page = include_template('main.php', $data = ['products' => $products, 'categories' => $categories]);
$page = include_template('layout.php', $data = ['categories' => $categories, 'content_page' => $content_page, 'name_page' => 'Главная', 'user_name' => $user_name, 'is_auth' => $is_auth]);
print($page);
?>


