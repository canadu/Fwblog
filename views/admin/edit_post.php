<?php $this->setLayoutVar('title', '投稿記事の修正') ?>
<?php $this->setLayoutVar('errors', $errors) ?>

<section class="post-editor">
  <h1 class="heading">投稿記事の修正</h1>
  <?php
  if (count($select_posts) > 0) :
  ?>
    <form action="" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="old_image" value="<?php echo $select_posts['image']; ?>">
      <input type="hidden" name="post_id" value="<?php echo $select_posts['id']; ?>">
      <input type="hidden" name="_token" value="<?php echo $this->escape($_token); ?>" />

      <p>投稿ステータス <span>*</span></p>
      <select name="status" class="box" required>
        <option value="<?php echo $select_posts['status']; ?>" selected><?php echo $select_posts['status'] == 'active' ? '公開' : '非公開'; ?></option>
        <option value="active">公開</option>
        <option value="deactive">非公開</option>
      </select>

      <p>投稿タイトル<span>*</span></p>
      <input type="text" name="title" maxlength="100" required placeholder="投稿タイトルを入力してください。" class="box" value="<?php echo $select_posts['title']; ?>">

      <p>投稿記事<span>*</span></p>
      <textarea name="content" class="box" required maxlength="10000" placeholder="記事を入力してください。" cols="30" rows="10"><?php echo $select_posts['content']; ?></textarea>

      <p>投稿カテゴリ<span>*</span></p>
      <select name="category" class="box" required>
        <option value="<?php echo $select_posts['category']; ?>" selected><?php echo $category[$select_posts['category']]; ?></option>
        <?php foreach ($category as $key => $value) { ?>
          <?php if ($key != $select_posts['category']) : ?>
            <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
          <?php endif; ?>
        <?php } ?>
      </select>

      <p>投稿画像</p>
      <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png, image/webp">
      <?php if ($select_posts['image'] != '') : ?>
        <img src="../../uploaded_img/<?php echo $select_posts['image']; ?>" class="image" alt="">
        <button type="submit" name="delete_image" class="inline-delete-btn" onclick="return confirm('画像を削除しますか？');">画像削除</button>
      <?php endif; ?>

      <div class="flex-btn">
        <input type="submit" value="編集" name="save" class="btn">
        <a href="<?php echo $base_url; ?>/admin/view_posts" class="option-btn">戻る</a>
        <button type="submit" name="delete_post" class="delete-btn" onclick="return confirm('この投稿を削除しますか？');">投稿を削除</button>
      </div>
    </form>
  <?php else : ?>
    <?php echo '<p class="empty">投稿がありません。</p>'; ?>
    <div class="flex-btn">
      <a href="<?php echo $base_url; ?>/admin/view_posts" class="option-btn">投稿を見る</a>
      <a href="<?php echo $base_url; ?>/admin/add_posts" class="option-btn">投稿する</a>
    </div>
  <?php endif; ?>
</section>