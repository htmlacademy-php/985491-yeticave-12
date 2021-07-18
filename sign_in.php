<?php
require_once('bootstrap.php');
require_once('functions/template.php');
require_once('functions/subsidiary.php');
require_once('functions/validate.php');
require_once('functions/datetime.php');
require_once('functions/get_from_get_or_post.php');

$errors_validate = [];

if (isset($_POST['submit'])) {  //Если есть такое поле в POST, значит форма отправлена
  $errors_validate = validate_sign_in($connection, $_POST);

  //Если были ошибки валидации - возвращаем на страницу добавления нового лота с показом ошибок
  if ($errors_validate) {
    print_page('login.php', ['categories' => $categories, 'errors_validate' => $errors_validate], 'Вход');
  }
  else {
    $sql_read_user_id = "SELECT * FROM users WHERE users.email = ?";
    $email = get_post_val('email');
    $user = db_read_one_line_stmt($connection, $sql_read_user_id, [$email]);

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];

    //Перенаправляем на страницу входа в личный кабинет
    header('Location: index.php');
  }
}
else {  //Если форма не отправлена, показываем страницу создания аккаунта
  print_page('login.php', ['categories' => $categories, 'errors_validate' => $errors_validate], 'Вход');
}
?>

