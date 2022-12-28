<?php $this->setLayoutVar('title', 'ユーザーを見る') ?>
<?php $this->setLayoutVar('errors', $errors) ?>

<section class="read-post">
  <?php
  if (($select_posts) || !empty($view_posts)) {
    foreach ($view_posts as $post_data) {
      $post_id = $post_data['post_id'];
  ?>
      <form method="post">
        <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
        <div class="status" style="background-color:<?php if ($post_data['status'] == 'active') {
                                                      echo '#FFC107';
                                                    } else {
                                                      echo '#6C757D';
                                                    }; ?>;"><?= $post_data['status'] == 'active' ? '公開' : '非公開'; ?></div>
        <?php if ($post_data['image'] != '') { ?>
          <img src="../uploaded_img/<?php echo $post_data['image']; ?>" class="image" alt="">
        <?php } ?>
        <div class="title"><?= $post_data['title']; ?></div>
        <div class="content"><?php echo $post_data['content']; ?></div>
        <div class="icons">
          <div class="likes"><i class="fas fa-heart"></i><span><?php echo $post_data['total_post_likes']['total']; ?></span></div>
          <div class="comments"><i class="fas fa-comments"></i><span><?php echo $post_data['total_post_comments']['total']; ?></span></div>
        </div>
        <div class="flex-btn">
          <a href="<?php echo $base_url; ?>/admin/edit_post/<?php echo $post_id; ?>" class="inline-option-btn">編集</a>
          <button type="submit" name="delete" class="inline-delete-btn" onclick="return confirm('この投稿を削除しますか？');">削除</button>
          <a href="<?php echo $base_url; ?>/admin/view_posts" class="inline-option-btn">戻る</a>
        </div>
      </form>
  <?php
    }
  } else {
    //投稿がない場合
    echo '<p class="empty">まだ投稿はありません。<a href=<?php echo $base_url; ?>/admin/add_posts" class="btn" style="margin-top:1.5rem;">記事を投稿する</a></p>';
  }
  ?>
</section>

<!-- 記事に投稿されたコメント -->
<section class="comments" style="padding-top:0;">
  <p class="comment-title">投稿コメント</p>
  <div class="box-container">
    <?php
    if (($select_comments) && !empty($select_comments)) {
      foreach ($comments as $comment) {
    ?>
        <div class="box">
          <div class="user">
            <i class="fas fa-user"> </i>
            <div class="user-info">
              <span><?php echo $comment['user_name']; ?></span>
              <div><?php echo $comment['date'] ?></div>
            </div>
          </div>
          <div class="text"><?php echo $comment['comment']; ?></div>
          <form action="" method="POST">
            <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
            <button type="submit" class="inline-delete-btn" name="delete_comment" onclick="return confirm('コメントを削除しますか?');">コメント削除</button>
          </form>
        </div>
    <?php
      }
    } else {
      echo '<p class="empty">まだコメントはありません</p>';
    }
    ?>
  </div>
</section>