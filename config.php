<?php 
date_default_timezone_set('Asia/Yekaterinburg');

$is_auth = rand(0, 1);

$user_name = 'Mikhail'; // укажите здесь ваше имя

define('NAME_FOLDER_UPLOADS_FILE', '/uploads/');  // Папка с загруженными файлами
define('FILE_PATH', __DIR__ . NAME_FOLDER_UPLOADS_FILE);	// Относительный путь к папке с загруженными файлами

$connect = mysqli_connect("localhost", "mysql", "mysql", "yeticave");
if ($connect == false) {
    exit("Ошибка подключения: " . mysqli_connect_error());
}

mysqli_set_charset($connect, "utf8");

require_once('helpers.php');