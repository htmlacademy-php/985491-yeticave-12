<?php
require_once('config.php');
require_once('user_function.php');

function get_dt_range_back(string $date_create): string {
    $diff = strtotime("now") - strtotime($date_create);
    $back_time = [floor($diff/3600), floor(($diff % 3600)/60)];

    if ($diff < 3600) {
      return $back_time[1] . ' минут назад';
    }
    if ($diff < 86400) {
      return $back_time[0] . ' часов назад';
    }
    
    return date('d.m.Y в H:i', strtotime($date_create));    
}

if (isset($_GET['id'])) {
  $id = (int) $_GET['id'];  
}
else {
  http_response_code(404);
  exit("Ошибка подключения: не указан id");
}

$sql_read_lot = "SELECT lots.date_create, lots.name, lots.description, lots.url_image, lots.start_price, lots.date_end, lots.step_price, categories.name AS name_category FROM lots JOIN categories ON lots.category = categories.id WHERE lots.id ='" . $id ."'";
$result_lot = mysqli_query($connect, $sql_read_lot);
$open_lot = mysqli_fetch_array($result_lot, MYSQLI_ASSOC);

$sql_read_bet = "SELECT bets.date_create, bets.price, users.name FROM bets JOIN users ON bets.user = users.id WHERE bets.lot = '$id' ORDER BY bets.date_create DESC ";
$result_bet = mysqli_query($connect, $sql_read_bet);
$bet_open_lot = mysqli_fetch_all($result_bet, MYSQLI_ASSOC);

if ($open_lot===NULL) {
  http_response_code(404);
  exit("Страница с id =" . $id . " не найдена.");
}

$sql_read_categories = "SELECT * FROM categories";
$result_categories = mysqli_query($connect, $sql_read_categories);
$categories = mysqli_fetch_all($result_categories, MYSQLI_ASSOC);


$content_page = include_template('content_lot.php', ['open_lot' => $open_lot, 'categories' => $categories, 'bet_open_lot' => $bet_open_lot]);
$page = include_template('layout.php', ['categories' => $categories, 'content_page' => $content_page, 'name_page' => htmlspecialchars($open_lot['name']) , 'user_name' => $user_name, 'is_auth' => $is_auth]);
print($page);

?>

