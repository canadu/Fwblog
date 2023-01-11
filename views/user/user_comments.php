<?php
$this->setLayoutVar('title', 'コメントした投稿記事');
$this->setLayoutVar('errors', $errors);

?>

<?php if (count($select_edit_comment) > 0) : ?>
  <section class="comment-edit-form">
    <p>コメントの編集</p>
    <div class="box-container">
      <form action="" method="POST">
        <input type="hidden" name="edit_comment_id" value="<?php echo $select_edit_comment['id']; ?>">
        <textarea name="comment_edit_box" required id="" cols="30" rows="10" placeholder="コメントを入力してください"><?php echo $select_edit_comment['comment']; ?></textarea>
        <button type="submit" class="inline-btn" name="edit_comment">編集</button>
        <div class="inline-option-btn" onclick="window.location.href='<?php echo $base_url; ?>/user/user_comments';">キャンセル</div>
      </form>
    </div>
  </section>
<?php endif; ?>

<section class="comments-container">
  <h1 class="heading">コメント</h1>
  <p class="comment-title">投稿に対するコメント</p>
  <div class="user-comments-container">
    <?php
    if (isset($select_comments)) {
      foreach ($select_comments as $comment) {
    ?>
        <div class="show-comments">
          <?php
          foreach ($select_posts as $post) {
            if ($comment['post_id'] === $post['id']) {
          ?>
              <div class="post-title">from: <span><?php echo $post['title']; ?></span><a href="<?php echo $base_url; ?>/user/view_post/<?php echo $post['id']; ?>"> 投稿を見る</a></div>
              <div class="comment-box"><?php echo $comment['comment']; ?></div>
              <form action="" method="POST">
                <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                <button type="submit" class="inline-option-btn" name="open_edit_box">コメントを編集</button>
                <button type="submit" class="inline-delete-btn" name="delete_comment" onclick="return confirm('コメントを削除しますか？');">コメントを削除</button>
              </form>
          <?php
              break;
            }
          } ?>
        </div>
    <?php
      }
    } else {
      echo '<p class="empty>コメントはまだありません</p>';
    }
    ?>
  </div>
</section>