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
    <form class="form container <?php if($errors_validate): ?> form--invalid <?php endif; ?>" action="sign_in.php" method="post"> 
      <h2>Вход</h2>
      <div class="form__item <?php if($errors_validate['email']): ?> form__item--invalid <?php endif; ?>"> 
        <label for="email">E-mail <sup>*</sup></label>
        <input id="email" type="text" name="email" placeholder="Введите e-mail" value="<?=get_filtered_post_val('email'); ?>">
        <span class="form__error"><?=$errors_validate['email'] ?></span>
      </div>
      <div class="form__item form__item--last <?php if($errors_validate['password']): ?> form__item--invalid <?php endif; ?>">
        <label for="password">Пароль <sup>*</sup></label>
        <input id="password" type="password" name="password" placeholder="Введите пароль" value="<?=get_filtered_post_val('password'); ?>">
        <span class="form__error"><?=$errors_validate['password'] ?></span>
      </div>
      <button type="submit" name="submit" class="button">Войти</button>
    </form>
  </main>
