<?php $this->setLayoutVar('title', '管理者登録') ?>
<?php $this->setLayoutVar('errors', $errors) ?>
<section class="form-container">
  <form action="" method="POST">
    <h3>管理者登録</h3>
    <input type="hidden" name="_token" value="<?php echo $this->escape($_token); ?>" />
    <input type="text" name="name" maxlength="20" required placeholder="ユーザー名を入力して下さい。" class="box" oninput="this.value= this.replace(/\s/g,'')">
    <input type="password" name="password" maxlength="20" required placeholder="パスワードを入力して下さい。" class="box" oninput="this.value= this.replace(/\s/g,'')">
    <input type="password" name="confirm_password" maxlength="20" required placeholder="確認用にもう一度パスワードを入力して下さい。" class="box" oninput="this.value= this.replace(/\s/g,'')">
    <input type="submit" value="登録" name="submit" class="btn">
  </form>
</section>