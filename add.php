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

function getPostVal(string $name): string {
    return $_POST[$name] ?? "";
}

function validation_format_date(string $date): bool {
   $pattern = "/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/"; // Основной 2013-10-22  
  if ( preg_match($pattern, $date, $razdeli) ) :
if ( checkdate($razdeli[2],$razdeli[3],$razdeli[1]) )
      return true;
    else
      return false;
  else :
    return false;
  endif;
} 

$added_lot = [];
$errors_validate = [];
$required_fields = ['lot-name', 'category', 'message', 'lot-rate', 'lot-step', 'lot-date'];

/*function validate_form_add_lot (array $required_fields): void {  
    foreach ($required_fields as $field) {
      if (empty($_POST[$field])) {
          $errors_validate[$field] = 'Поле не заполнено';
      }
      else {$added_lot[$field] = $_POST[$field];}
    }    
    if (isset($_FILES['file_img_lot'])) {
      $file_name = $_FILES['file_img_lot']['name'];
      $file_path = __DIR__ . '/uploads/';
      $file_url = '/uploads/' . $file_name;
      //$added_lot['file_img_lot'] = 'uploads/';
      move_uploaded_file($_FILES['file_img_lot']['tmp_name'], $file_path . $file_name);
      $url_file = (string)$file_url;
      $added_lot['file_img_lot'] = $url_file;
      //$added_lot['file_img_lot'] = 'uploads/';
      //print($_FILES['file_img_lot']['type']);
    }
    else {      
      $errors_validate['file_img_lot'] = 'Поле не заполнено';
    }

  if ($errors_validate) {
       // показать ошибку валидации
      print('Ошибка валидации ');
      foreach ($errors_validate as $key => $err) {
        print($key . ' => ' . $err . '; ');
      }
      //return false;
  }  
}*/

$sql_read_categories = "SELECT * FROM categories";
$result_categories = mysqli_query($connect, $sql_read_categories);
$categories = mysqli_fetch_all($result_categories, MYSQLI_ASSOC);

$sql_read_id_category = "SELECT id FROM categories WHERE name ='" . $_POST['category'] ."'";
$result_id = mysqli_query($connect, $sql_read_id_category);
$id_select_category = mysqli_fetch_array($result_id, MYSQLI_ASSOC);

if (isset($_POST['lot-name'])) {  //Если есть такое поле в POST, значит форма отправлена
  foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {  
      $errors_validate[$field] = 'Поле не заполнено'; 
    }  
    else {
      $added_lot[$field] = $_POST[$field];
    }    
  }

  $added_lot['category'] = $id_select_category['id'];
  $added_lot['author'] = 1;  
  $added_lot['date_create'] = date('Y-m-d H:i:s');

  if (isset($_FILES['file_img_lot'])) {
    $file_name = $_FILES['file_img_lot']['name'];
    $file_path = __DIR__ . '/uploads/';
    $file_url = '/uploads/' . $file_name;    
    move_uploaded_file($_FILES['file_img_lot']['tmp_name'], $file_path . $file_name);
    $url_file = (string)$file_url;
    $added_lot['file_img_lot'] = $url_file;
  }           
  else { 
    $errors_validate['file_img_lot'] = 'Поле не заполнено'; 
  }

  //Валидация соответствующих полей и сохранение ошибок (при наличии)
  if ($_POST['category'] === 'Выберите категорию') {
    $errors_validate['category'] = 'Не выбрана категория';
  }

  if ($_POST['lot-rate'] <= 0) {
    $errors_validate['lot-rate'] = 'Начальная цена должна быть больше нуля';
  }

  if (!ctype_digit ($_POST['lot-step'])) {
    $errors_validate['lot-step_format'] = 'Шаг ставки должен быть целым числом больше нуля';
  }
    
  if ($_POST['lot-step'] <= 0) {
    $errors_validate['lot-step_format'] = 'Шаг ставки должен быть целым числом больше нуля';
  }    

  if (!validation_format_date($_POST['lot-date'])) {
    $errors_validate['lot-date_format'] = 'Дата должна быть введена в формате "ГГГГ-ММ_ДД"';
  }

  $hours_and_minuts_2 = get_dt_range($_POST['lot-date']); //получение массива с часами и минутами разницы времени между введенной датой окончания торгов и текущей датой
  if ($hours_and_minuts_2[0] < 24) {  //В "0" элементе хранятся часы, если меньше 24 часов, значит менее суток
    $errors_validate['lot-date_earlier_current_time'] = 'Дата окончания торгов должна быть позже текущего времени минимум на 24 часа'; 
  }

  $type_file = mime_content_type($file_path . $file_name);
  if (!($type_file === 'image/jpeg' || $type_file === 'image/png')) { 
    $errors_validate['file_img_lot_mime'] = 'Допустимы только файлы изображений типов jpeg и png';  
  } 
   
  //Если были ошибки валидации - возвращаем на страницу добавления нового лота с показом ошибок
  if ($errors_validate) {   
    $content_page = include_template('content_add_lot.php', $data = ['categories' => $categories, 'errors_validate' => $errors_validate]);
    $page = include_template('layout.php', $data = ['categories' => $categories, 'content_page' => $content_page, 'name_page' => 'Добавление лота' , 'user_name' => $user_name, 'is_auth' => $is_auth]);
    print($page);    
  }
  else {
    //Если ошибок не было - добавляем ноый лот в БД
    $sql_insert_lot = "INSERT INTO lots (date_create, name, description, url_image, start_price, date_end, step_price, author, category) VALUES ('" . $added_lot['date_create'] . "', '" . $added_lot['lot-name'] . "','" . $added_lot['message'] . "','" . $added_lot['file_img_lot'] . "',' " . $added_lot['lot-rate'] . "', '" . $added_lot['lot-date'] . "', '" . $added_lot['lot-step'] . "', '" . $added_lot['author'] . "', '" . $added_lot['category'] . "')";
    $result_insert_lot = mysqli_query($connect, $sql_insert_lot);
    
    if ($result_insert_lot) { //Получаем id последней добавленной записи (только что добавленного лота)
      $id_last_insert_lot = mysqli_insert_id($connect);
    }
    else{
      $error = mysqli_error($connect); 
     print("Ошибка MySQL: " . $error);
    }

    //Получаем из БД данные по только что добавленному лоту
    $sql_read_lot = "SELECT lots.date_create, lots.name, lots.description, lots.url_image, lots.start_price, lots.date_end, lots.step_price, categories.name AS name_category FROM lots JOIN categories ON lots.category = categories.id WHERE lots.id ='" . $id_last_insert_lot ."'";
    $result_lot = mysqli_query($connect, $sql_read_lot);
    $open_lot = mysqli_fetch_array($result_lot, MYSQLI_ASSOC);
      
    if ($open_lot===NULL) {
      http_response_code(404);
      exit("Страница с id =" . $id . " не найдена.");
    }

    //Показываем страницу с только что добавленным лотом
    $content_page = include_template('content_lot.php', $data = ['open_lot' => $open_lot, 'categories' => $categories, 'bet_open_lot' => $bet_open_lot]);
    $page = include_template('layout.php', $data = ['categories' => $categories, 'content_page' => $content_page, 'name_page' => htmlspecialchars($open_lot['name']) , 'user_name' => $user_name, 'is_auth' => $is_auth]);
    print($page);    
  }
}
else {  //Если форма не отправлена, показываем страницу добавления лота
  $content_page = include_template('content_add_lot.php', $data = ['categories' => $categories, 'errors_validate' => $errors_validate]);
  $page = include_template('layout.php', $data = ['categories' => $categories, 'content_page' => $content_page, 'name_page' => 'Добавление лота' , 'user_name' => $user_name, 'is_auth' => $is_auth]);
  print($page);
}

?>

