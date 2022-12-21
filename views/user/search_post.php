<?php $this->setLayoutVar('title', '検索') ?>
<?php $this->setLayoutVar('errors', $errors) ?>
<?php if (!empty($select_posts)) {
?>
  <section class="posts-container">
    <div class="box-container">
      <?php
      $idx = 0;
      foreach ($select_posts as $post) {

        $post_id = $post['id'];

        //各投稿毎のいいねの件数を取得
        $total_post_like = $count_post_likes[$idx]['total'];

        //各投稿毎のコメントの件数を取得
        $total_post_comment = $count_post_comments[$idx]['total'];

        //ユーザーのいいねを取得
        $confirm_like = $confirm_likes[$idx]['total'];

      ?>
        <form method="post" class="box">
          <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
          <input type="hidden" name="admin_id" value="<?php echo $post['admin_id']; ?>">
          <div class="post-admin">
            <i class="fas fa-user"></i>
            <div>
              <a href="<?php echo $base_url; ?>/user/author_posts/<?php echo $post['name']; ?>"><?php echo $post['name']; ?></a>
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
            <a href="<?php echo $base_url; ?>/user/view_post/<?php echo $post['id']; ?>"><i class="fas fa-comment"></i><span>(<?php echo $total_post_like; ?>)</span></a>
            <button type="submit" name="like_post"><i class="fas fa-heart" style="<?php if (isset($confirm_like) && (int)$confirm_like['total'] > 0) {
                                                                                    echo 'color:var(--red);';
                                                                                  } ?>  "></i><span>(<?= $total_post_like; ?>)</span></button>
          </div>
        </form>
      <?php
        $idx++;
      }
      ?>
    </div>
  </section>
<?php
} else {
  echo '<section><p class="empty">該当する投稿はありません</p></section>';
}
?>