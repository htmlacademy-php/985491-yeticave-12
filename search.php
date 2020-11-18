<?php
require_once('config.php');
require_once('user_function.php');

$rules = [
  'search' => function() : ?string {                
      return validate_filled_GET('search');                
  }
];

$errors_validate = [];
define('DEFAULT_LOTS_ON_PAGE', 9);
$number_lots_on_page = DEFAULT_LOTS_ON_PAGE;  //Вынужден был оставить переменную, т.к. функция mysqli_stmt_bind_param принимает только переменные, Fatal error иначе выдает

if (isset($_GET['find'])) {  //Если есть такое поле в GET, значит форма отправлена      
  
  //Валидация соответствующих полей и сохранение ошибок (при наличии)
  foreach ($_GET as $key => $value) {    
    if (isset($rules[$key])) {           
        $rule = $rules[$key];
        $errors_validate[$key] = $rule();               
    }
  } 
  $errors_validate = array_filter($errors_validate);  //убираем пустые значения в массиве
    
  //Если были ошибки валидации - показываем страницу результатов поиска
  if ($errors_validate) {   
    $content_page = include_template('search_templates.php', ['categories' => $categories, 'errors_validate' => $errors_validate]);
    $page = include_template('layout.php', ['categories' => $categories, 'content_page' => $content_page, 'name_page' => 'Результаты поиска']);
    print($page);    
  }
  else {
    $search_query = trim($_GET['search']);
    
    //Получаем количество лотов которые найдены поиском
    $sql_number_lots_searched = "SELECT lots.id, lots.date_create, lots.name, lots.description FROM lots WHERE (MATCH(lots.name, lots.description) AGAINST(?)) AND (lots.date_end > NOW()) ORDER BY lots.date_create DESC";        
    $stmt = mysqli_prepare($connect, $sql_number_lots_searched);
    mysqli_stmt_bind_param($stmt, 's', $search_query);          
    mysqli_stmt_execute($stmt);
    $lots_searched = mysqli_stmt_get_result($stmt);     
    $number_lots_searched = mysqli_num_rows($lots_searched);    
    
    if (isset($_GET['page'])) {
      $active_page = $_GET['page'];
    }
    else {
      $active_page = 1;
    }

    //Рассчет параметров для выборки лотов
    $offset = ((int)$active_page - 1) * DEFAULT_LOTS_ON_PAGE;
    $number_page = (int)ceil($number_lots_searched / DEFAULT_LOTS_ON_PAGE);          

    //Получение всех данных лотов найденных поиском, но только ограниченной выборки
    $sql_search_lots = "SELECT lots.id, lots.date_create, lots.name, lots.description, lots.url_image, lots.start_price, lots.date_end, lots.step_price, categories.name AS name_category FROM lots JOIN categories ON lots.category = categories.id WHERE (MATCH(lots.name, lots.description) AGAINST(?)) AND (lots.date_end > NOW()) ORDER BY lots.date_create DESC LIMIT ? OFFSET ?";          
    $stmt = mysqli_prepare($connect, $sql_search_lots);
    mysqli_stmt_bind_param($stmt, 'sii', $search_query, $number_lots_on_page, $offset);          
    mysqli_stmt_execute($stmt);
    $result_query_search = mysqli_stmt_get_result($stmt);     
    $results_search = mysqli_fetch_all($result_query_search, MYSQLI_ASSOC);  

    if (!mysqli_stmt_execute($stmt)) { 
      $error = mysqli_error($connect); 
      exit("Ошибка MySQL: " . $error);
    }             
        
    $content_page = include_template('search_templates.php', ['categories' => $categories, 'errors_validate' => $errors_validate, 'results_search' => $results_search, 'number_lots_on_page' => DEFAULT_LOTS_ON_PAGE, 'number_lots_searched' => $number_lots_searched, 'number_page' => $number_page, 'active_page' => $active_page]);
    $page = include_template('layout.php', ['categories' => $categories, 'content_page' => $content_page, 'name_page' => 'Результаты поиска']);
    print($page);
    
  }
}
else {  //Если форма не отправлена, показываем страницу результатов без результатов
  $content_page = include_template('search_templates.php', ['categories' => $categories, 'errors_validate' => $errors_validate, 'results_search' => $results_search, 'number_lots_on_page' => DEFAULT_LOTS_ON_PAGE, 'number_lots_searched' => $number_lots_searched, 'number_page' => $number_page, 'active_page' => $active_page]);
  $page = include_template('layout.php', ['categories' => $categories, 'content_page' => $content_page, 'name_page' => 'Результаты поиска']);
  print($page);
}

?>

