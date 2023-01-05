<?php $this->setLayoutVar('title', 'ユーザーを見る') ?>
<?php $this->setLayoutVar('errors', $errors) ?>

<section class="show-posts">
  <h1 class="heading">投稿</h1>
  <form action="<?php echo $base_url; ?>/admin/search_page" method="POST" class="search-form">
    <input type="text" placeholder="検索" maxlength="100" name="search_box">
    <button class="fas fa-search" type="submit" name="search_btn"></button>
  </form>
  <div class="box-container">
    <?php
    if (!empty($view_posts)) {
      foreach ($view_posts as $view_post) {
    ?>
        <form method="post" class="box">
          <input type="hidden" name="post_id" value="<?php echo $view_post['post_id']; ?>">
          <?php if ($view_post['image'] != '') : ?>
            <img src="../uploaded_img/<?php echo $view_post['image']; ?>" class="image" alt="">
          <?php endif; ?>
          <div class="status" style="background-color:<?php if ($view_post['status'] == 'active') {
                                                        echo '#FFC107';
                                                      } else {
                                                        echo '#6C757D';
                                                      }; ?>;"><?= $view_post['status'] == 'active' ? '公開' : '非公開'; ?></div>
          <div class="title"><?= $view_post['title']; ?></div>
          <div class="posts-content"><?php echo $view_post['content']; ?></div>
          <div class="icons">
            <div class="likes"><i class="fas fa-heart"></i><span><?php echo $view_post['total_post_likes']['total']; ?></span></div>
            <div class="comments"><i class="fas fa-comments"></i><span><?php echo $view_post['total_post_comments']['total']; ?></span></div>
          </div>
          <div class="flex-btn">
            <a href="<?php echo $base_url; ?>/admin/edit_post/<?php echo $view_post['post_id']; ?>" class="option-btn">編集</a>
            <button type="submit" name="delete" class="delete-btn" onclick="return confirm('この投稿を削除しますか？');">削除</button>
          </div>
          <a href="<?php echo $base_url; ?>read_post/<?php echo $view_post['post_id']; ?>" class="btn">投稿を見る</a>
        </form>
      <?php
      }
    } else {
      ?>
      <p class="empty">まだ投稿はありません。<a href="<?php echo $base_url; ?>/admin/add_posts" class="btn" style="margin-top:1.5rem;">記事を投稿する</a></p>
    <?php } ?>
  </div>
</section>