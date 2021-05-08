<?php
require_once('bootstrap.php');
require_once('functions/template.php');
require_once('functions/subsidiary.php');
require_once('functions/validate.php');
require_once('functions/datetime.php');
require_once('functions/get_from_get_or_post.php');

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

$sql_lots_with_my_bets = "SELECT lots.id AS lot_id, lots.date_create AS date_create_lot, lots.name, lots.url_image, lots.start_price, lots.date_end, lots.step_price, lots.winner_id, categories.name AS name_category, bets.user_id, bets.date_create AS date_create_bet, bets.price AS price_my_bet, users.contact FROM lots JOIN categories ON lots.category_id = categories.id JOIN bets ON lots.id = bets.lot_id LEFT JOIN users ON lots.winner_id = users.id WHERE (bets.user_id = ?) ORDER BY bets.date_create DESC";
$lots_with_my_bet = db_read_all_stmt($connection, $sql_lots_with_my_bets, [$user_id]);

print_page('my-bets_templates.php', ['categories' => $categories, 'lots_with_my_bet' => $lots_with_my_bet], 'Мои ставки');
?>
