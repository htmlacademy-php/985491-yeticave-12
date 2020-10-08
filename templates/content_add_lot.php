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
    <form class="form form--add-lot container <?php if($errors_validate): ?> form--invalid <?php endif; ?> " action="add.php" method="post" enctype="multipart/form-data"> <!-- form--invalid -->
      <h2>Добавление лота</h2>
      <div class="form__container-two">
        <div class="form__item <?php if($errors_validate['lot-name']): ?> form__item--invalid <?php endif; ?> "> <!-- form__item--invalid -->
          <label for="lot-name">Наименование <sup>*</sup></label>
          <input id="lot-name" type="text" name="lot-name" placeholder="Введите наименование лота" value="<?=getPostVal('lot-name'); ?>">
          <span class="form__error">Введите наименование лота</span>
        </div>
        <div class="form__item <?php if($errors_validate['category']): ?> form__item--invalid <?php endif; ?>">
          <label for="category">Категория <sup>*</sup></label>
          <select id="category" name="category" value="<?=getPostVal('category'); ?>">
            <option>Выберите категорию</option>
            <?php foreach ($categories as $category): ?>
              <option><?=htmlspecialchars($category['name']) ?></option>              
            <?php endforeach; ?>                
          </select>
          <span class="form__error">Выберите категорию</span>
        </div>
      </div>
      <div class="form__item form__item--wide <?php if($errors_validate['message']): ?> form__item--invalid <?php endif; ?>">
        <label for="message">Описание <sup>*</sup></label>
        <textarea id="message" name="message" placeholder="Напишите описание лота"><?=getPostVal('message'); ?></textarea>
        <span class="form__error">Напишите описание лота</span>
      </div>
      <div class="form__item form__item--file <?php if($errors_validate['file_img_lot'] || $errors_validate['file_img_lot_mime']): ?> form__item--invalid <?php endif; ?>">
        <label>Изображение <sup>*</sup></label>
        <div class="form__input-file">
          <input class="visually-hidden" type="file" id="lot-img" value="" name="file_img_lot">
          <label for="lot-img">
            Добавить
          </label>
          <span class="form__error"><?php if($errors_validate['file_img_lot']): ?> Добавьте фото товара <?php endif; ?> <?php if($errors_validate['file_img_lot_mime']): ?> Допустимы только файлы изображений типов jpeg и png <?php endif; ?></span>
        </div>
      </div>
      <div class="form__container-three">
        <div class="form__item form__item--small <?php if($errors_validate['lot-rate']): ?> form__item--invalid <?php endif; ?>">
          <label for="lot-rate">Начальная цена <sup>*</sup></label>
          <input id="lot-rate" type="text" name="lot-rate" placeholder="0" value="<?=getPostVal('lot-rate'); ?>">
          <span class="form__error">Введите начальную цену</span>
        </div>
        <div class="form__item form__item--small <?php if($errors_validate['lot-step'] || $errors_validate['lot-step_format']): ?> form__item--invalid <?php endif; ?>">
          <label for="lot-step">Шаг ставки <sup>*</sup></label>
          <input id="lot-step" type="text" name="lot-step" placeholder="0" value="<?=getPostVal('lot-step'); ?>" >
          <span class="form__error"><?php if($errors_validate['lot-step']): ?> Введите шаг ставки <?php endif; ?> <?php if($errors_validate['lot-step_format']): ?> Шаг ставки должен быть целым числом больше нуля <?php endif; ?></span>
        </div>
        <div class="form__item <?php if($errors_validate['lot-date'] || $errors_validate['lot-date_ranshe'] || $errors_validate['lot-date_format']): ?> form__item--invalid <?php endif; ?>">
          <label for="lot-date">Дата окончания торгов <sup>*</sup></label>
          <input class="form__input-date" id="lot-date" type="text" name="lot-date" placeholder="Введите дату в формате ГГГГ-ММ-ДД" value="<?=getPostVal('lot-date'); ?>" >
          <span class="form__error"><?php if($errors_validate['lot-date']): ?> Введите дату завершения торгов <?php endif; ?> <?php if($errors_validate['lot-date_ranshe']): ?> Дата окончания торгов должна быть позже текущего времени минимум на 24 часа <?php endif; ?><?php if($errors_validate['lot-date_format']): ?> Дата должна быть введена в формате "ГГГГ-ММ_ДД <?php endif; ?></span>
        </div>
      </div>
      <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
      <button type="submit" class="button">Добавить лот</button>
    </form>
  </main>





