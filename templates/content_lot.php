
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
    <section class="lot-item container">
      <h2><?=htmlspecialchars($open_lot['name']) ?></h2>
      <div class="lot-item__content">
        <div class="lot-item__left">
          <div class="lot-item__image">
            <img src="<?=$open_lot['url_image'] ?>" width="730" height="548" alt="Сноуборд">
          </div>
          <p class="lot-item__category">Категория: <span><?=htmlspecialchars($open_lot['name_category']) ?></span></p>
          <p class="lot-item__description">
            <?=htmlspecialchars($open_lot['description']) ?>
            </p>
        </div>
        <div class="lot-item__right">

          <div class="lot-item__state">
            <?php $hours_and_minuts = get_dt_range($open_lot['date_end']);?>
            <div class="lot-item__timer timer <?php if($hours_and_minuts[0] < 1): ?>timer--finishing<?php endif; ?>">
              <?=get_timer_value($hours_and_minuts);?>
            </div>
            <div class="lot-item__cost-state">
              <div class="lot-item__rate">
                <span class="lot-item__amount">Текущая цена</span>
                <span class="lot-item__cost"><?=htmlspecialchars(format_price($current_price)) ?></span>
              </div>
              <div class="lot-item__min-cost">
                <?php $new_bet = format_price($current_price + (int)$open_lot['step_price']) ?>
                Мин. ставка <span><?=htmlspecialchars($new_bet) ?></span>
              </div>
            </div>
            <?php if (isset($_SESSION['user_id'])) : ?>
            <form class="lot-item__form" action="lot.php?id=<?=$_SESSION['lot_id'] ?>" method="post" autocomplete="off">
              <p class="lot-item__form-item form__item <?php if(count($errors_validate) > 0): ?> form__item--invalid
              <?php
              endif; ?>">
                <label for="cost">Ваша ставка</label>
                <input id="cost" type="text" name="cost" value="<?=get_filtered_post_val('cost'); ?>" placeholder="<?=htmlspecialchars($new_bet) ?>">
                <span class="form__error"><?php echo ($errors_validate['cost'] ?? "") ?></span>
              </p>
              <button type="submit" name="submit_bet" class="button">Сделать ставку</button>
            </form>
            <?php endif; ?>
          </div>
          <div class="history">
            <h3>История ставок (<span><?=count($bet_open_lot) ?></span>)</h3>
            <table class="history__list">
              <?php if (count($bet_open_lot)) {
              foreach ($bet_open_lot as $bet): ?>
                <tr class="history__item">
                  <td class="history__name"><?=htmlspecialchars($bet['name']) ?></td>
                  <td class="history__price"><?=htmlspecialchars(format_price($bet['price'])) ?></td>
                  <td class="history__time"><?=get_dt_range_back($bet['date_create']) ?></td>
                </tr>
              <?php endforeach;} ?>
            </table>
          </div>
        </div>
      </div>
    </section>
  </main>



