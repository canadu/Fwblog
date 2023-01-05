<?php $this->setLayoutVar('title', 'ユーザーアカウント') ?>
<?php $this->setLayoutVar('errors', $errors) ?>

<section class="accounts">
  <h1 class="heading">ユーザーアカウント</h1>
  <div class="box-container">
    <?php
    if ($select_account->rowCount() > 0 || !empty($users_list)) {
      foreach ($users_list as $user) {
    ?>
        <div class="box">
          <p>ユーザーID : <span><?php echo $user['user_id']; ?></span></p>
          <p>ユーザー名 : <span><?php echo $user['name']; ?></span></p>
          <p>総コメント数 : <span><?php echo $user['total_user_comments']; ?></span></p>
          <p>総いいね数 : <span><?php echo $user['total_user_likes']; ?></span></p>
        </div>
    <?php
      }
    } else {
      echo '<p class="empty">利用ユーザーは居ません';
    }
    ?>
  </div>
</section>