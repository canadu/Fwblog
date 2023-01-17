<?php $this->setLayoutVar('title', '管理者一覧') ?>
<?php $this->setLayoutVar('user', $user) ?>

<section class="authors">
  <h1 class="heading">管理者</h1>
  <div class="box-container">
    <?php
    if (count($all_authors) > 0) {
      foreach ($all_authors as $author) {
    ?>
        <div class="box">
          <p>管理者：<span><?php echo $author['name']; ?></span></p>
          <p>総投稿数：<span><?php echo  $author['count_author_post']; ?></span></p>
          <p>いいね：<span><?php echo  $author['count_author_like']; ?></span></p>
          <p>コメント：<span><?php echo  $author['count_author_comment'] ?></span></p>
          <a href="<?php echo $base_url; ?>/user/author_posts/<?php echo $author['name']; ?>" class="btn">投稿を見る</a>
        </div>
    <?php
      }
    } else {
      echo '<p class="empty">まだ投稿はありません。</p>';
    }
    ?>
  </div>
</section>