<?php $this->setLayoutVar('title', '検索') ?>
<?php $this->setLayoutVar('errors', $errors) ?>
<?php $this->setLayoutVar('user', $user) ?>

<section class="posts-container">
  <div class="box-container">
    <?php
    if (count($search_posts) > 0) {
      foreach ($search_posts as $post) {
        $post_id = $post['id'];
    ?>
        <form method="post" class="box">
          <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
          <input type="hidden" name="admin_id" value="<?php echo $post['admin_id']; ?>">
          <!-- 抽出条件を格納しておく -->
          <input type="hidden" name="search_box" value="<?php echo $search_box; ?>">

          <div class="post-admin">
            <i class="fas fa-user"></i>
            <div>
              <a href="<?php echo $base_url; ?>/user/author_posts/<?php echo $post['name']; ?>"><?php echo $post['name']; ?></a>
              <div><?php echo $post['date']; ?></div>
            </div>
          </div>

          <?php if ($post['image'] != '') : ?>
            <img src="../../uploaded_img/<?php echo $post['image']; ?>" class="post-image" alt="">
          <?php endif; ?>

          <div class="post-title"><?php echo $post['title']; ?></div>
          <div class="post-content content-150"><?php echo $post['content']; ?></div>
          <a href="<?php echo $base_url; ?>/user/view_post/<?php echo $post['id']; ?>" class="inline-btn">もっと見る</a>
          <div class="icons">
            <a href="<?php echo $base_url; ?>/user/view_post/<?php echo $post_id; ?>"><i class="fas fa-comment"></i><span>(<?= $post['count_post_comment']; ?>)</span></a>
            <button type="submit" name="like_post"><i class="fas fa-heart" style="<?php if ($post['confirm_like']  > 0 and $user['id'] != '') {
                                                                                    echo 'color:red;';
                                                                                  }; ?>"></i><span>(<?= $post['count_post_like']; ?>)</span></button>
          </div>
        </form>
    <?php
      }
    } else {
      echo '<p class="empty">まだ投稿はありません。</p>';
    }
    ?>
  </div>
</section>