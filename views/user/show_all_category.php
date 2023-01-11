<?php $this->setLayoutVar('title', 'カテゴリ') ?>
<?php $this->setLayoutVar('errors', $errors) ?>
<?php $this->setLayoutVar('user', $user) ?>

<section class="categories">
  <h1 class="heading">投稿カテゴリー</h1>
  <div class="box-container">
    <?php $i = 1; ?>
    <?php foreach ($category as $key => $value) {
      $num = str_pad($i, 2, 0, STR_PAD_LEFT);
    ?>
      <div class="box"><span><?php echo $num; ?></span><a href="<?php echo $base_url; ?>/user/category/<?php echo $key; ?>"><?php echo $value; ?></a></div>
    <?php
      $i++;
    }
    ?>
  </div>
</section>