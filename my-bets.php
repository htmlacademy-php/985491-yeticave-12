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
$lots_with_my_bet = get_all_lots_with_my_bets($connection, $user_id);

print_page('my-bets_templates.php', ['categories' => $categories, 'lots_with_my_bet' => $lots_with_my_bet], 'Мои ставки');
?>
