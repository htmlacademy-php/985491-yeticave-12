

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
        
        </div>
      </div>
    </section>
  </main>



