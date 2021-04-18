<?php 
$sql_read_lot_ended_and_without_winner = "SELECT lots.id, lots.author FROM lots WHERE (lots.date_end <= NOW()) AND (lots.winner IS NULL)";
$result_lots_ended_and_without_winner = mysqli_query($connect, $sql_read_lot_ended_and_without_winner);
$lots_ended_and_without_winner = mysqli_fetch_all($result_lots_ended_and_without_winner, MYSQLI_ASSOC);

foreach ($lots_ended_and_without_winner as $lot_ended_and_without_winner) {
    $id_lot = $lot_ended_and_without_winner['id'];
    $sql_read_bets_for_lot = "SELECT bets.id, bets.user FROM bets WHERE bets.lot = '$id_lot' ORDER BY bets.date_create DESC";
    $result_bets_for_lot = mysqli_query($connect, $sql_read_bets_for_lot);
    $bets_for_lot = mysqli_fetch_all($result_bets_for_lot, MYSQLI_ASSOC);    

    if (count($bets_for_lot) > 0) {
        $id_winner = (int)$bets_for_lot[0]['user'];
        print('Зашел');
    }
    else {
        //Записываю в победители автора, если ставок не было, т.к. если оставлять NULL, то он будет снова и снова перебирать массив "лотов без победителей, по которым его и не будет
        $id_winner = (int)$lot_ended_and_without_winner['author'];
    }

    $sql_write_winner_in_lot = "UPDATE lots SET winner = '$id_winner' WHERE id = '$id_lot'";
    $result = mysqli_query($connect, $sql_write_winner_in_lot);

    if (!$result) { 
	    $error = mysqli_error($con); 
	    print("Ошибка MySQL: " . $error);
    }
}


