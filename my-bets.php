<?php
require_once('config.php');
require_once('user_function.php');

function get_dt_range_back(string $date_create): string {
  $diff = strtotime("now") - strtotime($date_create);
  $back_time = [floor($diff/3600), floor(($diff % 3600)/60)];

  if ($diff < 3600) {
    return $back_time[1] . get_noun_plural_form($back_time[1], ' минута', ' минуты', ' минут') . ' назад';
  }
  if ($diff < 86400) {
    return $back_time[0] . get_noun_plural_form($back_time[0], ' час', ' часа', ' часов') . ' назад';
  }
  
  return date('d.m.y в H:i', strtotime($date_create));    
}

$user_id = (int)$_SESSION['user_id'];

$sql_lots_with_my_bets = "SELECT lots.id AS lot_id, lots.date_create AS date_create_lot, lots.name, lots.url_image, lots.start_price, lots.date_end, lots.step_price, lots.winner, categories.name AS name_category, bets.user, bets.date_create AS date_create_bet, bets.price AS price_my_bet, users.contact FROM lots JOIN categories ON lots.category = categories.id JOIN bets ON lots.id = bets.lot LEFT JOIN users ON lots.winner = users.id WHERE (bets.user = ?) ORDER BY bets.date_create DESC";          
$stmt = mysqli_prepare($connect, $sql_lots_with_my_bets);
mysqli_stmt_bind_param($stmt, 'i', $user_id);          
mysqli_stmt_execute($stmt);
$result_lots_with_my_bets = mysqli_stmt_get_result($stmt);     
$lots_with_my_bet = mysqli_fetch_all($result_lots_with_my_bets, MYSQLI_ASSOC);  

if (!mysqli_stmt_execute($stmt)) { 
  $error = mysqli_error($connect); 
  exit("Ошибка MySQL: " . $error);
}             

$content_page = include_template('my-bets_templates.php', ['categories' => $categories, 'lots_with_my_bet' => $lots_with_my_bet]);
$page = include_template('layout.php', ['categories' => $categories, 'content_page' => $content_page, 'name_page' => 'Мои ставки']);
print($page);

?>

