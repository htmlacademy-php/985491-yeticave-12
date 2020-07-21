USE yeticave;

-- вставка категорий
INSERT INTO categories (name, symbol_code) VALUES ('Доски и лыжи', 'boards');
INSERT INTO categories (name, symbol_code) VALUES ('Крепления', 'attachment');
INSERT INTO categories (name, symbol_code) VALUES ('Ботинки', 'boots');
INSERT INTO categories (name, symbol_code) VALUES ('Одежда', 'clothing');
INSERT INTO categories (name, symbol_code) VALUES ('Инструменты', 'tools');
INSERT INTO categories (name, symbol_code) VALUES ('Разное', 'other');

--вставка пользователей
INSERT INTO users (date_registered, email, name, password, contact) 
VALUES ('2020-01-26 20:20:24', 'klapeyron@mail.ru', 'Klapeyron', 'PVequallyURT', 'г. Санкт-Петербург, ул. Менделеева 1, тел.287-84-15');
INSERT INTO users (date_registered, email, name, password, contact) 
VALUES ('2019-08-28 09:00:30', 'shuhov@yandex.ru', 'Vladimir', 'hyperboloid', 'г. Москва, ул. Попова 1, тел.8-912-458-79-88');

--вставка лотов
INSERT INTO lots (date_create, name, description, url_image, start_price, date_end, step_price, author, category) 
VALUES ('2020-06-26 21:33:30', '2014 Rossingnol District Snowboard', 'Классный борд!', 'img/lot-1.jpg', 10999, '2020-07-25 21:33:30', 200, 1, 1);
INSERT INTO lots (date_create, name, description, url_image, start_price, date_end, step_price, author, category) 
VALUES ('2020-07-20 16:33:30', 'DC Ply Mens 2016/2017 Snowboard', 'Спортивный борд', 'img/lot-2.jpg', 159999, '2020-07-22 20:52:33', 5000, 2, 1);
INSERT INTO lots (date_create, name, description, url_image, start_price, date_end, step_price, author, category) 
VALUES ('2020-06-15 12:54:56', 'Крепления Union Contact Pro 2015 года размер L/XL', 'Мощные крепления', 'img/lot-3.jpg', 8000, '2020-06-20 10:33:00', 500, 2, 2);
INSERT INTO lots (date_create, name, description, url_image, start_price, date_end, step_price, author, category) 
VALUES ('2020-06-23 23:59:59', 'Ботинки для сноуборда DC Mutiny Charocal', 'Отличные боты!', 'img/lot-4.jpg', 10999, '2020-06-25 19:25:41', 350, 1, 3);
INSERT INTO lots (date_create, name, description, url_image, start_price, date_end, step_price, author, category) 
VALUES ('2020-07-21 17:36:50', 'Куртка для сноуборда DC Mutiny Charocal', 'Куртка почти новая, продаю так как не подошла по размеру', 'img/lot-5.jpg', 7500, '2020-07-25 22:32:57', 150, 1, 4);
INSERT INTO lots (date_create, name, description, url_image, start_price, date_end, step_price, author, category) 
VALUES ('2020-07-21 15:31:10', 'Маска Oakley Canopy', 'Новая маска. Отлично защищает от ветра!', 'img/lot-6.jpg', 5400, '2020-07-27 11:15:12', 200, 2, 6);

--вставка ставок
INSERT INTO bets (date_create, price, user, lot) 
VALUES ('2020-07-21 13:25:34', 165000, 1, 2);
INSERT INTO bets (date_create, price, user, lot) 
VALUES ('2020-06-17 21:45:00', 8500, 1, 3);

--получение всех категорий
SELECT * FROM categories;

--получение самых новых открытых лотов
SELECT lots.name, start_price, url_image, step_price, categories.name FROM lots JOIN categories 
ON lots.category = categories.id WHERE lots.winner IS NOT NULL ORDER BY date_create DESC;

--запрос лота по его id
SELECT * FROM lots JOIN categories ON lots.category = categories.id WHERE lots.id = 2;

--обновление названия лота по его id
UPDATE lots SET name = 'Good Sport Board!' WHERE id = 2;

--получение списка ставок для лота по его идентификатору, с сортировкой по дате
SELECT lots.date_create, price, user FROM bets JOIN lots ON bets.lot = lots.id WHERE lots.id = 2 ORDER BY date_create DESC;

