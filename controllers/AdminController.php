<?php
class AdminController extends Controller
{
  //ダッシュボード
  public function dashboardAction()
  {

    //セッションからユーザー情報を取得
    $admin = $this->session->get('admin');

    if (!$this->session->isAuthenticated() || empty($admin)) {
      return $this->redirect('/admin/admin_login');
    }

    //管理者の投稿総数
    $select_posts = $this->db_manager->get('Post')->fetchAllPostByAdminId($admin['id']);
    $number_of_posts = count($select_posts);

    //公開されている投稿
    $number_of_active_posts = $this->db_manager->get('Post')->fetchCountByPostByStatusAndAdminId($this->application::ACTIVE_STATUS, $admin['id']);
    $number_of_active_posts = $number_of_active_posts['total'];

    //非公開な投稿
    $number_of_deactive_posts = $this->db_manager->get('Post')->fetchCountByPostByStatusAndAdminId($this->application::NON_ACTIVE_STATUS, $admin['id']);
    $number_of_deactive_posts = $number_of_deactive_posts['total'];

    //ユーザーアカウント
    $select_users = $this->db_manager->get('User')->fetchAllUser;
    $number_of_users = count($select_users);

    //管理者アカウント
    $select_admins = $this->db_manager->get('Admin')->fetchAllAdmin;
    $number_of_admins = count($select_admins);

    //コメント
    $select_comments = $this->db_manager->get('Comment')->fetchCountCommentByAdminId($admin['id']);
    $number_of_comments = count($select_comments);

    //総いいね
    $select_likes = $this->db_manager->get('Like')->fetchCountLikeByAdminId($admin['id']);
    $number_of_likes = count($select_likes);

    return $this->render(array(
      'admin' => $admin,
      'number_of_posts' => $number_of_posts,
      'number_of_active_posts' => $number_of_active_posts,
      'number_of_deactive_posts' => $number_of_deactive_posts,
      'number_of_users' => $number_of_users,
      'number_of_admins' => $number_of_admins,
      'number_of_comments' => $number_of_comments,
      'number_of_likes' => $number_of_likes,
    ), 'dashboard', 'admin_layout');
  }

  //管理者登録
  public function signupAction()
  {
    return $this->render(array(
      'name' => '',
      'password' => '',
      '_token' => $this->generateCsrfToken('account/signup'),
    ));
  }

  //新規投稿
  public function add_postsAction()
  {
    //セッションからユーザー情報を取得
    $admin = $this->session->get('admin');

    if (!$this->session->isAuthenticated() || empty($admin)) {
      return $this->redirect('/admin/admin_login');
    }

    //POST送信か?
    if (!$this->request->isPost()) {
      if (!is_null($this->request->getPost('publish'))) {
        Post($admin['id'], $this->application::ACTIVE_STATUS);
      };
      if (!is_null($this->request->getPost('draft'))) {
        Post($admin['id'], $this->application::NON_ACTIVE_STATUS,);
      };
    }

    return $this->render(array(
      'admin' => $admin,
      // 'fetch_profile' => $fetch_profile,
      // 'likes' => $likes,
      // 'authors' => $select_authors,
      // '_token' => $this->generateCsrfToken('admin/add_posts'),
    ), 'home', 'admin_layout');
  }

  //ログイン
  public function admin_loginAction()
  {
    //アクセスチェック
    if ($this->session->isAuthenticated()) {
      //ログインしている場合は、ダッシュボードに移動する
      return $this->redirect('/admin/dashboard');
    }

    //POST送信か?
    $name = '';
    $errors = array();
    if ($this->request->isPost()) {

      //CSRFトークンは正しいか？
      $token = $this->request->getPost('_token');
      if (!$this->checkCsrfToken('admin/admin_login', $token)) {
        return $this->redirect('/admin/admin_login');
      }
      //フォームの入力内容を変数に格納
      $name = $this->request->getPost('name');
      $password = $this->request->getPost('password');

      // バリデーション
      if (!mb_strlen($name)) {
        $errors[] = 'ユーザー名を入力してください';
      }
      if (!mb_strlen($password)) {
        $errors[] = 'パスワードを入力してください';
      }

      if (count($errors) === 0) {

        //管理者のリポジトリインスタンスを生成する
        $admin_repository = $this->db_manager->get('Admin');

        //管理者情報を取得する
        $admin = $admin_repository->fetchByUserName($name);

        if (!$admin || (!password_verify($password, $admin['password']))) {
          $errors[] = 'ユーザー名かパスワードが不正です。';
        } else {
          //ログイン状態の制御
          $this->session->setAuthenticated(true);
          //DBから取得した管理者情報をセッションにセット
          $this->session->set('admin', $admin);
          //ダッシュボードのページへリダイレクト
          return $this->redirect('/admin/dashboard');
        }
      }
    }

    return $this->render(array(
      'errors' => $errors,
      'name' => $name,
      '_token' => $this->generateCsrfToken('admin/admin_login'),
    ), 'admin_login', 'admin_login_layout');
  }

  //管理者アカウントの登録
  public function admin_registerAction()
  {
    //管理者のセッション情報を取得する
    $admin = $this->session->get('admin');

    // if ($this->session->isAuthenticated() || !empty($admin)) {
    //   return $this->redirect('/admin/admin_login');
    // }

    $name = '';
    $message = array();

    if ($this->request->isPost()) {

      //CSRFトークンは正しいか？
      $token = $this->request->getPost('_token');
      if (!$this->checkCsrfToken('admin/admin_register', $token)) {
        return $this->redirect('/admin/admin_register');
      }

      //フォームの入力内容を変数に格納
      $name = $this->request->getPost('name');
      $password = $this->request->getPost('password');
      $confirm_password = $this->request->getPost('confirm_password');

      //バリデーション
      if (!mb_strlen($name)) {
        $message[] = 'ユーザー名を入力して下さい';
      } else if (!preg_match('/^\w{3,20}$/', $name)) {
        $message[] = 'ユーザーIDは半角英数字およびアンダースコアを3～20文字で入力して下さい';
      }

      if (!mb_strlen($password)) {
        $message[] = 'パスワードを入力して下さい';
      } else if (4 > mb_strlen($password) || mb_strlen($password) > 30) {
        $message[] = 'パスワードは4～30文字以内で入力して下さい';
      }

      if (!mb_strlen($confirm_password)) {
        $message[] = ' 確認用パスワードを入力して下さい';
      } else if (4 > mb_strlen($confirm_password) || mb_strlen($confirm_password) > 30) {
        $message[] = '確認用パスワードは4～30文字以内で入力して下さい';
      }

      if (count($message) === 0) {

        //管理者情報を取得する
        $admin = $this->db_manager->get('Admin')->fetchByUserName($name);

        if ($admin) {
          $message[] = '同じユーザー名が登録されています。';
        } else {
          if ($password == $confirm_password) {
            //入力値で管理者登録を行う
            $message[] = '新しい管理者を登録しました。';
            // レコードの登録
            $this->db_manager->get('Admin')->insert($name, $password);

            //DBから取得した管理者情報をセッションにセット
            $this->session->set('admin', $admin);

            //ダッシュボードのページへリダイレクト
            return $this->redirect('/admin/dashboard');
          } else {
            $message[] = 'パスワードが一致しません。';
          }
        }
      }
    }

    return $this->render(array(
      'errors' => $message,
      'name' => $name,
      '_token' => $this->generateCsrfToken('admin/admin_register'),
    ), 'admin_register', 'admin_login_layout');
  }

  //プロフィールの更新
  public function update_profileAction()
  {
    //管理者のセッション情報を取得する
    $admin = $this->session->get('admin');

    if ($this->session->isAuthenticated() || !empty($admin)) {
      return $this->redirect('/admin/admin_login');
    }

    if ($this->request->isPost()) {

      //CSRFトークンは正しいか？
      $token = $this->request->getPost('_token');
      if (!$this->checkCsrfToken('admin/update_profile', $token)) {
        return $this->redirect('/admin/update_profile');
      }

      //ユーザー名の取得
      $name = $this->request->getPost('name');
      if (!empty($name)) {
        // レコードの登録
        $adminUser = $this->db_manager->get('Admin')->fetchByUserName($name);
        if ($adminUser) {
          $message[] = 'ユーザー名は既に利用されています。';
        } else {
          //ユーザー情報の更新
          $this->db_manager->get('Admin')->updateName($admin['id'], $name);
          $message[] = 'ユーザー名を更新しました。';
        }
      }

      //現在パスワードを取得
      $select_old_password = $this->db_manager->get('Admin')->fetchById($admin['id']);
      $prev_password = $select_old_password['password'];

      $old_password = $this->request->getPost('old_password');
      $new_password = $this->request->getPost('new_password');
      $confirm_password = $this->request->getPost('confirm_password');

      if (!empty($old_password)) {
        if (!password_verify($old_password, $prev_password)) {
          $message[] = '古いパスワードが一致しません。';
        } elseif ($new_password != $confirm_password) {
          $message[] = 'パスワードが一致しません。';
        } else {
          $message[] = 'パスワードを更新しました。';

          // 更新処理を入れること



        }
      }
    }

    return $this->render(array(
      'errors' => $message,
      'name' => $name,
      '_token' => $this->generateCsrfToken('admin/update_profile'),
    ), 'admin_profile', 'admin_login_layout');
  }

  function Post(string $id, string $param_status)
  {
    // global $message;
    // $name = h($_POST['name']);
    // $title = h($_POST['title']);
    // $content = h($_POST['content']);
    // $category = h($_POST['category']);
    // $status = $param_status;
    // $result = false;
    // $generateImageName = '';

    // if (!empty($_FILES['image']['name'])) {
    //   list($result, $errMessage) = validateImage();
    //   if ($result !== true) {
    //     $message[] = $errMessage;
    //   } else {
    //     $image = htmlspecialchars($_FILES['image']['name'], ENT_QUOTES);
    //     $generateImageName = generateImageName($image);
    //     $imagePath = '../uploaded_img/' . $generateImageName;
    //     move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
    //   }
    // }
    // if ($result) {
    //   $insert_post = $conn->prepare("INSERT INTO posts(admin_id, name, title, content, category, image, status) VALUES(:admin_id, :name, :title, :content, :category, :image, :status)");
    //   $insert_post->bindValue(':admin_id', $id, PDO::PARAM_INT);
    //   $insert_post->bindValue(':name', $name, PDO::PARAM_STR);
    //   $insert_post->bindValue(':title', $title, PDO::PARAM_STR);
    //   $insert_post->bindValue(':content', $content, PDO::PARAM_STR);
    //   $insert_post->bindValue(':category', $category, PDO::PARAM_STR);
    //   $insert_post->bindValue(':image', $generateImageName, PDO::PARAM_STR);
    //   $insert_post->bindValue(':status', $status, PDO::PARAM_STR);
    //   $insert_post->execute();
    //   $message[] = $param_status == STATUS[0]  ? '投稿しました。' : '下書きに保存しました。';
    // }
  }
}
