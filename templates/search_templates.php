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
    <div class="container">
      <section class="lots">
        <h2>Результаты поиска по запросу «<span><?=get_filtered_get_val('search'); ?></span>»</h2>
        <h3><?php if(count($results_search) === 0): ?> Ничего не найдено по Вашему запросу <?php endif; ?></h3>
        <ul class="lots__list">
          <?php foreach ($results_search as $result_search): ?>
            <li class="lots__item lot">
              <div class="lot__image">
                <img src="<?=$result_search['url_image'] ?>" width="350" height="260" alt="<?=htmlspecialchars($result_search['name']) ?>">
              </div>
              <div class="lot__info">
                <span class="lot__category"><?=htmlspecialchars($result_search['name_category']) ?></span>
                <h3 class="lot__title"><a class="text-link" href="/lot.php?id=<?=$result_search['id'] ?>"><?=htmlspecialchars($result_search['name']) ?></a></h3>
                <div class="lot__state">
                  <div class="lot__rate">
                    <span class="lot__amount">Стартовая цена</span>
                    <span class="lot__cost"><?=htmlspecialchars(format_price($result_search['start_price'])) ?></span>
                  </div>
                  <?php $hours_and_minuts = get_dt_range($result_search['date_end']);?>
                  <div class="lot__timer timer">
                  <?php if ((int)$hours_and_minuts[1] > 0 || (int)$hours_and_minuts[0] > 0) : ?>
                    <?=implode(':', $hours_and_minuts);?>
                  <?php else: ?>
                    00:00
                  <?php endif; ?>
                  </div>
                </div>
              </div>
            </li>
          <?php endforeach; ?>           
        </ul>
      </section>
      <?php         
        $url_page = 'search.php?search=' . $_GET['search'] . '&find=' . $_GET['find'];
        if ((int)$_GET['page'] > 1) {
          $preview = (int)$_GET['page'] - 1;
        }
        else {
          $preview = (int)$_GET['page'];
        }
        
        $next = (int)$_GET['page'] + 1;
      ?>
      <?php if($number_lots_searched > $number_lots_on_page): ?> 
      <ul class="pagination-list">
          <li class="pagination-item pagination-item-prev"><a href="<?=$url_page . '&page=' . $preview ?>">Назад</a></li>          
        <?php for ($i = 1; $i <= $number_page; $i++): ?>                      
            <li class="pagination-item <?php if((int)$_GET['page'] === $i): ?> pagination-item-active <?php endif; ?>"><a href="<?=$url_page . '&page=' . $i ?>"><?=$i ?></a></li>
        <?php endfor; ?>           
          <li class="pagination-item pagination-item-next"><a href="<?=$url_page . '&page=' . $next ?>">Вперед</a></li>
      </ul>
      <?php endif; ?>
    </div>
  </main>