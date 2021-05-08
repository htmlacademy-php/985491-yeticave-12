<?php
session_start();
$config = require 'config.php';
require 'functions/db.php';
date_default_timezone_set('Asia/Yekaterinburg');

const NAME_FOLDER_UPLOADS_FILE = '/uploads/';  // Папка с загруженными файлами
const FILE_PATH = __DIR__ . NAME_FOLDER_UPLOADS_FILE;    // Относительный путь к папке с загруженными файлами

$connection = db_connect($config['db']);

require_once('helpers.php');

$sql_read_categories = "SELECT * FROM categories";
$categories = db_read_all($connection, $sql_read_categories);

require_once('check_win.php');

