<?php
require_once('config.php');
require_once('user_function.php');

$sql_read_categories = "SELECT * FROM categories";
$result_categories = mysqli_query($connect, $sql_read_categories);
$categories = mysqli_fetch_all($result_categories, MYSQLI_ASSOC);

$rules = [
  'email' => function() use ($connect): ?string {                
      $error = validate_filled('email');
      if ($error) {
        return $error;
      }  

      if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))  {
        return 'Неверный формат адреса электронной почты. Проверьте введенный email';
      }      
      
      $sql_read_email_users = "SELECT users.email FROM users WHERE users.email = ?";          
      $stmt = mysqli_prepare($connect, $sql_read_email_users);
      mysqli_stmt_bind_param($stmt, 's', get_post_val('email'));          
      mysqli_stmt_execute($stmt);
      $result = mysqli_stmt_get_result($stmt);
      $exist_email = mysqli_fetch_all($result, MYSQLI_ASSOC);
      
      if (!$exist_email) {         
        return 'Пользователь с таким email еще не зарегистрирован';
      }

      return NULL;
  },
  'password' => function() use ($connect): ?string {     
      $error = validate_filled('password');
      if ($error) {
        return $error;
      }      
      
      $sql_read_password_users = "SELECT users.password FROM users WHERE users.email = ?";          
      $stmt = mysqli_prepare($connect, $sql_read_password_users);
      mysqli_stmt_bind_param($stmt, 's', get_post_val('email'));          
      mysqli_stmt_execute($stmt);
      $result = mysqli_stmt_get_result($stmt);     
      $exist_email = mysqli_fetch_assoc ($result);

      if (!password_verify(get_post_val('password'), $exist_email['password'])) {
        return 'Вы ввели неверный пароль';
      }

      return NULL;
  }
];

$errors_validate = [];

if (isset($_POST['submit'])) {  //Если есть такое поле в POST, значит форма отправлена      
  
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
    $content_page = include_template('login.php', ['categories' => $categories, 'errors_validate' => $errors_validate]);
    $page = include_template('layout.php', ['categories' => $categories, 'content_page' => $content_page, 'name_page' => 'Вход' , 'user_name' => $user_name, 'is_auth' => $is_auth]);
    print($page);    
  }
  else {   
    $sql_read__user_id = "SELECT * FROM users WHERE users.email = ?";          
    $stmt = mysqli_prepare($connect, $sql_read__user_id);
    mysqli_stmt_bind_param($stmt, 's', get_post_val('email'));          
    mysqli_stmt_execute($stmt);
    $result_user = mysqli_stmt_get_result($stmt);     
    $user = mysqli_fetch_assoc($result_user);    

    if (!mysqli_stmt_execute($stmt)) { 
      $error = mysqli_error($connect); 
      exit("Ошибка MySQL: " . $error);
    }

    session_start();
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    
    //Перенаправляем на страницу входа в личный кабинет
    header('Location: index.php');    
  }
}
else {  //Если форма не отправлена, показываем страницу создания аккаунта
  $content_page = include_template('login.php', ['categories' => $categories, 'errors_validate' => $errors_validate]);
  $page = include_template('layout.php', ['categories' => $categories, 'content_page' => $content_page, 'name_page' => 'Вход' , 'user_name' => $user_name, 'is_auth' => $is_auth]);
  print($page);
}

?>

