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
 * Читает из БД
 * 
 *
 * @param mysqli $connection Ресурс соединения
 * @param string $query Строка запроса на чтение
 *
 * @return array Ассоциативный массив результата запроса
 */
function db_read(mysqli $connection, string $query) : ?array
{
    $result_query = mysqli_query($connection, $query);
    return mysqli_fetch_all($result_query, MYSQLI_ASSOC);    
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