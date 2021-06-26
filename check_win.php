<?php
$lots_ended_and_without_winner = get_all_lot_ended_and_without_winner($connection);

foreach ($lots_ended_and_without_winner as $lot_ended_and_without_winner) {
    $bets_for_lot = get_all_bets_for_lot($connection, $lot_ended_and_without_winner['id']);

    if (count($bets_for_lot) > 0) {
        $id_winner = (int)$bets_for_lot[0]['user_id'];
    }
    else {
        //Записываю в победители автора, если ставок не было, т.к. если оставлять NULL, то он будет снова и снова перебирать массив "лотов без победителей, по которым его и не будет
        $id_winner = (int)$lot_ended_and_without_winner['author_id'];
    }

    set_winner_for_lot($connection, $lot_ended_and_without_winner['id'], $id_winner);
}


