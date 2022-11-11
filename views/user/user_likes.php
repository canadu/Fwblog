<?php $this->setLayoutVar('title', 'いいねした投稿記事') ?>
<?php $this->setLayoutVar('errors', $errors) ?>

<section class="posts-container">
  <h1 class="heading">いいねした投稿記事</h1>
  <div class="box-container">
    <?php
    if (isset($select_posts) && !empty($select_posts)) {

      $idx = 0;

      foreach ($select_posts as $select_post) {

    ?>
        <form method="POST" class="box">
          <input type="hidden" name="post_id" value="<?php echo $select_post['id']; ?>">
          <input type="hidden" name="admin_id" value="<?php echo $select_post['admin_id']; ?>">
          <div class="post-admin">
            <i class="fas fa-user"></i>
            <div>
              <a href="<?php echo $base_url; ?>/user/author_posts/<?php echo $select_post['name']; ?>"><?php echo $select_post['name']; ?></a>
              <div><?php echo $select_post['date']; ?></div>
            </div>
          </div>
          <?php if ($select_post['image'] != '') : ?>
            <img src="uploaded_img/<?php echo $select_post['image']; ?>" class="post-image" alt="">
          <?php endif; ?>
          <div class="post-title"><?php echo $select_post['title']; ?></div>
          <div class="post-content content-150"><?php echo $select_post['content']; ?></div>
          <a href="<?php echo $base_url; ?>/user/view_post/<?php echo $select_post['id']; ?>" class="inline-btn">もっと見る</a>
          <div class="icons">
            <a href="<?php echo $base_url; ?>/user/view_post/<?php echo $select_post['id']; ?>"><i class="fas fa-comment"></i><span>(<?php echo $select_post['total_post_comments']; ?>)</span></a>
            <button type="submit" name="like_post"><i class="fas fa-heart" style="<?php if ($select_post['total_post_likes'] > 0 and $select_post['id'] != '') {
                                                                                    echo 'color:red;';
                                                                                  }; ?>"></i><span>(<?= $select_post['total_post_likes']; ?>)</span></button>
          </div>
        </form>
    <?php
        $idx++;
      }
    } else {
      echo '<p class="empty">いいね！を押した投稿はありません</p>';
    }
    ?>
  </div>
</section>