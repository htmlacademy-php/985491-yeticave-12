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
  $format_date = date_create_from_format('Y-m-j', $date);
   if ($format_date === false){
      return false;
   }
   else {
      return true;
   }  
} 

$added_lot = [];
$errors_validate = [];
$required_fields = ['lot-name', 'category', 'message', 'lot-rate', 'lot-step', 'lot-date'];

$sql_read_categories = "SELECT * FROM categories";
$result_categories = mysqli_query($connect, $sql_read_categories);
$categories = mysqli_fetch_all($result_categories, MYSQLI_ASSOC);


$name_category_safe = mysqli_real_escape_string($connect, $_POST['category']);
$sql_read_id_category = "SELECT id FROM categories WHERE name ='" . $name_category_safe ."'";
$result_id = mysqli_query($connect, $sql_read_id_category);
$id_select_category = mysqli_fetch_array($result_id, MYSQLI_ASSOC);

if (isset($_POST['submit'])) {  //Если есть такое поле в POST, значит форма отправлена
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

  if (isset($_FILES['file_img_lot']) && !empty($_FILES['file_img_lot']['name'])) {
    $file_name = $_FILES['file_img_lot']['name'];
    $file_path = __DIR__ . '/uploads/';
    $file_url = '/uploads/' . $file_name;    
    
    if (file_exists($file_path . $file_name)) {   
      $i = 1;
      while (file_exists($file_path . $temp_file_name))   {
        $temp_file_name = '(' . $i . ')' . $file_name;
        $i++;
      }      
      $file_name = $temp_file_name;
      $file_url = '/uploads/' . $file_name;       
    }
    
    if (move_uploaded_file($_FILES['file_img_lot']['tmp_name'], $file_path . $file_name)) {
      $url_file = (string)$file_url;
      $added_lot['file_img_lot'] = $url_file;
    }
    else {
      $errors_validate['file_img_lot_error_move'] = 'Ошибка при перемещении файла ';
    }    
  }           
  else { 
    $errors_validate['file_img_lot'] = 'Поле не заполнено '; 
  }

  //Валидация соответствующих полей и сохранение ошибок (при наличии)
  $category_exists = 0;
  foreach ($categories as $item_category) { //проверяется, что введенная категория существует
    if ($item_category['name'] === $_POST['category']) {
      $category_exists = 1;
    }
  }
  if ($_POST['category'] === 'Выберите категорию') {
    $errors_validate['category'] = 'Не выбрана категория';
  }
  if ($category_exists === 0 && $_POST['category'] != 'Выберите категорию') {
    $errors_validate['category'] = 'Выбрана несуществующая категория';
  }

  if ($_POST['lot-rate'] <= 0 && !$errors_validate['lot-rate']) {
    $errors_validate['lot-rate'] = 'Начальная цена должна быть числом больше нуля';
  }

  if (!ctype_digit ($_POST['lot-step']) && !$errors_validate['lot-step']) {
    $errors_validate['lot-step_format'] = 'Шаг ставки должен быть целым числом больше нуля';
  }
    
  if ($_POST['lot-step'] <= 0 && !$errors_validate['lot-step']) {
    $errors_validate['lot-step_format'] = 'Шаг ставки должен быть целым числом больше нуля';
  }    

  if (!validation_format_date($_POST['lot-date']) && !$errors_validate['lot-date']) {
    $errors_validate['lot-date_format'] = 'Дата должна быть введена в формате "ГГГГ-ММ_ДД"';
  }

  $hours_and_minuts_2 = get_dt_range($_POST['lot-date']); //получение массива с часами и минутами разницы времени между введенной датой окончания торгов и текущей датой
  if ($hours_and_minuts_2[0] < 24 && !$errors_validate['lot-date']) {  //В "0" элементе хранятся часы, если меньше 24 часов, значит менее суток
    $errors_validate['lot-date_earlier_current_time'] = 'Дата окончания торгов должна быть позже текущего времени минимум на 24 часа'; 
  }

  if (!$errors_validate['file_img_lot_error_move'] && !$errors_validate['file_img_lot']) {    
    $type_file = mime_content_type($file_path . $file_name);
    if (!($type_file === 'image/jpeg' || $type_file === 'image/png' || $type_file === 'image/jpg')) { 
      $errors_validate['file_img_lot_mime'] = 'Допустимы только файлы изображений типов jpeg, jpg и png ';  
    } 
  }
   
  //Если были ошибки валидации - возвращаем на страницу добавления нового лота с показом ошибок
  if ($errors_validate) {   
    $content_page = include_template('content_add_lot.php', $data = ['categories' => $categories, 'errors_validate' => $errors_validate]);
    $page = include_template('layout.php', $data = ['categories' => $categories, 'content_page' => $content_page, 'name_page' => 'Добавление лота' , 'user_name' => $user_name, 'is_auth' => $is_auth]);
    print($page);    
  }
  else {
    //Если ошибок не было - добавляем ноый лот в БД
    $sql_insert_lot = 'INSERT INTO lots (date_create, name, description, url_image, start_price, date_end, step_price, author, category) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)';
    
    $stmt = mysqli_prepare($connect, $sql_insert_lot);
    mysqli_stmt_bind_param($stmt, 'sssssssii', $added_lot['date_create'], $added_lot['lot-name'], $added_lot['message'], $added_lot['file_img_lot'], $added_lot['lot-rate'], $added_lot['lot-date'], $added_lot['lot-step'], $added_lot['author'], $added_lot['category']);
    mysqli_stmt_execute($stmt);
    $id_last_insert_lot = mysqli_insert_id($connect);

    //Получаем из БД данные по только что добавленному лоту
    $sql_read_lot = "SELECT lots.date_create, lots.name, lots.description, lots.url_image, lots.start_price, lots.date_end, lots.step_price, categories.name AS name_category FROM lots JOIN categories ON lots.category = categories.id WHERE lots.id ='" . $id_last_insert_lot ."'";
    $result_lot = mysqli_query($connect, $sql_read_lot);
    $open_lot = mysqli_fetch_array($result_lot, MYSQLI_ASSOC);
      
    if ($open_lot===NULL) {
      http_response_code(404);
      exit("Страница с id =" . $id . " не найдена.");
    }

    //Перенаправляем на страницу с только что добавленным лотом    
    header('Location: lot.php?id=' . $id_last_insert_lot);    
  }
}
else {  //Если форма не отправлена, показываем страницу добавления лота
  $content_page = include_template('content_add_lot.php', $data = ['categories' => $categories, 'errors_validate' => $errors_validate]);
  $page = include_template('layout.php', $data = ['categories' => $categories, 'content_page' => $content_page, 'name_page' => 'Добавление лота' , 'user_name' => $user_name, 'is_auth' => $is_auth]);
  print($page);
}

?>

