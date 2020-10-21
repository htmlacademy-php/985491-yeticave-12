<?php 
date_default_timezone_set('Asia/Yekaterinburg');

$is_auth = rand(0, 1);

$user_name = 'Mikhail'; // укажите здесь ваше имя

$name_folder_uploads_file = '/uploads/';  // Папка с загруженными файлами
$file_path = __DIR__ . $name_folder_uploads_file;  // Относительный путь к папке с загруженными файлами

$connect = mysqli_connect("localhost", "mysql", "mysql", "yeticave");
if ($connect == false) {
    exit("Ошибка подключения: " . mysqli_connect_error());
}

mysqli_set_charset($connect, "utf8");

require_once('helpers.php');