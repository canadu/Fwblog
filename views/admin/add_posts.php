<?php $this->setLayoutVar('title', '新規投稿') ?>
<?php $this->setLayoutVar('errors', $errors) ?>

<section class="post-editor">
  <h1 class="heading">新規投稿</h1>
  <form action="" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="name" value="<?php echo $admin['name']; ?>">
    <p>投稿タイトル<span>*</span></p>
    <input type="text" name="title" maxlength="100" require placeholder="投稿タイトルを入力してください。" class="box">
    <p>投稿記事<span>*</span></p>
    <textarea name="content" class="box" required maxlength="10000" placeholder="記事を入力してください。" cols="30" rows="10"></textarea>
    <p>投稿カテゴリ<span>*</span></p>
    <select name="category" class="box" required>
      <option value="" selected disabled>-- カテゴリを選択</option>
      <?php foreach ($category as $key => $value) { ?>
        <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
      <?php } ?>
    </select>
    <p>投稿画像</p>
    <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png, image/webp">
    <div class="flex-btn">
      <input type="submit" value="投稿" name="publish" class="btn">
      <input type="submit" value="下書き" name="draft" class="option-btn">
    </div>
  </form>
</section>