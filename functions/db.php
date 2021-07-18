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
 * Читает из БД все, согласно запросу, используя подготовленные выражения
 *
 *
 * @param mysqli $connection Ресурс соединения
 * @param string $query Строка запроса на чтение
 * @param array $data Массив с данными для вставки на место плэйсхолдеров
 *
 * @return array Ассоциативный массив результата запроса
 */
function db_read_all_stmt(mysqli $connection, string $query, array $data) : ?array
{
    $stmt = db_get_prepare_stmt($connection, $query, $data);
    check_success_insert_or_read_stmt_execute($connection, $stmt);
    $result_query = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_all($result_query, MYSQLI_ASSOC);
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
 * олучает количество строк (записей), согласно запросу, используя подготовленные выражения
 *
 *
 * @param mysqli $connection Ресурс соединения
 * @param string $query Строка запроса на чтение
 * @param array $data Массив с данными для вставки на место плэйсхолдеров
 *
 * @return int Количество строк
 */
function db_num_rows_stmt(mysqli $connection, string $query, array $data) : int
{
    $stmt = db_get_prepare_stmt($connection, $query, $data);
    check_success_insert_or_read_stmt_execute($connection, $stmt);
    $result_query = mysqli_stmt_get_result($stmt);
    return mysqli_num_rows($result_query);
}

/**
 * Проверяет успешность выполнения записи в БД
 * Если нет, выдает ошибку
 *
 *
 * @param mysqli $connection Ресурс соединения
 * @param mysqli_stmt $stmt Подготовленное выражение
 *
 * @return void
 */
function check_success_insert_or_read_stmt_execute(mysqli $connection, mysqli_stmt $stmt) : void
{
    if (!mysqli_stmt_execute($stmt)) {
        $error = mysqli_error($connection);
        exit("Ошибка MySQL: " . $error);
    }
}

/**
 * Получает id крайней добавленной в БД записи
 *
 * @param mysqli $connection Ресурс соединения
 *
 * @return int
 */
function id_last_inserted_line(mysqli $connection) : int
{
    $id_last_insert_line = mysqli_insert_id($connection);
    if ($id_last_insert_line === 0) {
        exit('Ошибка получения id последней добавленной записи');
    }
    return (int)$id_last_insert_line;
}
