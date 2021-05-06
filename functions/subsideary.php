<?php
/**
 * Проверяет загруженный массив лотов на наличие сделанных на них ставок
 * При наличии, заменяет начальную цену на крайнюю ставку
 * 
 *
 * @param mysqli $connection Ресурс соединения
 * @param string $query Строка запроса на чтение
 *
 * @return array Ассоциативный массив результата запроса
 */
function subsideary_update_price(mysqli $connection, array $products) : array
{
    for ($i = 0; $i < count($products); $i++) {
        $id = (int)$products[$i]['id'];
    
        $sql_read_bet = "SELECT bets.date_create, bets.price FROM bets WHERE bets.lot_id = '$id' ORDER BY bets.date_create DESC ";    
        $bet_open_lot = db_read($connection, $sql_read_bet);
    
        if ($bet_open_lot === NULL) {
            continue;
        }
    
        if ((int)$bet_open_lot[0]['price'] > $products['price']) {
            $products[$i]['price'] = $bet_open_lot[0]['price'];
        }
    }
    return $products;
}