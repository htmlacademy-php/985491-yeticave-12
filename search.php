<?php
require_once('bootstrap.php');
require_once('functions/template.php');
require_once('functions/subsidiary.php');
require_once('functions/validate.php');
require_once('functions/datetime.php');
require_once('functions/get_from_get_or_post.php');

$errors_validate = [];
define('DEFAULT_LOTS_ON_PAGE', 9);
$number_lots_on_page = DEFAULT_LOTS_ON_PAGE;  //Вынужден был оставить переменную, т.к. функция mysqli_stmt_bind_param принимает только переменные, Fatal error иначе выдает
$active_page = set_active_page();

if (isset($_GET['find'])) {  //Если есть такое поле в GET, значит форма отправлена
  $errors_validate = validate_search();

  //Если были ошибки валидации - показываем страницу результатов поиска
  if ($errors_validate) {
    print_page('search_templates.php', ['categories' => $categories, 'errors_validate' => $errors_validate], 'Результаты поиска');
  }
  else {
    //Получаем количество лотов которые найдены поиском
    $search_query = trim($_GET['search']);
    $sql_number_lots_searched = "SELECT lots.id, lots.date_create, lots.name, lots.description FROM lots WHERE (MATCH(lots.name, lots.description) AGAINST(?)) AND (lots.date_end > NOW()) ORDER BY lots.date_create DESC";
    $number_lots_searched = db_num_rows_stmt($connection, $sql_number_lots_searched, [$search_query]);

    $active_page = set_active_page();

    //Расчет параметров для выборки лотов
    $offset = ($active_page - 1) * DEFAULT_LOTS_ON_PAGE;
    $number_page = (int)ceil($number_lots_searched / DEFAULT_LOTS_ON_PAGE);

    //Получение всех данных лотов найденных поиском, но только ограниченной выборки
    $sql_search_lots = "SELECT lots.id, lots.date_create, lots.name, lots.description, lots.url_image, lots.start_price, lots.date_end, lots.step_price, categories.name AS name_category FROM lots JOIN categories ON lots.category_id = categories.id WHERE (MATCH(lots.name, lots.description) AGAINST(?)) AND (lots.date_end > NOW()) ORDER BY lots.date_create DESC LIMIT ? OFFSET ?";
    $results_search = db_read_all_stmt($connection, $sql_search_lots, [$search_query, $number_lots_on_page, $offset]);

    print_page('search_templates.php', ['categories' => $categories, 'errors_validate' => $errors_validate, 'results_search' => $results_search, 'number_lots_on_page' => DEFAULT_LOTS_ON_PAGE, 'number_lots_searched' => $number_lots_searched, 'number_page' => $number_page, 'active_page' => $active_page], 'Результаты поиска');
  }
}
else {  //Если форма не отправлена, показываем страницу результатов без результатов
  print_page('search_templates.php', ['categories' => $categories, 'errors_validate' => $errors_validate, 'results_search' => $results_search, 'number_lots_on_page' => DEFAULT_LOTS_ON_PAGE, 'number_lots_searched' => $number_lots_searched, 'number_page' => $number_page, 'active_page' => $active_page], 'Результаты поиска');
}
?>

