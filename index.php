<?php
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
    'URL_pict' => "img/lot-1.jpg"
    ],
    [
    'name' => "DC Ply Mens 2016/2017 Snowboard",
    'category' => $categories[0],
    'price' => 159999,
    'URL_pict' => "img/lot-2.jpg"
    ],
    [
    'name' => "Крепления Union Contact Pro 2015 года размер L/XL",
    'category' => $categories[1],
    'price' => 8000,
    'URL_pict' => "img/lot-3.jpg"
    ],
    [
    'name' => "Ботинки для сноуборда DC Mutiny Charocal",
    'category' => $categories[2],
    'price' => 10999,
    'URL_pict' => "img/lot-4.jpg"
    ],
    [
    'name' => "Куртка для сноуборда DC Mutiny Charocal",
    'category' => $categories[3],
    'price' => 7500,
    'URL_pict' => "img/lot-5.jpg"
    ],
    [
    'name' => "Маска Oakley Canopy",
    'category' => $categories[5],
    'price' => 5400,
    'URL_pict' => "img/lot-6.jpg"
    ],
];           

function format_price(int $price): string {
    $price = ceil($price);
    if ($price < 1000) {        
        return $price . ' ₽';
    }     
    
    return number_format($price, 0, ".", " ") . ' ₽'; 
}

require('helpers.php');

$content_page = include_template('main.php', $data = ['products' => $products, 'categories' => $categories]);
$page = include_template('layout.php', $data = ['products' => $products, 'categories' => $categories, 'content_page' => $content_page, 'name_page' => 'Главная', 'user_name' => $user_name, 'is_auth' => $is_auth]);
print($page);
?>


