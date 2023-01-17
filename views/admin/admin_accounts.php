<?php $this->setLayoutVar('title', '管理者アカウント') ?>
<?php $this->setLayoutVar('errors', $errors) ?>

<section class="accounts">
  <h1 class="heading">管理者アカウント</h1>
  <div class="box-container">
    <div class="box" style="order:-2;">
      <p>新しい管理者を登録</p>
      <a href="<?php echo $base_url; ?>/admin/admin_register" class="option-btn" style="margin-bottom: .5rem;">登録</a>
    </div>
    <?php
    if ($admin_count == 0) {
      echo '<p class="empty">利用できるアカウントはありません。</p>';
    } else {
      foreach ($admin_posts as $post) {
    ?>
        <div class="box" style="order:<?php if ($post['id'] == $admin['id']) {
                                        echo '-1';
                                      } ?>;">
          <p>管理者ID:<span><?php echo $post['id']; ?></span></p>
          <p>ユーザー名:<span><?php echo $post['name']; ?></span></p>
          <p>総投稿数:<span><?php echo $post['total']; ?></span></p>
          <?php
          if ($post['id'] == $admin['id']) :
          ?>
            <form action="" method="POST">
              <div class="flex-btn">
                <input type="hidden" name="_token" value="<?php echo $this->escape($_token); ?>" />
                <a href="<?php echo $base_url; ?>/admin/update_profile" class="option-btn" style="margin-bottom: .5rem;">更新</a>
                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>" on>
                <?php if ($admin_count > 1) : ?>
                  <!-- 管理者アカウントが一人しかいない場合は削除出来ないようにする -->
                  <button type="submit" name="delete" onclick="return confirm('アカウントを削除しますか？');" class="delete-btn" style="margin-bottom:.5rem;">削除</button>
                <?php endif; ?>
              </div>
            </form>
          <?php endif; ?>
        </div>
    <?php
      }
    }
    ?>
  </div>
</section>