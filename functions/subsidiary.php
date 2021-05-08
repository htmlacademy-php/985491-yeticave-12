<?php
/**
 * Проверяет загруженный массив лотов на наличие сделанных на них ставок
 * При наличии, заменяет начальную цену на крайнюю ставку
 *
 *
 * @param mysqli $connection Ресурс соединения
 * @param array $products Массив с лотами (продуктами), в которых проверяется актуальность стартовой цены
 *
 * @return array Проверенный массив
 */
function subsidiary_update_price(mysqli $connection, array $products) : array
{
    for ($i = 0; $i < count($products); $i++) {
        $id = (int)$products[$i]['id'];

        $sql_read_bet = "SELECT bets.date_create, bets.price FROM bets WHERE bets.lot_id = '$id' ORDER BY bets.date_create DESC ";
        $bet_open_lot = db_read_all($connection, $sql_read_bet);

        if ($bet_open_lot === NULL) {
            continue;
        }

        if ((int)$bet_open_lot[0]['price'] > $products['price']) {
            $products[$i]['price'] = $bet_open_lot[0]['price'];
        }
    }
    return $products;
}

/**
 * Проверяет указан ли id лота в $GET или в $SESSION и возвращает id лота
 * Если открыта сессия - прописывает в нее id лота
 *
 *
 *
 * @return int Возвращает id лота
 */
function init_open_lot() : int
{
    if (isset($_GET['id'])) {
        $id = (int) $_GET['id'];

        if (isset($_SESSION['user_id'])) {
            $_SESSION['lot_id'] = $id;
        }
    }
    else {
        if (isset($_SESSION['lot_id'])) {
            $id = (int)$_SESSION['lot_id'];
        }
        else {
            http_response_code(404);
            exit("Ошибка подключения: не указан id");
        }
    }
    return $id;
}

/**
 * Проверяет не сделаны ли уже ставки на данный лот
 * Если сделаны, указывает её как цену лота
 *
 * @param array $bet_open_lot Массив со ставками открытого лота
 * @param int $start_price_lot Стартовая цена лота
 *
 * @return int Возвращает текущую цены лота
 */
function check_price_lot(array $bet_open_lot, int $start_price_lot) : int
{
    if (isset($bet_open_lot[0]['price'])) {
        $current_price = (int)$bet_open_lot[0]['price'];
    }
    else {
        $current_price = $start_price_lot;
    }
    return $current_price;
}

/**
 * Проверяет выполнен ли вход в учетную запись (для операции добавления нового лота)
 * Если нет, выдает ошибку
 *
 *
 * @return void
 */
function check_sign_in_for_add_lot() : void
{
    if (!isset($_SESSION['user_id'])) {
        http_response_code(403);
        exit("Для добавления лота необходимо зарегистрироваться на сайте.");
    }
}

/**
 * Проверяет, не выполнен ли уже вход в учетную запись (для операции добавления нового аккаунта)
 * Если да - выдает ошибку
 *
 *
 * @return void
 */
function check_sign_in_for_add_account() : void
{
    if (isset($_SESSION['user_id'])) {
        http_response_code(403);
        exit("Вы уже зарегистрированы.");
    }
}

/**
 * Проверяет успешность выполнения записи в БД
 * Если нет, выдает ошибку
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
