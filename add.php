<?php
require_once('config.php');

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
/*$added_lot = [];
$required_fields = ['lot-name', 'category', 'message', 'file_img_lot', 'lot-rate', 'lot-step', 'lot-date'];
*/
function validate_form_add_lot (array $required_fields): void {
  
  foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        $errors[$field] = 'Поле не заполнено';
    }
    else {$added_lot[$field] = $_POST[$field];}
    }

  if (count($errors)) {
       // показать ошибку валидации
  }  
}

/*if (isset($_GET['id'])) {
  $id = (int) $_GET['id'];  
}
else {
  http_response_code(404);
  exit("Ошибка подключения: не указан id");
}*/
$sql_read_categories = "SELECT * FROM categories";
$result_categories = mysqli_query($connect, $sql_read_categories);
$categories = mysqli_fetch_all($result_categories, MYSQLI_ASSOC);


/*$sql_read_id_category = "SELECT id FROM categories WHERE name ='" . $added_lot['category'] ."'";
$result_id = mysqli_query($connect, $sql_read_id_category);
$id_select_category = mysqli_fetch_array($result_id, MYSQLI_ASSOC);*/

$added_lot['date_create'] = date('Y-m-d H:i:s', $timestamp = strtotime("now"));
$added_lot['author'] = 1;
$added_lot['category'] = $id_select_category['id'];
$added_lot['file_img_lot'] = 'img/lot-2.jpg';
/*$added_lot['lot-rate'] = '';
$added_lot['lot-rate'] = 1000;
$added_lot['lot-rate'] = (int)$added_lot['lot-rate'];*/

$sql_read_lot = "SELECT lots.date_create, lots.name, lots.description, lots.url_image, lots.start_price, lots.date_end, lots.step_price, categories.name AS name_category FROM lots JOIN categories ON lots.category = categories.id WHERE lots.id ='" . $id ."'";
$result_lot = mysqli_query($connect, $sql_read_lot);

$open_lot = mysqli_fetch_array($result_lot, MYSQLI_ASSOC);

/*if ($open_lot===NULL) {
  http_response_code(404);
  exit("Страница с id =" . $id . " не найдена.");
}*/

/*print($_POST['lot-name']);
print($_POST['category']);
print($_POST['message']);*/

$lot_rate = trim($_POST['lot-rate']);
$lot_rate = (int) $lot_rate;
settype($lot_rate, 'integer');
$lot_step = $_POST['lot-step'];



if (isset($_POST['lot-name'])) {
  //validate_form_add_lot($required_fields);
  print($_POST['lot-name']);
  $sql_insert_lot = "INSERT INTO lots (date_create, name, description, url_image, start_price, date_end, step_price, author, category) 
  VALUES ('" . $added_lot['date_create'] . "', '" . $added_lot['lot-name'] . "','" . $added_lot['message'] . "','" . $added_lot['file_img_lot'] . "','
  " . $lot_rate . "', '" . $added_lot['lot-date'] . "', '" . $added_lot['lot-step'] . "', '" . $added_lot['author'] . "', 
  '" . $added_lot['category'] . "')";
  $result_insert_lot = mysqli_query($connect, $sql_insert_lot);

  /*$sql_insert_lot = 'INSERT INTO lots (date_create, name, description, url_image, start_price, date_end, step_price, author, category) 
  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)';*/

  //$added_lot['lot-rate'] = (int)$added_lot['lot-rate'];
  /*$stmt = mysqli_prepare($connect, $sql_insert_lot);
  mysqli_stmt_bind_param($stmt, 'ssssisiii', $added_lot['date_create'], $added_lot['lot-name'], $added_lot['message'], $added_lot['file_img_lot'], $lot_rate, $added_lot['lot-date'], $lot_step, $added_lot['author'], $added_lot['category']);
  mysqli_stmt_execute($stmt);*/
  print($_POST['lot-rate']);

  if (!$result_insert_lot) { 
    $error = mysqli_error($connect); 
    print("Ошибка MySQL: " . $error);
  }
}
else {
  $content_page = include_template('content_add_lot.php', $data = ['open_lot' => $open_lot, 'categories' => $categories, 'bet_open_lot' => $bet_open_lot]);
  $page = include_template('layout.php', $data = ['categories' => $categories, 'content_page' => $content_page, 'name_page' => 'Добавление лота' , 'user_name' => $user_name, 'is_auth' => $is_auth]);
  print($page);
}


?>

