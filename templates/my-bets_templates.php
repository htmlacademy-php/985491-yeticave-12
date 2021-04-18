  <main>
    <nav class="nav">
      <ul class="nav__list container">
        <?php foreach ($categories as $category): ?>
            <li class="nav__item">
                <a href="pages/all-lots.html"><?=htmlspecialchars($category['name']) ?></a>
            </li>
        <?php endforeach; ?> 
      </ul>
    </nav>
    <section class="rates container">
      <h2>Мои ставки</h2>
      <table class="rates__list">
        <?php foreach ($lots_with_my_bet as $lot_with_my_bet): ?>
          <?php $hours_and_minuts_with_seconds = get_dt_range_with_seconds($lot_with_my_bet['date_end']);
            
            if ($lot_with_my_bet['winner'] == $_SESSION['user_id']) {
              //$class_item = "rates__item--win";
              $class_timer = "timer--win";
              $text_timer = "Ставка выиграла";
            }
            elseif ($lot_with_my_bet['winner'] != $_SESSION['user_id'] && $hours_and_minuts_with_seconds[2] < 1) {
              $class_item = "rates__item--end";
              $class_timer = "timer--end";
              $text_timer = "Торги окончены";              
            }
            elseif ($hours_and_minuts_with_seconds[0] < 1) {
              $class_timer = "timer--finishing";
              $text_timer = get_timer_value($hours_and_minuts_with_seconds);              
            } 
            else {
              $text_timer = get_timer_value($hours_and_minuts_with_seconds);
            }                       
            ?>

          <tr class="rates__item <?=$class_item ?>">
            <td class="rates__info">
              <div class="rates__img">
                <img src="<?=$lot_with_my_bet['url_image'] ?>" width="54" height="40" alt="Сноуборд">
              </div>
              <h3 class="rates__title"><a href="/lot.php?id=<?=$lot_with_my_bet['lot_id'] ?>"><?=htmlspecialchars($lot_with_my_bet['name']) ?></a></h3>
            </td>
            <td class="rates__category">
              <?=htmlspecialchars($lot_with_my_bet['name_category']) ?>
            </td>        
            <td class="rates__timer">
              <div class="timer <?=$class_timer ?>"><?=$text_timer ?></div>
            </td>
            <td class="rates__price">
              <?=htmlspecialchars(format_price($lot_with_my_bet['price_my_bet'])) ?>
            </td>
            <td class="rates__time">
              <?=get_dt_range_back($lot_with_my_bet['date_create_bet']) ?>
              <!-- 5 минут назад -->
            </td>
          </tr>
        <?php endforeach; ?>         
      </table>
    </section>
  </main>
