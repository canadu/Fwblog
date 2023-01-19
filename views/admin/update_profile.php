<?php $this->setLayoutVar('title', 'プロフィールの更新') ?>
<?php $this->setLayoutVar('errors', $errors) ?>
<section class="form-container">
  <form action="<?php echo $base_url; ?>/admin/update_profile" method="POST">
    <h3>プロフィールの更新</h3>
    <input type="hidden" name="_token" value="<?php echo $this->escape($_token); ?>" />
    <input type="text" name="name" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')" value="<?php echo $name; ?>" placeholder="管理者名を入力してください。">
    <input type="password" name="old_password" maxlength="20" autocomplete="new-password" placeholder="現在のパスワードを入力してください。" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
    <input type="password" name="new_password" maxlength="20" placeholder="新しいパスワードを入力してください。" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
    <input type="password" name="confirm_password" maxlength="20" placeholder="確認用に新しいパスワードを入力してください。" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
    <input type="submit" value="更新" name="submit" class="btn">
  </form>
</section>