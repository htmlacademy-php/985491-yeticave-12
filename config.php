<?php 
date_default_timezone_set('Asia/Yekaterinburg');

session_start();

define('NAME_FOLDER_UPLOADS_FILE', '/uploads/');  // Папка с загруженными файлами
define('FILE_PATH', __DIR__ . NAME_FOLDER_UPLOADS_FILE);	// Относительный путь к папке с загруженными файлами

$connect = mysqli_connect("localhost", "mysql", "mysql", "yeticave");
if ($connect == false) {
    exit("Ошибка подключения: " . mysqli_connect_error());
}

mysqli_set_charset($connect, "utf8");

require_once('helpers.php');

$sql_read_categories = "SELECT * FROM categories";
$result_categories = mysqli_query($connect, $sql_read_categories);
$categories = mysqli_fetch_all($result_categories, MYSQLI_ASSOC);

require_once('check_win.php');