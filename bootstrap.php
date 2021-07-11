<?php
session_start();
$config = require 'config.php';
require 'functions/db.php';
require 'functions/subsidiary.php';
date_default_timezone_set($config['time']['timezone']);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

const NAME_FOLDER_UPLOADS_FILE = '/uploads/';  // Папка с загруженными файлами
const FILE_PATH = __DIR__ . NAME_FOLDER_UPLOADS_FILE;    // Относительный путь к папке с загруженными файлами

$connection = db_connect($config['db']);

require_once('helpers.php');

$categories = get_all_category($connection);


