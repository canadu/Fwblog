<?php $this->setLayoutVar('title', '管理者一覧') ?>
<?php $this->setLayoutVar('user', $user) ?>

<section class="authors">
  <h1 class="heading">管理者</h1>
  <div class="box-container">
    <?php
    if (isset($select_author)) {

      $idx = 0;

      foreach ($select_author as $author) {

        //記事を取得
        $total_admin_posts = $count_admin_posts[$idx]['total'];

        //いいねの件数を取得
        $total_admin_likes = $count_admin_likes[$idx]['total'];

        //コメントの件数を取得
        $total_admin_comments = $count_admin_comments[$idx]['total'];

    ?>
        <div class="box">
          <p>管理者：<span><?php echo $author['name']; ?></span></p>
          <p>総投稿数：<span><?php echo $total_admin_posts; ?></span></p>
          <p>いいね：<span><?php echo $total_admin_likes; ?></span></p>
          <p>コメント：<span><?php echo $total_admin_comments ?></span></p>
          <a href="<?php echo $base_url; ?>/user/author_posts/<?php echo $author['name']; ?>" class="btn">投稿を見る</a>
        </div>
    <?php
        $idx++;
      }
    } else {
      echo '<p class="empty">まだ投稿はありません。</p>';
    }
    ?>
  </div>
</section>