<?php $this->setLayoutVar('title', 'ダッシュボード') ?>
<?php $this->setLayoutVar('errors', $errors) ?>
<?php $this->setLayoutVar('user', $user) ?>

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
  <section class="posts-container">
    <h1 class="heading">最近の投稿</h1>
    <div class="box-container">
      <?php
      if (count($select_posts) > 0) {

        $idx = 0;

        foreach ($select_posts as $post) {

          //記事が公開されている場合
          $post_id = $post['id'];

          //各投稿毎のいいねの件数を取得
          $total_post_likes = count($count_post_likes[$idx]);

          //各投稿毎のコメントの件数を取得
          $total_post_comments = count($count_post_comments[$idx]);

      ?>
          <form method="post" class="box">
            <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
            <input type="hidden" name="admin_id" value="<?php echo $post['admin_id']; ?>">

            <div class="post-admin">
              <i class="fas fa-user"></i>
              <div>
                <a href="author_posts.php?author=<?php echo $post['name']; ?>"><?php echo $post['name']; ?></a>
                <div><?php echo $post['date']; ?></div>
              </div>
            </div>

            <?php if ($post['image'] != '') : ?>
              <img src="../../uploaded_img/<?php echo $post['image']; ?>" class="post-image" alt="">
            <?php endif; ?>

            <div class="post-title"><?php echo $post['title']; ?></div>
            <div class="post-content content-150"><?php echo $post['content']; ?></div>
            <a href="<?php echo $base_url; ?>/user/view_post/<?php echo $post_id; ?>" class="inline-btn">もっと見る</a>
            <a href="<?php echo $base_url; ?>/user/category/<?php echo $post['category']; ?>" class="post-cat"> <i class="fas fa-tag"></i> <span><?= $category[$post['category']]; ?></span></a>
            <div class="icons">
              <a href="<?php echo $base_url; ?>/user/view_post/<?php echo $post_id; ?>"><i class="fas fa-comment"></i><span>(<?php echo $total_post_comments; ?>)</span></a>
              <button type="submit" name="like_post"><i class="fas fa-heart" style="<?php if ($total_post_likes > 0 and $user['id'] != '') {
                                                                                      echo 'color:red;';
                                                                                    }; ?>"></i><span>(<?= $total_post_likes; ?>)</span></button>
            </div>
          </form>
      <?php
          $idx++;
        }
      } else {
        echo '<p class="empty">まだ投稿はありません。</p>';
      }
      ?>
    </div>
    <div class="more-btn" style="text-align: center; margin-top:1rem;">
      <a href="<?php echo $base_url; ?>/user/posts" class="inline-btn">すべての投稿を見る</a>
    </div>
  </section>