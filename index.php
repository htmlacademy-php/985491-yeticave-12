<?php
require_once('config.php');
require_once('user_function.php');

$sql_read_open_lots = "SELECT lots.id, lots.name, start_price AS price, url_image AS URL_pict, lots.date_end, categories.name AS category FROM lots JOIN categories 
ON lots.category = categories.id WHERE (lots.winner IS NULL) AND (lots.date_end > NOW()) ORDER BY date_create DESC";
$result_open_lots = mysqli_query($connect, $sql_read_open_lots);
$products = mysqli_fetch_all($result_open_lots, MYSQLI_ASSOC);

for ($i = 0; $i < count($products); $i++) {        
    $id = (int)$products[$i]['id'];
    
    $sql_read_bet = "SELECT bets.date_create, bets.price FROM bets WHERE bets.lot = '$id' ORDER BY bets.date_create DESC ";
    $result_bet = mysqli_query($connect, $sql_read_bet);
    $bet_open_lot = mysqli_fetch_all($result_bet, MYSQLI_ASSOC);    
    
    if ((int)$bet_open_lot[0]['price'] > $products['price']) {
        $products[$i]['price'] = $bet_open_lot[0]['price'];        
    }
} 

$content_page = include_template('main.php', ['products' => $products, 'categories' => $categories]);
$page = include_template('layout.php', ['categories' => $categories, 'content_page' => $content_page, 'name_page' => 'Главная', 'user_name' => $user_name, 'is_auth' => $is_auth]);
print($page);
?>


