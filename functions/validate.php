<?php
/**
 * Валидирует данные формы добавления ставки, получая данные из $POST
 *
 * @param array $open_lot Ассоциативный массив с данными открытого лота
 * @param int $current_price Текущая цена лота
 *
 * @return array Возвращает массив с ошибками, или пустой массив, если ошибок нет
 */
function validate_bets_form(array $open_lot, int $current_price) : array
{
    $rules = [
        'cost' => function() use ($open_lot, $current_price): ?string {
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
    ];

    //Валидация соответствующих полей и сохранение ошибок (при наличии)
    foreach ($_POST as $key => $value) {
        if (isset($rules[$key])) {
            $rule = $rules[$key];
            $errors_validate[$key] = $rule();
        }
    }
    return array_filter($errors_validate);  //убираем пустые значения в массиве и возвращаем его
}

/**
 * Валидирует данные формы добавления лота, получая данные из $POST
 *
 * @param array $categories Ассоциативный массив с категориями
 *
 * @return array Возвращает массив с ошибками, или пустой массив, если ошибок нет
 */
function validate_add_lot_form(array $categories) : array
{
    $rules = [
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
    }
    return array_filter($errors_validate);  //убираем пустые значения в массиве и возвращаем его
}

/**
 * Валидирует приложенный к форме файл, получая данные из $FILES
 *
 * @param string $name Имя файла
 * @param string $name_folder_uploads_file Имя папки с изображениями
 *
 * @return ?string Возвращает ошибку (при наличии) или NULL
 */
function validate_file(string $name, string $name_folder_uploads_file): ?string {
    if (isset($_FILES[$name]) && !empty($_FILES[$name]['name'])) {
        $file_name = $_FILES[$name]['tmp_name'];
        $file_path = sys_get_temp_dir();

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
 *
 * @return array Возвращает массив с ошибками, или пустой массив, если ошибок нет
 */
function validate_add_account(mysqli $connection): array {
    $rules = [
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
    }
    return array_filter($errors_validate);  //убираем пустые значения в массиве и возвращаем его
}

/**
 * Валидирует данные формы хода в аккаунт, получая данные из $POST
 *
 * @param mysqli $connection Ресурс соединения
 *
 * @return array Возвращает массив с ошибками, или пустой массив, если ошибок нет
 */
function validate_sign_in(mysqli $connection): array {
    $rules = [
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
            mysqli_stmt_bind_param($stmt, 's', get_post_val('email'));
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $exist_email = mysqli_fetch_all($result, MYSQLI_ASSOC);

            if (!$exist_email) {
                return 'Пользователь с таким email еще не зарегистрирован';
            }

            return NULL;
        },
        'password' => function() use ($connection): ?string {
            $error = validate_filled('password');
            if ($error) {
                return $error;
            }

            $sql_read_password_users = "SELECT users.password FROM users WHERE users.email = ?";
            $stmt = mysqli_prepare($connection, $sql_read_password_users);
            mysqli_stmt_bind_param($stmt, 's', get_post_val('email'));
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $exist_email = mysqli_fetch_assoc ($result);

            if (!password_verify(get_post_val('password'), $exist_email['password'])) {
                return 'Вы ввели неверный пароль';
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
    }
    return array_filter($errors_validate);  //убираем пустые значения в массиве и возвращаем его
}

/**
 * Валидирует данные поиска, получая данные из $GET
 *
 *
 * @return array Возвращает массив с ошибками, или пустой массив, если ошибок нет
 */
function validate_search(): array {
    $rules = [
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
    }
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
