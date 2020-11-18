<?php
require_once('config.php');
require_once('user_function.php');

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
      
      if ($exist_email) {         
        return 'Пользователь с таким email уже зарегистрирован';
      }

      return NULL;
  },
  'password' => function(): ?string {         
      return validate_filled('password');               
  },
  'name' => function(): ?string {           
      return validate_filled('name');      
  },
  'message' => function(): ?string {           
      return validate_filled('message');      
  }
];

$errors_validate = [];

if (isset($_SESSION['user_id'])) {
  http_response_code(403);
  exit("Вы уже зарегистрированы.");
}

if (isset($_POST['submit'])) {  //Если есть такое поле в POST, значит форма отправлена      
  $date_create = date('Y-m-d H:i:s');
  $hash_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

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
    $content_page = include_template('sign-up.php', ['categories' => $categories, 'errors_validate' => $errors_validate]);
    $page = include_template('layout.php', ['categories' => $categories, 'content_page' => $content_page, 'name_page' => 'Регистрация нового аккаунта' , 'user_name' => $user_name, 'is_auth' => $is_auth]);
    print($page);    
  }
  else {
    //Если ошибок не было - добавляем нового пользователя в БД
    $sql_insert_new_users = 'INSERT INTO users (date_registered, email, name, password, contact) 
    VALUES (?, ?, ?, ?, ?)';
    
    $stmt = mysqli_prepare($connect, $sql_insert_new_users);
    mysqli_stmt_bind_param($stmt, 'sssss', $date_create, get_post_val('email'), get_post_val('name'), $hash_password, get_post_val('message'));
    
    if (!mysqli_stmt_execute($stmt)) { 
      $error = mysqli_error($connect); 
      exit("Ошибка MySQL: " . $error);
    }    
    
    //Перенаправляем на страницу входа в личный кабинет
    header('Location: sign_in.php');    
  }
}
else {  //Если форма не отправлена, показываем страницу создания аккаунта
  $content_page = include_template('sign-up.php', ['categories' => $categories, 'errors_validate' => $errors_validate]);
  $page = include_template('layout.php', ['categories' => $categories, 'content_page' => $content_page, 'name_page' => 'Регистрация нового аккаунта' , 'user_name' => $user_name, 'is_auth' => $is_auth]);
  print($page);
}

?>

