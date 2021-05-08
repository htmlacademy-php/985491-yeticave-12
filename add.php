<?php
require_once('bootstrap.php');
require_once('user_function.php');
require_once('functions/template.php');
require_once('functions/subsidiary.php');
require_once('functions/validate.php');

$added_lot = [];
$errors_validate = [];

check_sign_in_for_add_lot();

if (isset($_POST['submit'])) {  //Если есть такое поле в POST, значит форма отправлена
  $added_lot['author_id'] = $_SESSION['user_id'];
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
  $errors_validate = validate_add_lot_form($categories);

  //Если были ошибки валидации - возвращаем на страницу добавления нового лота с показом ошибок
  if ($errors_validate) {
    print_page('content_add_lot.php', ['categories' => $categories, 'errors_validate' => $errors_validate], 'Добавление лота');
  }
  else {
    //Если ошибок не было - добавляем ноый лот в БД
    $sql_insert_lot = 'INSERT INTO lots (date_create, name, description, url_image, start_price, date_end, step_price, author_id, category_id)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)';
    db_insert_one_line_stmt($connection, $sql_insert_lot, [$added_lot['date_create'], get_post_val('lot-name'), get_post_val('message'), $added_lot['file_img_lot'], get_post_val('lot-rate'), get_post_val('lot-date'), get_post_val('lot-step'), $added_lot['author_id'], get_post_val('category')]);

    $id_last_insert_lot = id_last_inserted_line($connection);

    //Получаем из БД данные по только что добавленному лоту
    $sql_read_lot = "SELECT lots.date_create, lots.name, lots.description, lots.url_image, lots.start_price, lots.date_end, lots.step_price, categories.name AS name_category FROM lots JOIN categories ON lots.category_id = categories.id WHERE lots.id ='" . $id_last_insert_lot ."'";
    $open_lot = db_read_one_line($connection, $sql_read_lot);

    if ($open_lot===NULL) {
      http_response_code(404);
      exit("Страница с id =" . $id_last_insert_lot . " не найдена.");
    }

    //Перенаправляем на страницу с только что добавленным лотом
    header('Location: lot.php?id=' . $id_last_insert_lot);
  }
}
else {  //Если форма не отправлена, показываем страницу добавления лота
  print_page('content_add_lot.php', ['categories' => $categories, 'errors_validate' => $errors_validate], 'Добавление лота');
}
?>

