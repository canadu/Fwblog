<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $title; ?></title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
  <link rel="stylesheet" href="/css/admin_style.css">
</head>

<body>
  <?php if (isset($errors) && count($errors) > 0) : ?>
    <?php echo $this->render('errors', array('errors' => $errors)); ?>
  <?php endif; ?>
  <!-- ナビゲーションメニュー -->
  <header class="header">
    <a href="<?php echo $base_url; ?>/admin/dashboard" class="logo">Admin<span>Panel</span></a>
    <div class="profile">
      <p><?php echo $admin['name']; ?></p>
      <a href="<?php echo $base_url; ?>/admin/update_profile" class="btn">プロフィールを更新</a>
    </div>
    <nav class="navbar">
      <a href="<?php echo $base_url; ?>/admin/dashboard"><i class="fas fa-home"></i><span>ホーム</span></a>
      <a href="<?php echo $base_url; ?>/admin/add_posts"><i class="fas fa-pen"></i><span>投稿</span></a>
      <a href="<?php echo $base_url; ?>/admin/view_posts"><i class="fas fa-eye"></i><span>閲覧</span></a>
      <a href="<?php echo $base_url; ?>/admin/admin_accounts"><i class="fas fa-user"></i><span>アカウント</span></a>
      <a href="<?php echo $base_url; ?>/admin/admin_logout" style="color:var(--red);" onclick="return confirm('サイトからログアウトしますか？');"><i class="fas fa-right-from-bracket"></i><span>ログアウト</span></a>
    </nav>
    <div class="flex-btn">
      <a href="<?php echo $base_url; ?>/admin/admin_login" class="option-btn">ログイン</a>
      <a href="<?php echo $base_url; ?>/admin/admin_register" class="option-btn">登録</a>
    </div>
  </header>
  <!-- ナビゲーションメニュー -->
  <div id="menu-btn" class="fas fa-bars"></div>
  <?php echo $_content; ?>
  <script src="../../js/admin_script.js"></script>
</body>

</html>