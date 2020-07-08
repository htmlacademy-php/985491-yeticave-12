<?php
require('config.php');
$is_auth = rand(0, 1);

$user_name = 'Mikhail'; // укажите здесь ваше имя
?>
<?php
$categories = ["Доски и лыжи", "Крепления", "Ботинки", "Одежда", "Инструменты", "Разное"];

$products = [
    [
    'name' => "2014 Rossingnol District Snowboard",
    'category' => $categories[0],
    'price' => 10999,
    'URL_pict' => 'img/lot-1.jpg',
    'date_end' => '2020-07-09',
    ],
    [
    'name' => "DC Ply Mens 2016/2017 Snowboard",
    'category' => $categories[0],
    'price' => 159999,
    'URL_pict' => 'img/lot-2.jpg',
    'date_end' => '2020-07-10',
    ],
    [
    'name' => "Крепления Union Contact Pro 2015 года размер L/XL",
    'category' => $categories[1],
    'price' => 8000,
    'URL_pict' => 'img/lot-3.jpg',
    'date_end' => '2020-07-20',
    ],
    [
    'name' => "Ботинки для сноуборда DC Mutiny Charocal",
    'category' => $categories[2],
    'price' => 10999,
    'URL_pict' => 'img/lot-4.jpg',
    'date_end' => '2020-07-09',
    ],
    [
    'name' => "Куртка для сноуборда DC Mutiny Charocal",
    'category' => $categories[3],
    'price' => 7500,
    'URL_pict' => 'img/lot-5.jpg',
    'date_end' => '2020-07-10',
    ],
    [
    'name' => "Маска Oakley Canopy",
    'category' => $categories[5],
    'price' => 5400,
    'URL_pict' => 'img/lot-6.jpg',
    'date_end' => '2020-07-14',
    ],
];           

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

require('helpers.php');

$content_page = include_template('main.php', $data = ['products' => $products, 'categories' => $categories]);
$page = include_template('layout.php', $data = ['products' => $products, 'categories' => $categories, 'content_page' => $content_page, 'name_page' => 'Главная', 'user_name' => $user_name, 'is_auth' => $is_auth]);
print($page);
?>


