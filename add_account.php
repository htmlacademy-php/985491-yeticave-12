<?php
require_once('bootstrap.php');
require_once('user_function.php');
require_once('functions/template.php');
require_once('functions/subsidiary.php');
require_once('functions/validate.php');

$errors_validate = [];

check_sign_in_for_add_account();

if (isset($_POST['submit'])) {  //Если есть такое поле в POST, значит форма отправлена
  $date_create = date('Y-m-d H:i:s');
  $hash_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

  $errors_validate = validate_add_account($connection);

  //Если были ошибки валидации - возвращаем на страницу добавления нового лота с показом ошибок
  if ($errors_validate) {
    print_page('sign-up.php', ['categories' => $categories, 'errors_validate' => $errors_validate], 'Регистрация нового аккаунта');
  }
  else {
    //Если ошибок не было - добавляем нового пользователя в БД
    $sql_insert_new_users = 'INSERT INTO users (date_registered, email, name, password, contact)
    VALUES (?, ?, ?, ?, ?)';
    db_insert_one_line_stmt($connection, $sql_insert_new_users, [$date_create, get_post_val('email'), get_post_val('name'),
        $hash_password, get_post_val('message')]);

    //Перенаправляем на страницу входа в личный кабинет
    header('Location: sign_in.php');
  }
}
else {  //Если форма не отправлена, показываем страницу создания аккаунта
  print_page('sign-up.php', ['categories' => $categories, 'errors_validate' => $errors_validate], 'Регистрация нового аккаунта');
}
?>

