<?php $this->setLayoutVar('title', 'ダッシュボード') ?>

<section class="dashboard">
  <h1 class="heading">dashboard</h1>
  <div class="box-container">
    <div class="box">
      <h3>welcome</h3>
      <p><?php echo $admin['name']; ?></p>
      <a href="<?php echo $base_url; ?>/admin/update_profile" class="btn">プロフィールの更新</a>
    </div>

    <!-- 投稿総数  -->
    <div class="box">
      <h3><?php echo $number_of_posts; ?></h3>
      <p>投稿総数</p>
      <a href="<?php echo $base_url; ?>/admin/add_posts" class="btn">新規に投稿</a>
    </div>

    <!-- 公開されている投稿  -->
    <div class="box">
      <h3><?php echo $number_of_active_posts; ?></h3>
      <p>公開件数</p>
      <a href="<?php echo $base_url; ?>/admin/view_post/active" class="btn">公開されている投稿</a>
    </div>

    <!-- 非公開な投稿  -->
    <div class="box">
      <h3><?php echo $number_of_deactive_posts; ?></h3>
      <p>非公開件数</p>
      <a href="<?php echo $base_url; ?>/admin/view_post/deactive" class="btn">非公開の投稿</a>
    </div>

    <!-- ユーザーアカウント -->
    <div class="box">
      <h3><?php echo $number_of_users; ?></h3>
      <p>ユーザーアカウント</p>
      <a href="users_accounts.php" class="btn">ユーザーを確認</a>
    </div>

    <!-- 管理者アカウント  -->
    <div class="box">
      <h3><?php echo $number_of_admins; ?></h3>
      <p>管理者アカウント</p>
      <a href="admin_accounts.php" class="btn">管理者を確認</a>
    </div>

    <!-- コメント  -->
    <div class="box">
      <h3><?php echo $number_of_comments; ?></h3>
      <p>総コメント数</p>
      <a href="comments.php" class="btn">コメントを確認</a>
    </div>
    <!-- いいね  -->
    <div class="box">
      <h3><?php echo $number_of_likes; ?></h3>
      <p>総いいね数</p>
      <a href="view_posts.php" class="btn">投稿を確認</a>
    </div>
  </div>
</section>