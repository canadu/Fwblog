<?php $this->setLayoutVar('title', 'ログイン') ?>
<?php $this->setLayoutVar('errors', $errors) ?>
<section class="form-container">
  <form action="<?php echo $base_url; ?>/user/user_login" method="POST">
    <h3>login</h3>
    <input type="hidden" name="_token" value="<?php echo $this->escape($_token); ?>" />
    <input type="email" name="email" maxlength="50" value="<?php echo $email; ?>" required placeholder="メールアドレスを入力して下さい。" class="box" oninput="this.value= this.replace(/\s/g,'')">
    <input type="password" name="password" maxlength="50" autocomplete="new-password" required placeholder="パスワードを入力して下さい。" class="box" oninput="this.value= this.replace(/\s/g,'')">
    <input type="submit" value="ログイン" name="submit" class="btn">
    <p>アカウント登録は<a href="<?php echo $base_url; ?>/user/user_register">こちら</a></p>
  </form>
</section>