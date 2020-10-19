<?php 
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

function get_post_val(string $name): string {
    return $_POST[$name] ?? "";
}

function validation_format_date(string $date): bool {
  $format_date = date_create_from_format('Y-m-d', $date);
   return $format_date !== false;
} 

//здесь не указан тип возвращаемого значения т.к. при указании string выдает ошибку, я так понимаю что когда ошибки нет он возвращает NULL
function validate_filled(string $name): ?string { 
    if (empty($_POST[$name])) {
        return 'Поле не заполнено ';
    }
    else{
    	return NULL;
    }
}

//здесь не указан тип возвращаемого значения т.к. при указании string выдает ошибку, я так понимаю что когда ошибки нет он возвращает NULL
function validate_file(string $name): string {     
  if (isset($_FILES[$name]) && !empty($_FILES[$name]['name'])) {
    $file_name = $_FILES[$name]['name'];
    $file_path = __DIR__ . '/uploads/';
    $file_url = '/uploads/' . $file_name;     
    
    $type_file = mime_content_type($file_path . $file_name);
    if (!($type_file === 'image/jpeg' || $type_file === 'image/png' || $type_file === 'image/jpg')) { 
      return 'Допустимы только файлы изображений типов jpeg, jpg и png ';  
    }     
    
    if (move_uploaded_file($_FILES[$name]['tmp_name'], $file_path . $file_name)) {
      return (string)$file_url;      
    }
    else {
      return 'Ошибка при перемещении файла ';
    }                 
  }           
  else { 
    return 'Поле не заполнено '; 
  }  
}

$rules = [
  'lot-name' => function(): ?string {          
      return validate_filled('lot-name');
  },
  'category' => function(array $categories): ?string {
      $error = validate_filled('category');
      if ($error) {
        return $error;
      }                        
      
      $category_exists = 0;      
      foreach ($categories as $item_category) { //проверяется, что введенная категория существует
        if ($item_category['name'] === $_POST['category']) {
          $category_exists = 1;
        }
      }
      if ($_POST['category'] === 'Выберите категорию') {
        return 'Не выбрана категория';
      }
      if ($category_exists === 0) {
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
      if ($_POST['lot-rate'] <= 0) {
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
      
      if (!validation_format_date($_POST['lot-date'])) {
        return 'Дата должна быть введена в формате "ГГГГ-ММ_ДД"';
      }

      $hours_and_minuts_2 = get_dt_range($_POST['lot-date']); //получение массива с часами и минутами разницы времени между введенной датой окончания торгов и текущей датой
      if ($hours_and_minuts_2[0] < 24) {  //В "0" элементе хранятся часы, если меньше 24 часов, значит менее суток
        return 'Дата окончания торгов должна быть позже текущего времени минимум на 24 часа'; 
      }     
      return NULL;       
  }
];