<?php
/**
 * Валидирует данные формы добавления ставки, получая данные из $POST
 *
 * @param array $open_lot Ассоциативный массив с данными открытого лота
 * @param int $current_price Текущая цена лота
 * @param array $session Данные из массива $_SESSION
 * @param array $post Данные из массива $_POST
 *
 * @return array Возвращает массив с ошибками, или пустой массив, если ошибок нет
 */
function validate_bets_form(array $open_lot, int $current_price, array $session, array $post) : array
{
    /*$rules = [
        'cost' => function validate_field_cost(array $open_lot, int $current_price) : ?string{
            $error = validate_filled('cost');
            if ($error) {
                return $error;
            }
            if (!isset($_SESSION['user_id'])){
                return 'Необходимо зарегистрироваться';
            }
            if ($_POST['cost'] <= 0 || !is_numeric($_POST['cost'])) {
                return 'Начальная цена должна быть целым числом больше нуля';
            }

            $min_bet = $current_price + (int)$open_lot['step_price'];
            if ((int)$_POST['cost'] < $min_bet) {
                return 'Мин.ставка д.б. не менее ' . $min_bet .  ' ₽';
            }

            return NULL;
        }
    ];*/

    //Валидация соответствующих полей и сохранение ошибок (при наличии)
    /*foreach ($_POST as $key => $value) {
        if (isset($rules[$key])) {
            $rule = $rules[$key];
            $errors_validate[$key] = $rule();
        }
    }*/
    $errors_validate['cost'] = validate_field_cost($open_lot, $current_price, $session, $post);

    return array_filter($errors_validate);  //убираем пустые значения в массиве и возвращаем его
}

/**
 * Валидирует данные формы добавления лота, получая данные из $POST
 *
 * @param array $categories Ассоциативный массив с категориями
 * @param array $post Данные из массива $_POST
 *
 * @return array Возвращает массив с ошибками, или пустой массив, если ошибок нет
 */
function validate_add_lot_form(array $categories, array $post) : array
{
    /*$rules = [
        'lot-name' => function(): ?string {
            return validate_filled('lot-name');
        },
        'category' => function() use ($categories): ?string {
            if (empty($_POST['category'])) {
                return 'Не выбрана категория';
            }

            $category_exists = false;
            foreach ($categories as $item_category) { //проверяется, что введенная категория существует
                if ($item_category['id'] === $_POST['category']) {
                    $category_exists = true;
                }
            }
            if (!$category_exists) {
                return 'Выбрана несуществующая категория';
            }
            return NULL;
        },
        'message' => function(): ?string {
            return validate_filled('message');
        },
        'lot-rate' => function(): ?string {
            $error = validate_filled('lot-rate');
            if ($error) {
                return $error;
            }
            if ($_POST['lot-rate'] <= 0 || !is_numeric($_POST['lot-rate']) ) {
                return 'Начальная цена должна быть числом больше нуля';
            }
            return NULL;
        },
        'lot-step' => function(): ?string {
            $error = validate_filled('lot-step');
            if ($error) {
                return $error;
            }

            if (!ctype_digit ($_POST['lot-step']) || $_POST['lot-step'] <= 0) {
                return 'Шаг ставки должен быть целым числом больше нуля';
            }
            return NULL;
        },
        'lot-date' => function(): ?string {
            $error = validate_filled('lot-date');
            if ($error) {
                return $error;
            }

            if (validation_format_date($_POST['lot-date'])) {
                return 'Дата должна быть введена в формате "ГГГГ-ММ-ДД"';
            }

            if (strtotime($_POST['lot-date']) < (time() + 86400)) {
                return 'Дата окончания торгов должна быть позже текущего времени минимум на 24 часа';
            }
            return NULL;
        }
    ];

    //Валидация соответствующих полей и сохранение ошибок (при наличии)
    foreach ($_POST as $key => $value) {
        if (isset($rules[$key])) {
            $rule = $rules[$key];
            $errors_validate[$key] = $rule();
        }
    }*/
    $errors_validate['lot-name'] = validate_filled('lot-name');
    $errors_validate['category'] = validate_field_category($categories, $post);
    $errors_validate['message'] = validate_filled('message');
    $errors_validate['lot-rate'] = validate_field_lot_rate($post);
    $errors_validate['lot-step'] = validate_field_lot_step($post);
    $errors_validate['lot-date'] = validate_field_lot_date($post);

    return array_filter($errors_validate);  //убираем пустые значения в массиве и возвращаем его
}

/**
 * Валидирует приложенный к форме файл, получая данные из $FILES
 *
 * @param string $name Имя файла
 *
 * @return ?string Возвращает ошибку (при наличии) или NULL
 */
function validate_file(array $files, string $name): ?string {
    if (isset($files[$name]) && !empty($files[$name]['name'])) {
        $file_name = $files[$name]['tmp_name'];

        $type_file = mime_content_type($file_name);
        if ($type_file === 'image/jpeg' || $type_file === 'image/png' || $type_file === 'image/jpg') {
            return NULL;
        }

        return 'Допустимы только файлы изображений типов jpeg, jpg и png ';
    }

    return 'Поле не заполнено ';
}

/**
 * Валидирует данные формы добавления аккаунта, получая данные из $POST
 *
 * @param mysqli $connection Ресурс соединения
 * @param array $post Данные из массива $_POST
 *
 * @return array Возвращает массив с ошибками, или пустой массив, если ошибок нет
 */
function validate_add_account(mysqli $connection, array $post): array {
    /*$rules = [
        'email' => function() use ($connection): ?string {
            $error = validate_filled('email');
            if ($error) {
                return $error;
            }

            if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))  {
                return 'Неверный формат адреса электронной почты. Проверьте введенный email';
            }

            $sql_read_email_users = "SELECT users.email FROM users WHERE users.email = ?";
            $stmt = mysqli_prepare($connection, $sql_read_email_users);
            $get_post_val = get_post_val('email');
            mysqli_stmt_bind_param($stmt, 's', $get_post_val);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $exist_email = mysqli_fetch_all($result, MYSQLI_ASSOC);

            if ($exist_email) {
                return 'Пользователь с таким email уже зарегистрирован';
            }

            return NULL;
        },
        'password' => function(): ?string {
            return validate_filled('password');
        },
        'name' => function(): ?string {
            return validate_filled('name');
        },
        'message' => function(): ?string {
            return validate_filled('message');
        }
    ];

    //Валидация соответствующих полей и сохранение ошибок (при наличии)
    foreach ($_POST as $key => $value) {
        if (isset($rules[$key])) {
            $rule = $rules[$key];
            $errors_validate[$key] = $rule();
        }
    }*/
    $errors_validate['email'] = validate_field_email_in_add_account($connection, $post);
    $errors_validate['password'] = validate_filled('password');
    $errors_validate['name'] = validate_filled('name');
    $errors_validate['message'] = validate_filled('message');
    return array_filter($errors_validate);  //убираем пустые значения в массиве и возвращаем его
}

/**
 * Валидирует данные формы входа в аккаунт, получая данные из $POST
 *
 * @param mysqli $connection Ресурс соединения
 * @param array $post Данные из массива $_POST
 *
 * @return array Возвращает массив с ошибками, или пустой массив, если ошибок нет
 */
function validate_sign_in(mysqli $connection, array $post): array {
    $errors_validate['email'] = validate_field_email_in_sign_in($connection, $post);
    $errors_validate['password'] = validate_field_password_in_sign_in($connection);

    return array_filter($errors_validate);  //убираем пустые значения в массиве и возвращаем его
}

/**
 * Валидирует данные поиска, получая данные из $GET
 *
 *
 * @return array Возвращает массив с ошибками, или пустой массив, если ошибок нет
 */
function validate_search(): array {
    /*$rules = [
        'search' => function() : ?string {
            return validate_filled_GET('search');
        }
    ];
    $errors_validate = [];
    //Валидация соответствующих полей и сохранение ошибок (при наличии)
    foreach ($_POST as $key => $value) {
        if (isset($rules[$key])) {
            $rule = $rules[$key];
            $errors_validate[$key] = $rule();
        }
    }*/
    $errors_validate['search'] = validate_filled_GET('search');

    return array_filter($errors_validate);  //убираем пустые значения в массиве и возвращаем его
}

/**
 * Валидирует формат даты
 *
 *
 * @param string $date Строка даты для проверки
 *
 * @return ?string Текст ошибки или NULL
 */
function validation_format_date(string $date): ?string {
    if (date_create_from_format('Y-m-d', $date) === false) {
        return 'Дата должна быть введена в формате "ГГГГ-ММ_ДД"';
    }

    return NULL;
}

/**
 * Валидирует заполненность поля, получая данные из $_POST
 *
 *
 * @param string $name Имя поля в $_POST
 *
 * @return ?string Текст ошибки или NULL
 */
function validate_filled(string $name): ?string {
    if (empty($_POST[$name])) {
        return 'Поле не заполнено ';
    }

    return NULL;
}

/**
 * Валидирует заполненность поля, получая данные из $_GET
 *
 *
 * @param string $name Имя поля в $_GET
 *
 * @return ?string Текст ошибки или NULL
 */
function validate_filled_GET(string $name): ?string {
    if (empty($_GET[$name])) {
        return 'Поле не заполнено ';
    }

    return NULL;
}

/**
 * Валидирует поле 'cost' в форме добавления ставки
 *
 *
 * @param array $open_lot Массив с открытым лотом
 * @param int $current_price Текущая цена лота
 * @param array $session Данные из массива $_SESSION
 * @param array $post Данные из массива $_POST
 *
 * @return ?string Текст ошибки или NULL
 */
function validate_field_cost(array $open_lot, int $current_price, array $session, array $post): ?string {
    $error = validate_filled('cost');
    if ($error) {
        return $error;
    }
    if (!isset($session['user_id'])){
        return 'Необходимо зарегистрироваться';
    }
    if ($post['cost'] <= 0 || !is_numeric($post['cost'])) {
        return 'Начальная цена должна быть целым числом больше нуля';
    }

    $min_bet = $current_price + (int)$open_lot['step_price'];
    if ((int)$post['cost'] < $min_bet) {
        return 'Мин.ставка д.б. не менее ' . $min_bet .  ' ₽';
    }

    return NULL;
}

/**
 * Валидирует поле 'category' в форме добавления лота
 *
 *
 * @param array $categories Массив со списком существующих категорий
 * @param array $post Данные из массива $_POST
 *
 * @return ?string Текст ошибки или NULL
 */
function validate_field_category(array $categories, array $post): ?string {
    if (empty($post['category'])) {
        return 'Не выбрана категория';
    }

    $category_exists = false;
    foreach ($categories as $item_category) { //проверяется, что введенная категория существует
        if ($item_category['id'] === $post['category']) {
            $category_exists = true;
        }
    }
    if (!$category_exists) {
        return 'Выбрана несуществующая категория';
    }
    return NULL;
}

/**
 * Валидирует поле 'lot-rate' в форме добавления лота
 *
 * @param array $post Данные из массива $_POST
 *
 * @return ?string Текст ошибки или NULL
 */
function validate_field_lot_rate(array $post): ?string {
    $error = validate_filled('lot-rate');
    if ($error) {
        return $error;
    }
    if ($post['lot-rate'] <= 0 || !is_numeric($post['lot-rate']) ) {
        return 'Начальная цена должна быть числом больше нуля';
    }
    return NULL;
}

/**
 * Валидирует поле 'lot-step' в форме добавления лота
 *
 * @param array $post Данные из массива $_POST
 *
 * @return ?string Текст ошибки или NULL
 */
function validate_field_lot_step(array $post): ?string {
    $error = validate_filled('lot-step');
    if ($error) {
        return $error;
    }

    if (!ctype_digit ($post['lot-step']) || $post['lot-step'] <= 0) {
        return 'Шаг ставки должен быть целым числом больше нуля';
    }
    return NULL;
}

/**
 * Валидирует поле 'lot-date' в форме добавления лота
 *
 * @param array $post Данные из массива $_POST
 *
 * @return ?string Текст ошибки или NULL
 */
function validate_field_lot_date(array $post): ?string {
    $error = validate_filled('lot-date');
    if ($error) {
        return $error;
    }

    if (validation_format_date($post['lot-date'])) {
        return 'Дата должна быть введена в формате "ГГГГ-ММ-ДД"';
    }

    if (strtotime($post['lot-date']) < (time() + 86400)) {
        return 'Дата окончания торгов должна быть позже текущего времени минимум на 24 часа';
    }
    return NULL;
}

/**
 * Валидирует поле 'email' в форме добавления аккаунта
 *
 * @param array $post Данные из массива $_POST
 *
 * @return ?string Текст ошибки или NULL
 */
function validate_field_email_in_add_account(mysqli $connection, array $post): ?string {
    $error = validate_filled('email');
    if ($error) {
        return $error;
    }

    if (!filter_var($post['email'], FILTER_VALIDATE_EMAIL))  {
        return 'Неверный формат адреса электронной почты. Проверьте введенный email';
    }

    $sql_read_email_users = "SELECT users.email FROM users WHERE users.email = ?";
    $stmt = mysqli_prepare($connection, $sql_read_email_users);
    $email = get_post_val('email');
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $exist_email = mysqli_fetch_all($result, MYSQLI_ASSOC);

    if ($exist_email) {
        return 'Пользователь с таким email уже зарегистрирован';
    }

    return NULL;
}

/**
 * Валидирует поле 'email' в форме входа в аккаунт
 *
 * @param array $post Данные из массива $_POST
 *
 * @return ?string Текст ошибки или NULL
 */
function validate_field_email_in_sign_in(mysqli $connection, array $post): ?string {
    $error = validate_filled('email');
    if ($error) {
        return $error;
    }

    if (!filter_var($post['email'], FILTER_VALIDATE_EMAIL))  {
        return 'Неверный формат адреса электронной почты. Проверьте введенный email';
    }

    $sql_read_email_users = "SELECT users.email FROM users WHERE users.email = ?";
    $stmt = mysqli_prepare($connection, $sql_read_email_users);

    $email = get_post_val('email');
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $exist_email = mysqli_fetch_all($result, MYSQLI_ASSOC);

    if (!$exist_email) {
        return 'Пользователь с таким email еще не зарегистрирован';
    }

    return NULL;
}

/**
 * Валидирует поле 'password' в форме входа в аккаунт
 *
 *
 * @return ?string Текст ошибки или NULL
 */
function validate_field_password_in_sign_in(mysqli $connection): ?string {
    $error = validate_filled('password');
    if ($error) {
        return $error;
    }

    $sql_read_password_users = "SELECT users.password FROM users WHERE users.email = ?";
    $stmt = mysqli_prepare($connection, $sql_read_password_users);
    $email = get_post_val('email');
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $exist_email = mysqli_fetch_assoc ($result);

    if (!password_verify(get_post_val('password'), $exist_email['password'])) {
        return 'Вы ввели неверный пароль';
    }

    return NULL;
}
