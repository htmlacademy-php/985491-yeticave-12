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
 * @param array $get Данные из массива $_GET
 * @param array $session Данные из массива $_SESSION
 *
 * @return int Возвращает id лота
 */
function init_open_lot(array $get, array $session) : int
{
    if (isset($get['id'])) {
        $id = (int) $get['id'];

        if (isset($session['user_id'])) {
            $_SESSION['lot_id'] = $id;
        }
    }
    else {
        if (isset($session['lot_id'])) {
            $id = (int)$session['lot_id'];
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
 * @param array $session Данные из массива $_SESSION
 *
 * @return void
 */
function check_sign_in_for_add_lot(array $session) : void
{
    if (!isset($session['user_id'])) {
        http_response_code(403);
        exit("Для добавления лота необходимо зарегистрироваться на сайте.");
    }
}

/**
 * Проверяет, не выполнен ли уже вход в учетную запись (для операции добавления нового аккаунта)
 * Если да - выдает ошибку
 *
 * @param array $session Данные из массива $_SESSION
 *
 * @return void
 */
function check_sign_in_for_add_account(array $session) : void
{
    if (isset($session['user_id'])) {
        http_response_code(403);
        exit("Вы уже зарегистрированы.");
    }
}

/**
 * Возвращает номер активной страницы
 *
 * @param array $get Данные из массива $_GET
 *
 * @return int
 */
function set_active_page(array $get) : int
{
    if (isset($get['page'])) {
        return (int)$get['page'];
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

/**
 * Загружает файл
 * Возвращает путь к файлу или текст ошибки
 *
 * @param array $files Массив с данными о файле, на вход подаем массив $_FILES
 * @param string $name_folder_uploads_file строка имени папки с файлами
 * @param string $file_path Путь к папке в которой лежит папка с файлами
 *
 * @return string
 */
function download_file(array $files, string $name_folder_uploads_file, string $file_path): string {
    $errors_validate['file_img_lot'] = validate_file($files, 'file_img_lot');
    if ($errors_validate['file_img_lot'] === NULL) {
        if (move_uploaded_file($files['file_img_lot']['tmp_name'], $file_path . $files['file_img_lot']['name'])) {
            return $name_folder_uploads_file . $files['file_img_lot']['name'];
        }
        return 'Ошибка при перемещении файла ';
    }
    return $errors_validate['file_img_lot'];
}

/**
 * Добавляет лот в БД
 * Возвращает id добавленного лота
 *
 * @param mysqli $connection Ресурс соединения с БД
 * @param array $lot_data Данные по лоту полученные из БД
 *
 *
 * @return int id добавленного лота
 */
function add_lot(mysqli $connection, array $lot_data = []): int {
    $sql_insert_lot = 'INSERT INTO lots (date_create, name, description, url_image, start_price, date_end, step_price, author_id, category_id)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)';
    db_insert_one_line_stmt($connection, $sql_insert_lot, [$lot_data['date_create'], $lot_data['lot-name'],
        $lot_data['message'], $lot_data['file_img_lot'], $lot_data['lot-rate'], $lot_data['lot-date'], $lot_data['lot-step'], $lot_data['author_id'], $lot_data['category']]);

    return id_last_inserted_line($connection);
}

/**
 * Получает из БД все категории
 *
 * @param mysqli $connection Ресурс соединения с БД *
 *
 * @return array Все категории из БД
 */
function get_all_category(mysqli $connection): array {
    $sql_read_categories = "SELECT * FROM categories";
    return db_read_all($connection, $sql_read_categories);
}

