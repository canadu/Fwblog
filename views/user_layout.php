<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
  <link rel="stylesheet" href="/css/style.css">
  <title><?php if (isset($title)) : echo $this->escape($title) . ' - ';
          endif; ?>Blog</title>
</head>

<body>
  <?php if (isset($errors) && count($errors) > 0) : ?>
    <?php echo $this->render('errors', array('errors' => $errors)); ?>
  <?php endif; ?>

  <!-- ヘッダー -->
  <header class="header">
    <section class="flex">

      <a href="<?php echo $base_url; ?>/" class="logo">Blog</a>

      <!-- 検索フォーム -->
      <form action="search.php" method="post" class="search-form">
        <input type="text" name="search_box" class="box" maxlength="100" placeholder="検索">
        <button type="submit" class="fas fa-search" name="search_btn"></button>
      </form>
      <div class="icons">
        <div id="menu-btn" class="fas fa-bars"></div>
        <div id="search-btn" class="fas fa-search"></div>
        <div id="user-btn" class="fas fa-user"></div>
      </div>

      <!-- menu-btnをクリックした際に表示 -->
      <nav class="navbar">
        <a href="<?php echo $base_url; ?>/"><i class="fas fa-angle-right"></i>ホーム</a>
        <a href="<?php echo $base_url; ?>/user/posts"><i class="fas fa-angle-right"></i>投稿</a>
        <a href="<?php echo $base_url; ?>/user/show_all_category"><i class="fas fa-angle-right"></i>カテゴリ</a>
        <a href="<?php echo $base_url; ?>/user/view_authors"><i class="fas fa-angle-right"></i>管理者一覧</a>
        <a href="<?php echo $base_url; ?>/user/sign_in"><i class="fas fa-angle-right"></i>ユーザーログイン</a>
        <a href="<?php echo $base_url; ?>/user/sign_up"><i class="fas fa-angle-right"></i>ユーザー登録</a>
      </nav>

      <!-- user-btnをクリックした際に表示 -->
      <div class="profile">
        <?php if (isset($user)) : ?>
          <p class="name"><?php echo $user['name']; ?></p>
          <a href="<?php echo $base_url; ?>/user/update_user_info" class="btn">プロフィールを更新</a>
          <div class="flex-btn">
            <a href="<?php echo $base_url; ?>/user/sign_in" class="option-btn">ログイン</a>
            <a href="<?php echo $base_url; ?>/user/sign_up" class="option-btn">登録</a>
          </div>
          <a href="components/user_logout.php" onclick="return confirm('サイトからログアウトしますか？');" class="delete-btn">ログアウト</a>
        <?php else : ?>
          <p class="name">最初にログインしてください</p>
          <a href="<?php echo $base_url; ?>/user/sign_in" class="option-btn">ログイン</a>
        <?php endif; ?>
      </div>

    </section>

  </header>

  <?php echo $_content; ?>
  <script src="/js/script.js"></script>
</body>

</html>