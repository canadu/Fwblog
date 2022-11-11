<?php $this->setLayoutVar('title', '管理者投稿一覧') ?>
<section class="posts-container">
  <div class="box-container">
    <?php
    if (isset($select_posts)) {

      $idx = 0;

      foreach ($select_posts as $post) {

        //記事を取得
        $total_post_likes = $count_post_likes[$idx]['total'];

        //いいねの件数を取得
        $total_post_comments = $count_post_comments[$idx]['total'];

        //コメントの件数を取得
        $confirm_like = $confirm_likes[$idx];

    ?>
        <form action="post" class="box">
          <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
          <input type="hidden" name="admin_id" value="<?php echo $post['admin_id']; ?>">

          <div class="post-admin">
            <i class="fas fa-user"></i>
            <div>
              <a href="author_posts.php?author=<?php echo $post['name']; ?>"><?php echo $post['name']; ?></a>
              <div><?php echo $post['date']; ?></div>
            </div>
          </div>

          <?php if ($post['image'] != '') : ?>
            <img src="uploaded_img/<?php echo $post['image']; ?>" class="post-image" alt="">
          <?php endif; ?>

          <div class="post-title"><?php echo $post['title']; ?></div>
          <div class="post-content content-150"><?php echo $post['content']; ?></div>
          <a href="<?php echo $base_url; ?>/user/view_post/<?php echo $post['id']; ?>" class="inline-btn">もっと見る</a>
          <div class="icons">
            <a href="<?php echo $base_url; ?>/user/view_post/<?php echo $post['id']; ?>"><i class="fas fa-comment"></i><span>(<?php echo $total_post_comments; ?>)</span></a>
            <button type="submit" name="like_post"><i class="fas fa-heart" style="<?php if (isset($confirm_like)) {
                                                                                    echo 'color:var(--red);';
                                                                                  } ?>  "></i><span>(<?= $total_post_likes; ?>)</span></button>
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
</section>