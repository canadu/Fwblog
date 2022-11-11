<?php $this->setLayoutVar('title', 'ダッシュボード') ?>
<?php $this->setLayoutVar('errors', $errors) ?>

<?php $disp_category_count = 0; ?>

<section class="home-grid">
  <div class="box-container">
    <div class="box">

      <?php if (!empty($user)) : ?>
        <!-- レイアウトのファイルに変数を渡す -->
        <?php $this->setLayoutVar('user', $user) ?>

        <p>Welcome <span><?php echo $user['name']; ?></span></p>
        <p>総コメント : <span><?php echo count($comments); ?></span></p>
        <p>総いいね : <span><?php echo count($likes); ?></span></p>
        <a href="<?php echo $base_url; ?>/user/update_user_info" class="btn">プロフィールを更新</a>
        <div class="flex-btn">
          <a href="<?php echo $base_url; ?>/user/user_likes" class="option-btn">いいね</a>
          <a href="<?php echo $base_url; ?>/user/user_comments" class="option-btn">コメント</a>
        </div>
      <?php else : ?>
        <p class="name">ログイン or 登録</p>
        <div class="flex-btn">
          <a href="<?php echo $base_url; ?>/user/sign_in" class="option-btn">ログイン</a>
          <a href="<?php echo $base_url; ?>/user/sign_up" class="option-btn">登録</a>
        </div>
      <?php endif; ?>
    </div>

    <!-- カテゴリを取得 -->
    <div class="box">
      <p>カテゴリー</p>
      <div class="flex-box">
        <?php foreach ($category as $key => $value) : ?>
          <?php if ($disp_category_count < 5) : ?>
            <a href="<?php echo $base_url; ?>/user/category/<?php echo $key; ?>" class="links"><?php echo $value; ?></a>
          <?php endif; ?>
          <?php $disp_category_count++; ?>
        <?php endforeach; ?>
        <a href="<?php echo $base_url; ?>/user/show_all_category" class="btn">全て見る</a>
      </div>
    </div>

    <!-- 管理者アカウントを取得 -->
    <div class="box">
      <p>管理者</p>
      <div class="flex-box">
        <?php if (count($$select_authors) > 0) : ?>
          <?php foreach ($select_authors as $author) : ?>
            <a href="<?php echo $base_url; ?>/user/author_posts/<?php echo $author['name']; ?>" class="links"><?php echo $author['name']; ?></a>
          <?php endforeach; ?>
        <?php else : ?>
          <?php echo '<p class="empty">まだ投稿はありません。</p>'; ?>
        <?php endif; ?>
        <a href="<?php echo $base_url; ?>/user/authors" class="btn">全て見る</a>
      </div>
    </div>

  </div>
  <section>