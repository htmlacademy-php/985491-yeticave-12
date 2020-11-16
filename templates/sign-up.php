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
    <form class="form container <?php if($errors_validate): ?> form--invalid <?php endif; ?>" action="add_account.php" method="post" autocomplete="off"> 
      <h2>Регистрация нового аккаунта</h2>
      <div class="form__item <?php if($errors_validate['email']): ?> form__item--invalid <?php endif; ?>"> 
        <label for="email">E-mail <sup>*</sup></label>
        <input id="email" type="text" name="email" placeholder="Введите e-mail" value="<?=get_filtered_post_val('email'); ?>">
        <span class="form__error"><?=$errors_validate['email'] ?></span>
      </div>
      <div class="form__item <?php if($errors_validate['password']): ?> form__item--invalid <?php endif; ?>">
        <label for="password">Пароль <sup>*</sup></label>
        <input id="password" type="password" name="password" placeholder="Введите пароль" value="<?=get_filtered_post_val('password'); ?>">  
        <span class="form__error"><?=$errors_validate['password'] ?></span>
      </div>
      <div class="form__item <?php if($errors_validate['name']): ?> form__item--invalid <?php endif; ?>">
        <label for="name">Имя <sup>*</sup></label>
        <input id="name" type="text" name="name" placeholder="Введите имя" value="<?=get_filtered_post_val('name'); ?>">
        <span class="form__error"><?=$errors_validate['name'] ?></span>
      </div>
      <div class="form__item <?php if($errors_validate['message']): ?> form__item--invalid <?php endif; ?>">
        <label for="message">Контактные данные <sup>*</sup></label>
        <textarea id="message" name="message" placeholder="Напишите как с вами связаться"><?=get_filtered_post_val('message'); ?></textarea>
        <span class="form__error"><?=$errors_validate['message'] ?></span>
      </div>
      <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
      <button type="submit" name="submit" class="button">Зарегистрироваться</button>
      <a class="text-link" href="sign_in.php">Уже есть аккаунт</a>
    </form>
  </main>
