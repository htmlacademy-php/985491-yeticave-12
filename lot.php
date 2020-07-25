<?php
require('config.php');

$is_auth = rand(0, 1);

$user_name = 'Mikhail'; // укажите здесь ваше имя

$connect = mysqli_connect("localhost", "mysql", "mysql", "yeticave");
if ($connect == false) {
    exit("Ошибка подключения: " . mysqli_connect_error());
}

mysqli_set_charset($connect, "utf8");

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

/*function get_dt_range_back(string $date_create): array {
    $diff = strtotime("now") - strtotime($date_create);
    $back_time = [floor($diff/3600), floor(($diff % 3600)/60)];

    /*if ($back_time[0] <10) {
        $back_time[0] = '0' . $back_time[0];
    }
    if ($back_time[1] <10) {
        $back_time[1] = '0' . $back_time[1];
    }
    if ($back_time[0] > 0) {
      $back_time[2] = $back_time[0] . ' часа ';
    }
    if ($back_time[1] > 0) {
      $back_time[2] = $back_time[2] . $back_time[1] . ' минут назад';
    }
    $timed = $back_time[2];
    return $back_time;    
}*/
/*
function min_bet(int $start_price, int $step_price): int {
  return $start_price + $step_price;
}*/

/*$sql_read_open_lots = "SELECT lots.name, start_price AS price, url_image AS URL_pict, date_end, categories.name AS category FROM lots JOIN categories 
ON lots.category = categories.id WHERE lots.winner IS NULL ORDER BY date_create DESC";
$result_open_lots = mysqli_query($connect, $sql_read_open_lots);
$products = mysqli_fetch_all($result_open_lots, MYSQLI_ASSOC);*/

/*$sql_read_lot = "SELECT lots.name, start_price AS price, url_image AS URL_pict, date_end, categories.name AS category, bets.date_create, bets.price, users.name FROM lots JOIN categories ON lots.category = categories.id LEFT JOIN bets ON lots.id = bets.lot LEFT JOIN users ON bets.user = users.id WHERE lots.id = 2";*/

if (isset($_GET['id'])) {
  $id = $_GET['id'];
}
else {
  http_response_code(404);
  exit("Ошибка подключения: 404 Not found");
}


$sql_read_lot = "SELECT lots.date_create, lots.name, lots.description, lots.url_image, lots.start_price, lots.date_end, lots.step_price, categories.name AS name_category FROM lots JOIN categories ON lots.category = categories.id WHERE lots.id ='" . $id ."'";
$result_lot = mysqli_query($connect, $sql_read_lot);

//$open_lot = mysqli_fetch_all($result_lot, MYSQLI_ASSOC);
$open_lot = mysqli_fetch_array($result_lot, MYSQLI_ASSOC);

if ($open_lot==NULL) {
  http_response_code(404);
  exit("Ошибка подключения: 404 Not found");
}

/*$sql_read_bet = "SELECT bets.date_create, bets.price, users.name FROM bets JOIN users ON bets.user = users.id WHERE bets.lot ='" . $id ."'";
$result_bet = mysqli_query($connect, $sql_read_bet);
$bet_open_lot = mysqli_fetch_all($result_bet, MYSQLI_ASSOC);*/


$sql_read_categories = "SELECT * FROM categories";
$result_categories = mysqli_query($connect, $sql_read_categories);
$categories = mysqli_fetch_all($result_categories, MYSQLI_ASSOC);


$content_page = include_template('content_lot.php', $data = ['open_lot' => $open_lot, 'categories' => $categories, 'bet_open_lot' => $bet_open_lot]);
$page = include_template('layout.php', $data = ['categories' => $categories, 'content_page' => $content_page, 'name_page' => htmlspecialchars($open_lot['name']) , 'user_name' => $user_name, 'is_auth' => $is_auth]);
print($page);

?>

