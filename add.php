<?php
require_once('bootstrap.php');
require_once('functions/template.php');
require_once('functions/subsidiary.php');
require_once('functions/validate.php');
require_once('functions/datetime.php');
require_once('functions/get_from_get_or_post.php');

$added_lot = [];
$errors_validate = [];

check_sign_in_for_add_lot($_SESSION);

if (isset($_POST['submit'])) {  //Если есть такое поле в POST, значит форма отправлена
  $added_lot['author_id'] = $_SESSION['user_id'];
  $added_lot['date_create'] = date('Y-m-d H:i:s');

  $result_validate_file = download_file($_FILES, NAME_FOLDER_UPLOADS_FILE, FILE_PATH);
  if (stripos($result_validate_file, 'uploads') !== false){
      $added_lot['file_img_lot'] = $result_validate_file;
  }
  else {
      $errors_validate['file_img_lot'] = $result_validate_file;
  }

  $errors_validate = validate_add_lot_form($categories, $_POST);

  //Если были ошибки валидации - возвращаем на страницу добавления нового лота с показом ошибок
  if ($errors_validate) {
    print_page('content_add_lot.php', ['categories' => $categories, 'errors_validate' => $errors_validate], 'Добавление лота');
  }
  else {
    //Если ошибок не было - добавляем ноый лот в БД
    $id_last_insert_lot = add_lot($connection, ['date_create' => $added_lot['date_create'], 'lot-name' => get_post_val('lot-name'), 'message' => get_post_val('message'), 'file_img_lot' => $added_lot['file_img_lot'], 'lot-rate' => get_post_val('lot-rate'), 'lot-date' => get_post_val('lot-date'), 'lot-step' => get_post_val('lot-step'), 'author_id' => $added_lot['author_id'], 'category' => get_post_val('category')]);

    //Перенаправляем на страницу с только что добавленным лотом
    header('Location: lot.php?id=' . $id_last_insert_lot);
  }
}
else {  //Если форма не отправлена, показываем страницу добавления лота
  print_page('content_add_lot.php', ['categories' => $categories, 'errors_validate' => $errors_validate], 'Добавление лота');
}
?>

