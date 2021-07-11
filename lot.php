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
        return $back_time[1] . ' минут назад';
    }
    if ($diff < 86400) {
        return $back_time[0] . ' часов назад';
    }

    return date('d.m.Y в H:i', strtotime($date_create));
}

$id = init_open_lot($_GET, $_SESSION);

$sql_read_lot = "SELECT lots.date_create, lots.name, lots.description, lots.url_image, lots.start_price, lots.date_end, lots.step_price, categories.name AS name_category FROM lots JOIN categories ON lots.category_id = categories.id WHERE lots.id ='" . $id ."'";
$open_lot = db_read_one_line($connection, $sql_read_lot);
if ($open_lot === NULL) {
    http_response_code(404);
    exit("Страница с id =" . $id . " не найдена.");
}

$sql_read_bet = "SELECT bets.date_create, bets.price, users.name FROM bets JOIN users ON bets.user_id = users.id WHERE bets.lot_id = '$id' ORDER BY bets.date_create DESC ";
$bet_open_lot = db_read_all($connection, $sql_read_bet);

$current_price = check_price_lot($bet_open_lot, (int)$open_lot['start_price']);

if (isset($_POST['submit_bet'])) {  //Если есть такое поле в POST, значит форма отправлена
    $errors_validate = validate_bets_form($open_lot, $current_price, $_SESSION, $_POST);

//Если были ошибки валидации - возвращаем на страницу добавления новой ставки с показом ошибок
  if (!$errors_validate) {
    //Если ошибок не было - добавляем новую ставку в БД
      $errors_validate['error'] = "no errors";
    $price_bet = $current_price + (int)$open_lot['step_price'];
    if ($_POST['cost'] > $price_bet) {
        $price_bet = (int)get_post_val('cost');
    }
    $date_create = date('Y-m-d H:i:s');

    $sql_insert_bet = 'INSERT INTO bets (date_create, price, user_id, lot_id) VALUES (?, ?, ?, ?)';
    $stmt = mysqli_prepare($connection, $sql_insert_bet);
    mysqli_stmt_bind_param($stmt, 'siii', $date_create, $price_bet, $_SESSION['user_id'], $id);
    if (!mysqli_stmt_execute($stmt)) {
        $error = mysqli_error($connection);
        exit("Ошибка MySQL: " . $error);
    }

    $bet_open_lot = db_read_all($connection, $sql_read_bet);
    $current_price = check_price_lot($bet_open_lot, (int)$open_lot['start_price']);
  }
}

print_page('content_lot.php', ['open_lot' => $open_lot, 'categories' => $categories, 'errors_validate' =>
    $errors_validate ?? FALSE, 'bet_open_lot' => $bet_open_lot, 'current_price' =>
    $current_price], $open_lot['name']);

