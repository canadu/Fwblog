<?php $this->setLayoutVar('title', 'ユーザー登録') ?>
<?php $this->setLayoutVar('errors', $errors) ?>
<section class="form-container">
  <form action="<?php echo $base_url; ?>/user/user_register" method="POST">
    <h3>ユーザー登録</h3>
    <input type="hidden" name="_token" value="<?php echo $this->escape($_token); ?>" />
    <input type="text" name="name" maxlength="20" required value="<?php echo $name; ?>" placeholder="ユーザー名を入力して下さい。" class="box" oninput="this.value= this.replace(/\s/g,'')">
    <input type="email" name="email" maxlength="50" required value="<?php echo $email; ?>" placeholder="メールアドレスを入力して下さい。" class="box" oninput="this.value= this.replace(/\s/g,'')">
    <input type="password" name="password" maxlength="50" autocomplete="new-password" required placeholder="パスワードを入力して下さい。" class="box" oninput="this.value= this.replace(/\s/g,'')">
    <input type="password" name="confirm_password" maxlength="50" required placeholder="確認用にもう一度パスワードを入力して下さい。" class="box" oninput="this.value= this.replace(/\s/g,'')">
    <input type="submit" value="登録" name="submit" class="btn">
    <p>ログインは<a href="<?php echo $base_url; ?>/user/user_login">こちら</a></p>
  </form>
</section>