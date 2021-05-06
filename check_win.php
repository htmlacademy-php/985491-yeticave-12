<?php 
$sql_read_lot_ended_and_without_winner = "SELECT lots.id, lots.author_id FROM lots WHERE (lots.date_end <= NOW()) AND (lots.winner_id IS NULL)";
$lots_ended_and_without_winner = db_read($connection, $sql_read_lot_ended_and_without_winner);

foreach ($lots_ended_and_without_winner as $lot_ended_and_without_winner) {
    $id_lot = $lot_ended_and_without_winner['id'];
    $sql_read_bets_for_lot = "SELECT bets.id, bets.user FROM bets WHERE bets.lot = '$id_lot' ORDER BY bets.date_create DESC";    
    $bets_for_lot = db_read($connection, $sql_read_bets_for_lot);    

    if (count($bets_for_lot) > 0) {
        $id_winner = (int)$bets_for_lot[0]['user'];        
    }
    else {
        //Записываю в победители автора, если ставок не было, т.к. если оставлять NULL, то он будет снова и снова перебирать массив "лотов без победителей, по которым его и не будет
        $id_winner = (int)$lot_ended_and_without_winner['author'];
    }

    $sql_write_winner_in_lot = "UPDATE lots SET winner = '$id_winner' WHERE id = '$id_lot'";
    db_update($connection, $sql_write_winner_in_lot);
}


