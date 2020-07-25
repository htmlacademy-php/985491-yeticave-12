<?php
require('config.php');
$is_auth = rand(0, 1);

$user_name = 'Mikhail'; // укажите здесь ваше имя

$connect = mysqli_connect("localhost", "mysql", "mysql", "yeticave");
if ($connect == false) {
    exit("Ошибка подключения: " . mysqli_connect_error());
}

mysqli_set_charset($connect, "utf8");

$sql_read_open_lots = "SELECT lots.id, lots.name, start_price AS price, url_image AS URL_pict, date_end, categories.name AS category FROM lots JOIN categories 
ON lots.category = categories.id WHERE lots.winner IS NULL ORDER BY date_create DESC";
$result_open_lots = mysqli_query($connect, $sql_read_open_lots);
$products = mysqli_fetch_all($result_open_lots, MYSQLI_ASSOC);

$sql_read_categories = "SELECT * FROM categories";
$result_categories = mysqli_query($connect, $sql_read_categories);
$categories = mysqli_fetch_all($result_categories, MYSQLI_ASSOC);

?>
<?php

function format_price(int $price): string {
    $price = ceil($price);
    if ($price < 1000) {        
        return $price . ' ₽';
    }     
    
    return number_format($price, 0, ".", " ") . ' ₽'; 
}

function get_dt_range(string $date_end): array {
    $diff = strtotime($date_end) - strtotime("now");
    $end_time = [floor($diff/3600), floor(($diff % 3600)/60)];

    if ($end_time[0] <10) {
        $end_time[0] = '0' . $end_time[0];
    }
    if ($end_time[1] <10) {
        $end_time[1] = '0' . $end_time[1];
    }

    return $end_time;    
}



$content_page = include_template('main.php', $data = ['products' => $products, 'categories' => $categories]);
$page = include_template('layout.php', $data = ['categories' => $categories, 'content_page' => $content_page, 'name_page' => 'Главная', 'user_name' => $user_name, 'is_auth' => $is_auth]);
print($page);
?>


