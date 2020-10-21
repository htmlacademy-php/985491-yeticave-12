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

function get_filtered_post_val(string $name): string {
    return htmlspecialchars($_POST[$name]) ?? "";
}

function validation_format_date(string $date): ?string {  	
   	if (date_create_from_format('Y-m-d', $date) === false) {
   		return 'Дата должна быть введена в формате "ГГГГ-ММ_ДД"';
   	}
   	else {   	
   		return NULL;
   	}
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
function validate_file(string $name, string $name_folder_uploads_file): ?string {     
  if (isset($_FILES[$name]) && !empty($_FILES[$name]['name'])) {
    $file_name = $_FILES[$name]['name'];
    $file_path = __DIR__ . $name_folder_uploads_file;
    $file_url = $name_folder_uploads_file . $file_name;     
    
    $type_file = mime_content_type($file_path . $file_name);
    if (!($type_file === 'image/jpeg' || $type_file === 'image/png' || $type_file === 'image/jpg')) { 
      return 'Допустимы только файлы изображений типов jpeg, jpg и png ';  
    }   
    else {
    	return NULL;
    }                          
  }           
  else { 
    return 'Поле не заполнено '; 
  }  
}

