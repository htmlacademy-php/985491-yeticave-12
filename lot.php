<?php
require_once('config.php');
require_once('user_function.php');

if (isset($_GET['id'])) {
  $id = (int) $_GET['id'];    

  if (isset($_SESSION['user_id'])) {
    $_SESSION['lot_id'] = $id;
  }
}
else {
  if (isset($_SESSION['lot_id'])) {
    $id = (int)$_SESSION['lot_id'];
  } 
  else {
    http_response_code(404);
    exit("Ошибка подключения: не указан id");
  }  
}

$sql_read_lot = "SELECT lots.date_create, lots.name, lots.description, lots.url_image, lots.start_price, lots.date_end, lots.step_price, categories.name AS name_category FROM lots JOIN categories ON lots.category = categories.id WHERE lots.id ='" . $id ."'";
$result_lot = mysqli_query($connect, $sql_read_lot);
$open_lot = mysqli_fetch_array($result_lot, MYSQLI_ASSOC);

$sql_read_bet = "SELECT bets.date_create, bets.price, users.name FROM bets JOIN users ON bets.user = users.id WHERE bets.lot = '$id' ORDER BY bets.date_create DESC ";
$result_bet = mysqli_query($connect, $sql_read_bet);
$bet_open_lot = mysqli_fetch_all($result_bet, MYSQLI_ASSOC);

if ($open_lot === NULL) {
  http_response_code(404);
  exit("Страница с id =" . $id . " не найдена.");
}

  if (isset($bet_open_lot[0]['price'])) {
    $current_price = (int)$bet_open_lot[0]['price'];
  }
  else {
    $current_price = (int)$open_lot['start_price'];
  }

$rules = [
  'cost' => function() use ($open_lot, $bet_open_lot): ?string {          
      $error = validate_filled('cost');
      if ($error) {
        return $error;
      }         
      if (!isset($_SESSION['user_id'])){
        return 'Необходимо зарегистрироваться';
      }
      if ($_POST['cost'] <= 0 || !is_numeric($_POST['cost'])) {
        return 'Начальная цена должна быть целым числом больше нуля';
      }
      
      if (isset($bet_open_lot[0]['price'])) {
        $current_price = (int)$bet_open_lot[0]['price'];
      }
      else {
        $current_price = (int)$open_lot['start_price'];
      }
      $min_bet = $current_price + (int)$open_lot['step_price'];      
      if ((int)$_POST['cost'] < $min_bet) {
        return 'Мин.ставка д.б. не менее ' . $min_bet .  ' ₽';
      }

      return NULL;
  }
];

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

if (isset($_POST['submit_bet'])) {  //Если есть такое поле в POST, значит форма отправлена    

  //Валидация соответствующих полей и сохранение ошибок (при наличии)
  foreach ($_POST as $key => $value) {    
    if (isset($rules[$key])) {           
        $rule = $rules[$key];
        $errors_validate[$key] = $rule();               
    }
  } 
  $errors_validate = array_filter($errors_validate);  //убираем пустые значения в массиве  

  //Если были ошибки валидации - возвращаем на страницу добавления нового лота с показом ошибок
  if (!$errors_validate) {   
    //Если ошибок не было - добавляем ноый лот в БД    
    $price_bet = $current_price + (int)$open_lot['step_price'];
    
    if ($_POST['cost'] > $price_bet) {
        $price_bet = (int)get_post_val('cost');
    }
    $date_create = date('Y-m-d H:i:s');

    $sql_insert_bet = 'INSERT INTO bets (date_create, price, user, lot) VALUES (?, ?, ?, ?)';    
    $stmt = mysqli_prepare($connect, $sql_insert_bet);
    mysqli_stmt_bind_param($stmt, 'siii', $date_create, $price_bet, $_SESSION['user_id'], $id);
    
    if (!mysqli_stmt_execute($stmt)) { 
      $error = mysqli_error($connect); 
      exit("Ошибка MySQL: " . $error);
    }

    $sql_read_bet = "SELECT bets.date_create, bets.price, users.name FROM bets JOIN users ON bets.user = users.id WHERE bets.lot = '$id' ORDER BY bets.date_create DESC ";
    $result_bet = mysqli_query($connect, $sql_read_bet);
    $bet_open_lot = mysqli_fetch_all($result_bet, MYSQLI_ASSOC);
    
    if (isset($bet_open_lot[0]['price'])) {
      $current_price = (int)$bet_open_lot[0]['price'];
    }
    else {
      $current_price = (int)$open_lot['start_price'];
    }
  }
}

$content_page = include_template('content_lot.php', ['open_lot' => $open_lot, 'categories' => $categories, 'errors_validate' => $errors_validate, 'bet_open_lot' => $bet_open_lot, 'current_price' => $current_price]);
$page = include_template('layout.php', ['categories' => $categories, 'content_page' => $content_page, 'name_page' => $open_lot['name']]);
print($page);

?>

