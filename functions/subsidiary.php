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
function update_price(mysqli $connection, array $products) : array
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
 * Если открыта сессия - прописывает в нее id лота *
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
 * Возвращает номер активной страницы
 *
 *
 * @return int
 */
function set_active_page() : int
{
    if (isset($_GET['page'])) {
        return (int)$_GET['page'];
    }
    else {
        return 1;
    }
}

/**
 * Форматирует цену поданную на вход
 * Делит на разряды и добавляет символ рубля
 *
 * @param int $price Цена
 *
 * @return string
 */
function format_price(int $price): string {
    $price = ceil($price);
    if ($price < 1000) {
        return $price . ' ₽';
    }
    return number_format($price, 0, ".", " ") . ' ₽';
}

/**
 * Форматирует время поданное на вход
 * Вставляет двоеточие между часами и минутами
 *
 * @param array $hours_and_minuts Массив с часами и минутами
 *
 * @return string
 */
function get_timer_value(array $hours_and_minuts): string {
    return implode(':', $hours_and_minuts) ?? "";
}