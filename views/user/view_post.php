<?php $this->setLayoutVar('title', '投稿を見る') ?>
<?php $this->setLayoutVar('errors', $errors) ?>
<?php $this->setLayoutVar('user', $user) ?>

<!-- 編集ボタンをクリックした際に表示されるブロック -->
<?php if (count($select_edit_comment) > 0) : ?>
  <section class="comment-edit-form">
    <p>あなたのコメントを編集してください</p>
    <form action="<?php echo $base_url; ?>/user/view_post/<?php echo $post_id; ?>" method="POST">
      <input type="hidden" name="_token" value="<?php echo $this->escape($_token); ?>" />
      <input type="hidden" name="edit_comment_id" value="<?php echo $select_edit_comment['id']; ?>">
      <textarea name="comment_edit_box" required cols="30" rows="10" placeholder="コメントを入力してください。"><?php echo $select_edit_comment['comment']; ?></textarea>
      <button type=="submit" class="inline-btn" name="edit_comment">コメントの編集</button>
      <div class="inline-option-btn" onclick="window.location.href = '<?php echo $base_url; ?>/user/view_post/<?php echo $post_id; ?>';">キャンセル</div>
    </form>
  </section>
<?php endif; ?>

<section class=" posts-container" style="padding-bottom:0;">
  <div class="box-container">
    <?php
    if (count($select_posts) > 0) {
      foreach ($select_posts as $post) {
    ?>
        <form method="POST" class="box">
          <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
          <input type="hidden" name="admin_id" value="<?php echo $post['admin_id']; ?>">
          <input type="hidden" name="_token" value="<?php echo $this->escape($_token); ?>" />

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
          <div class="post-content"><?php echo $post['content']; ?></div>
          <div class="icons">
            <div><i class="fas fa-comment"></i><span>(<?= $post['count_post_comment']; ?>)</span></div>
            <button type="submit" name="like_post"><i class="fas fa-heart" style="<?php if ($post['confirm_like'] > 0 and $user['id'] != '') {
                                                                                    echo 'color:red;';
                                                                                  }; ?>"></i><span>(<?= $post['count_post_like']; ?>)</span></button>
          </div>
        </form>
    <?php
      }
    } else {
      echo '<p class="empty">この投稿はありません。</p>';
    }
    ?>
  </div>
</section>
<?php if (count($select_posts) > 0) : ?>
  <section class="comments-container">
    <?php if (isset($user['id'])) : ?>
      <p class="comment-title">コメントを追加</p>
      <form action="<?php echo $base_url; ?>/user/view_post/<?php echo $post_id; ?>" method="POST" class="add-comment">
        <input type="hidden" name="_token" value="<?php echo $this->escape($_token); ?>" />
        <input type="hidden" name="admin_id" value="<?php echo $select_posts[0]['admin_id']; ?>">
        <input type="hidden" name="user_name" value="<?php echo $user['name']; ?>">
        <p class="user"><i class="fas fa-user"></i><a href="<?php echo $base_url; ?>/user/user_register"><?php echo $user['name']; ?></a></p>
        <textarea name="comment" maxlength="1000" class="comment-box" cols="30" rows="10" placeholder="コメントを入力してください。" required></textarea>
        <input type="submit" value="コメントを追加" class="inline-btn" name="add_comment">
      </form>
    <?php else : ?>
      <div class="add-comment">
        <p>追加、編集するにはログインしてください</p>
        <a href="<?php echo $base_url; ?>/user/user_login" class="inline-btn">ログイン</a>
      </div>
    <?php endif; ?>
    <p class="comment-title">投稿されたコメント</p>
    <div class="user-comments-container">
      <?php if (count($select_comments) > 0) : ?>
        <?php foreach ($select_comments as $comment) : ?>
          <div class="show-comments" style="<?php if ($comment['user_id'] == $user['id']) {
                                              echo 'order:-1;';
                                            } ?>">
            <div class="comment-user">
              <i class="fas fa-user"></i>
              <div>
                <span><?php echo $comment['user_name']; ?></span>
                <div><?php echo $comment['date']; ?></div>
              </div>
            </div>
            <div class="comment-box" style="<?php if ($comment['user_id'] == $user['id']) {
                                              echo 'color:var(--white); background:var(--black);';
                                            } ?>"><?= $comment['comment']; ?></div>
            <?php if ($comment['user_id'] == $user['id']) : ?>
              <form action="<?php echo $base_url; ?>/user/view_post/<?php echo $post_id; ?>" method="POST">
                <input type="hidden" name="_token" value="<?php echo $this->escape($_token); ?>" />
                <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                <button type="submit" class="inline-option-btn" name="open_edit_box">コメント編集</button>
                <button type="submit" class="inline-delete-btn" name="delete_comment" onclick="return confirm('削除しますか?');">コメント削除</button>
              </form>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      <?php else : ?>
        <?php echo '<p class="empty">まだコメントはまだありません。</p>'; ?>
      <?php endif; ?>
    </div>
  </section>
<?php endif; ?>