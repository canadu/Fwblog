<?php $this->setLayoutVar('title', 'プロフィールの更新') ?>
<section class="form-container">
  <form action="" method="POST">
    <h3>プロフィールの更新</h3>
    <input type="hidden" name="_token" value="<?php echo $this->escape($_token); ?>" />
    <input type="text" name="name" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')" placeholder="<?php echo $fetch_profile['name']; ?>">
    <input type="password" name="old_password" maxlength="20" placeholder="現在のパスワードを入力してください。" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
    <input type="password" name="new_password" maxlength="20" placeholder="新しいパスワードを入力してください。" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
    <input type="password" name="confirm_password" maxlength="20" placeholder="確認用に新しいパスワードを入力してください。" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
    <input type="submit" value="更新" name="submit" class="btn">
  </form>
</section>