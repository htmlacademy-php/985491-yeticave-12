<?php
/**
 * Устанвавливает соединение с БД
 * Аварийно завершает работу сценария при неудачном подключении
 * Устанавливает кодировку UTF-8
 *
 * @param array $dbConfig данные для подключения
 *
 * @return mysqli Ресурс соединения
 */
function db_connect(array $dbConfig) : mysqli
{
    $connection = mysqli_connect(
        $dbConfig['host'],
        $dbConfig['user'],
        $dbConfig['password'],
        $dbConfig['database']);
    if ($connection === false) {
        exit("Ошибка подключения: " . mysqli_connect_error());
    }

    mysqli_set_charset($connection, "utf8");

    return $connection;
}

/**
 * Читает из БД всё, согласно запросу
 *
 *
 * @param mysqli $connection Ресурс соединения
 * @param string $query Строка запроса на чтение
 *
 * @return array Ассоциативный массив результата запроса
 */
function db_read_all(mysqli $connection, string $query) : ?array
{
    $result_query = mysqli_query($connection, $query);
    return mysqli_fetch_all($result_query, MYSQLI_ASSOC);
}

/**
 * Читает из БД одну строку, согласно запросу
 *
 *
 * @param mysqli $connection Ресурс соединения
 * @param string $query Строка запроса на чтение
 *
 * @return array Ассоциативный массив результата запроса
 */
function db_read_one_line(mysqli $connection, string $query) : ?array
{
    $result_query = mysqli_query($connection, $query);
    return mysqli_fetch_array($result_query, MYSQLI_ASSOC);
}

/**
 * Читает из БД одну строку, согласно запросу, используя подготовленные выражения
 *
 *
 * @param mysqli $connection Ресурс соединения
 * @param string $query Строка запроса на чтение
 * @param array $data Массив с данными для вставки на место плэйсхолдеров
 *
 * @return array Ассоциативный массив результата запроса
 */
function db_read_one_line_stmt(mysqli $connection, string $query, array $data) : ?array
{
    $stmt = db_get_prepare_stmt($connection, $query, $data);
    check_success_insert_or_read_stmt_execute($connection, $stmt);
    $result_query = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result_query);
}

/**
 * Вставляет в БД одну строку, согласно запросу, используя подготовленные выражения
 *
 *
 * @param mysqli $connection Ресурс соединения
 * @param string $query Строка запроса на вставку в БД
 * @param array $data Массив с данными для вставки на место плэйсхолдеров
 *
 * @return void
 */
function db_insert_one_line_stmt(mysqli $connection, string $query, array $data) : void
{
    $stmt = db_get_prepare_stmt($connection, $query, $data);
    check_success_insert_or_read_stmt_execute($connection, $stmt);
}

/**
 * Обновляет запись в БД
 *
 *
 * @param mysqli $connection Ресурс соединения
 * @param string $query Строка запроса на обновление
 *
 * @return bool true - при успешном обновлении, false - при ошибке
 */
function db_update(mysqli $connection, string $query) : bool
{
    $result = mysqli_query($connection, $query);

    if (!$result) {
	    $error = mysqli_error($connection);
	    print("Ошибка MySQL: " . $error);
        return false;
    }
    return true;
}

/**
 * Читает из БД всё, согласно запросу
 *
 *
 * @param mysqli $connection Ресурс соединения
 * @param string $query Строка запроса на чтение
 *
 * @return array Ассоциативный массив результата запроса
 */
//function db_insert(mysqli $connection, string $query) : ?array
//{
//
//}
