<?php
require_once('config.php');
require_once('user_function.php');

$rules = [
  'lot-name' => function(): ?string {          
      return validate_filled('lot-name');
  },
  'category' => function() use ($categories): ?string {         
      if (empty($_POST['category'])) {
        return 'Не выбрана категория';
      }   
      
      $category_exists = false;      
      foreach ($categories as $item_category) { //проверяется, что введенная категория существует
        if ($item_category['id'] === $_POST['category']) {
          $category_exists = true;
        }
      }
      if (!$category_exists) {
        return 'Выбрана несуществующая категория';
      }  
      return NULL;            
  },
  'message' => function(): ?string {           
      return validate_filled('message');      
  },
  'lot-rate' => function(): ?string {         
      $error = validate_filled('lot-rate');
      if ($error) {
        return $error;
      }         
      if ($_POST['lot-rate'] <= 0 || !is_numeric($_POST['lot-rate']) ) {
        return 'Начальная цена должна быть числом больше нуля';
      }
      return NULL;
  },
  'lot-step' => function(): ?string {
      $error = validate_filled('lot-step');
      if ($error) {
        return $error;
      }           

      if (!ctype_digit ($_POST['lot-step']) || $_POST['lot-step'] <= 0) {
        return 'Шаг ставки должен быть целым числом больше нуля';
      }   
      return NULL;   
  },
  'lot-date' => function(): ?string {
      $error = validate_filled('lot-date');
      if ($error) {
        return $error;
      }                 
      
      if (validation_format_date($_POST['lot-date'])) {
        return 'Дата должна быть введена в формате "ГГГГ-ММ-ДД"';
      }
      
      if (strtotime($_POST['lot-date']) < (time() + 86400)) {  
        return 'Дата окончания торгов должна быть позже текущего времени минимум на 24 часа'; 
      }     
      return NULL;       
  }
];

$added_lot = [];
$errors_validate = [];

if (!isset($_SESSION['user_id'])) {
  http_response_code(403);
  exit("Для добавления лота необходимо зарегистрироваться на сайте.");
}

if (isset($_POST['submit'])) {  //Если есть такое поле в POST, значит форма отправлена    
  $added_lot['author'] = 1;  
  $added_lot['date_create'] = date('Y-m-d H:i:s');

  //Валидация файла
  $errors_validate['file_img_lot'] = validate_file('file_img_lot', '/uploads/');  
  if ($errors_validate['file_img_lot'] === NULL) {
    if (move_uploaded_file($_FILES['file_img_lot']['tmp_name'], FILE_PATH . $_FILES['file_img_lot']['name'])) {
      $added_lot['file_img_lot'] = NAME_FOLDER_UPLOADS_FILE . $_FILES['file_img_lot']['name'];
    }
    else {
      $errors_validate['file_img_lot'] = 'Ошибка при перемещении файла ';
    }
  }
 
  //Валидация соответствующих полей и сохранение ошибок (при наличии)
  foreach ($_POST as $key => $value) {    
    if (isset($rules[$key])) {           
        $rule = $rules[$key];
        $errors_validate[$key] = $rule();               
    }
  } 
  $errors_validate = array_filter($errors_validate);  //убираем пустые значения в массиве
    
  //Если были ошибки валидации - возвращаем на страницу добавления нового лота с показом ошибок
  if ($errors_validate) {   
    $content_page = include_template('content_add_lot.php', ['categories' => $categories, 'errors_validate' => $errors_validate]);
    $page = include_template('layout.php', ['categories' => $categories, 'content_page' => $content_page, 'name_page' => 'Добавление лота' , 'user_name' => $user_name, 'is_auth' => $is_auth]);
    print($page);    
  }
  else {
    //Если ошибок не было - добавляем ноый лот в БД
    $sql_insert_lot = 'INSERT INTO lots (date_create, name, description, url_image, start_price, date_end, step_price, author, category) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)';
    
    $stmt = mysqli_prepare($connect, $sql_insert_lot);
    mysqli_stmt_bind_param($stmt, 'sssssssii', $added_lot['date_create'], get_post_val('lot-name'), get_post_val('message'), $added_lot['file_img_lot'], get_post_val('lot-rate'), get_post_val('lot-date'), get_post_val('lot-step'), $added_lot['author'], get_post_val('category'));
    
    if (!mysqli_stmt_execute($stmt)) { 
      $error = mysqli_error($connect); 
      exit("Ошибка MySQL: " . $error);
    }

    $id_last_insert_lot = mysqli_insert_id($connect);
    if ($id_last_insert_lot === 0) {
      exit('Ошибка получения id последней добавленной записи');
    }

    //Получаем из БД данные по только что добавленному лоту
    $sql_read_lot = "SELECT lots.date_create, lots.name, lots.description, lots.url_image, lots.start_price, lots.date_end, lots.step_price, categories.name AS name_category FROM lots JOIN categories ON lots.category = categories.id WHERE lots.id ='" . $id_last_insert_lot ."'";
    $result_lot = mysqli_query($connect, $sql_read_lot);
    $open_lot = mysqli_fetch_array($result_lot, MYSQLI_ASSOC);
      
    if ($open_lot===NULL) {
      http_response_code(404);
      exit("Страница с id =" . $id_last_insert_lot . " не найдена.");
    }

    //Перенаправляем на страницу с только что добавленным лотом    
    header('Location: lot.php?id=' . $id_last_insert_lot);    
  }
}
else {  //Если форма не отправлена, показываем страницу добавления лота
  $content_page = include_template('content_add_lot.php', ['categories' => $categories, 'errors_validate' => $errors_validate]);
  $page = include_template('layout.php', ['categories' => $categories, 'content_page' => $content_page, 'name_page' => 'Добавление лота' , 'user_name' => $user_name, 'is_auth' => $is_auth]);
  print($page);
}

?>

