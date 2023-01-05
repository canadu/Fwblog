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
    if ($this->request->isPost()) {
      $result = false;
      if (!is_null($this->request->getPost('publish'))) {
        $result = $this->Post($admin['id'], $this->application::ACTIVE_STATUS);
        if ($result) {
          $message[] = '投稿しました。';
        }
      };
      if (!is_null($this->request->getPost('draft'))) {
        $result = $this->Post($admin['id'], $this->application::NON_ACTIVE_STATUS,);
        if ($result) {
          $message[] = '下書きに保存しました。';
        }
      };
    }
    return $this->render(array(
      'admin' => $admin,
      'errors' => $message,
      'category' => $this->application::$category_array,
    ), 'add_posts', 'admin_layout');
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

            //レコードの取得
            $admin = $this->db_manager->get('Admin')->fetchByUserName($name);

            //ログイン状態の制御
            $this->session->setAuthenticated(true);

            //セッションにユーザー情報を格納
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

    if (!$this->session->isAuthenticated() || empty($admin)) {
      return $this->redirect('/admin/admin_login');
    }

    if ($this->request->isPost()) {

      $updateFg = false;

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
          $updateFg = true;
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
          // 更新処理を入れること
          $this->db_manager->get('Admin')->updatePassword($admin['id'], $confirm_password);
          $message[] = 'パスワードを更新しました。';
          $updateFg = true;
        }
      }

      //レコードの取得
      if ($updateFg) {
        $admin = $this->db_manager->get('Admin')->fetchById($admin['id']);
        //セッションにユーザー情報を格納
        $this->session->set('admin', $admin);
      }
    }

    return $this->render(array(
      'errors' => $message,
      'admin' => $admin,
      '_token' => $this->generateCsrfToken('admin/update_profile'),
    ), 'update_profile', 'admin_layout');
  }

  //ログアウト
  public function admin_logoutAction()
  {
    //セッションをクリア
    $this->session->clear();
    $this->session->setAuthenticated(false);
    return $this->redirect('/admin/admin_login');
  }

  public function view_postsAction($params)
  {

    //管理者のセッション情報を取得する
    $admin = $this->session->get('admin');

    if (!$this->session->isAuthenticated() || empty($admin)) {
      return $this->redirect('/admin/admin_login');
    }

    if ($this->request->isPost()) {
      //Postの場合
      $delete = $this->request->getPost('delete');
      if (isset($delete)) {
        //削除処理
        $id = $this->request->getPost('post_id');
        if (isset($id)) {
          $delete_image = $this->db_manager->get('Post')->fetchPostId($id);
          if ((!$delete_image) || $delete_image['image'] != '') {
            //ファイル削除
            if (file_exists('../web/upload_img/' . $delete_image['image'])) {
              unlink('../web/upload_img/' . $delete_image['image']);
            }
          }
          //データの削除
          //投稿の削除
          $this->db_manager->get('Post')->delete($id);
          //コメントの削除
          $this->db_manager->get('Comment')->deleteByPostId($id);
          $message[] = '投稿を削除しました。';
        }
      }
    } else {
      // Getの場合
      //$status = $this->request->getGet('status');
      $status = $params['status'];
    }
    //管理者の投稿を取得する
    $view_posts = array();
    if (!empty($status) && ($status == $this->application::ACTIVE_STATUS || $status == $this->application::NON_ACTIVE_STATUS)) {
      $select_posts = $this->db_manager->get('Post')->fetchAllPostByStatusAndAdminId($status, $admin['id']);
    } else {
      $select_posts = $this->db_manager->get('Post')->fetchAllPostByAdminId($admin['id']);
    }
    if ($select_posts) {

      while ($fetch_posts = current($select_posts)) {

        $post_id = $fetch_posts['id'];

        //コメントを取得
        $total_post_comments = $this->db_manager->get('Comment')->fetchCountCommentByPostId($post_id);

        //いいねを取得
        $total_post_likes = $this->db_manager->get('Like')->fetchCountLikeByPostId($post_id);

        $view_posts[] = [
          'post_id' => $post_id,
          'image' => $fetch_posts['image'],
          'status' => $fetch_posts['status'],
          'title' => $fetch_posts['title'],
          'content' => $fetch_posts['content'],
          'total_post_comments' => $total_post_comments,
          'total_post_likes' => $total_post_likes
        ];
        next($select_posts);
      }
    }
    return $this->render(array(
      'errors' => $message,
      'admin' => $admin,
      'select_posts' => $select_posts,
      'view_posts' => $view_posts,
    ), 'view_posts', 'admin_layout');
  }

  public function read_postAction($params)
  {
    //管理者のセッション情報を取得する
    $admin = $this->session->get('admin');

    if (!$this->session->isAuthenticated() || empty($admin)) {
      return $this->redirect('/admin/admin_login');
    }

    $post_id = $params['post_id'];

    if ($this->request->isPost()) {
      //Postの場合
      //削除処理
      $delete = $this->request->getPost('delete');
      if (isset($delete)) {
        $post_id = $this->request->getPost('post_id');

        $select_posts = $this->db_manager->get('Post')->fetchPostId($post_id);
        if (($select_posts) && $select_posts['image'] != '') {
          //画像ファイルの削除
          unlink('../web/upload_img/' . $select_posts['image']);
        }

        //投稿の削除
        $this->db_manager->get('Post')->delete($post_id);

        //コメントの削除
        $this->db_manager->get('Comment')->deleteByPostId($post_id);

        return $this->redirect('/admin/view_posts');
      }

      $delete_comment = $this->request->getPost('delete_comment');
      if (isset($delete_comment)) {
        //コメントの削除
        $comment_id = $delete_comment['comment_id'];
        $this->db_manager->get('Comment')->delete($comment_id);
        $message[] = 'コメントを削除しました。';
      }
    }

    //対象管理者の投稿を取得して表示する
    $view_posts = array();
    $select_posts = $this->db_manager->get('Post')->fetchPostByAdminIdAndId($admin['id'], $post_id);

    if ($select_posts) {

      $post_id = $select_posts['id'];

      //コメントを取得
      $total_post_comments = $this->db_manager->get('Comment')->fetchCountCommentByPostId($post_id);

      //いいねを取得
      $total_post_likes = $this->db_manager->get('Like')->fetchCountLikeByPostId($post_id);

      $view_posts[] = [
        'post_id' => $post_id,
        'image' => $select_posts['image'],
        'status' => $select_posts['status'],
        'title' => $select_posts['title'],
        'content' => $select_posts['content'],
        'total_post_comments' => $total_post_comments,
        'total_post_likes' => $total_post_likes
      ];
    }

    //コメントデータの取得
    $comments = array();
    $select_comments = $this->db_manager->get('Comment')->fetchAllCommentByPostId($post_id);
    if ($select_comments) {
      while ($fetch_comments = current($select_comments)) {
        $comments[] = [
          'id' => $fetch_comments['id'],
          'user_name' => $fetch_comments['user_name'],
          'date' => $fetch_comments['date'],
          'comment' => $fetch_comments['comment'],
        ];
        next($select_comments);
      }
    }

    return $this->render(array(
      'errors' => $message,
      'admin' => $admin,
      'view_posts' => $view_posts,
      'comments' => $comments,
    ), 'read_post', 'admin_layout');
  }

  public function admin_accountsAction()
  {
    //管理者のセッション情報を取得する
    $admin = $this->session->get('admin');

    if (!$this->session->isAuthenticated() || empty($admin)) {
      return $this->redirect('/admin/admin_login');
    }

    $admin_id = $admin['id'];

    if ($this->request->isPost()) {

      //削除処理
      $delete = $this->request->getPost('delete');

      if (isset($delete)) {

        $select_posts = $this->db_manager->get('Post')->fetchAllPostByAdminId($admin_id);
        if (($select_posts) && $select_posts['image'] != '') {
          //画像ファイルの削除
          unlink('../web/upload_img/' . $select_posts['image']);
        }

        //投稿の削除
        $this->db_manager->get('Post')->deleteByAdminId($admin_id);

        //コメントの削除
        $this->db_manager->get('Comment')->deleteByAdminId($admin_id);

        //いいねの削除
        $this->db_manager->get('Like')->DelLikeByAdminId($admin_id);

        //アカウントの削除
        $this->db_manager->get('Admin')->delete($admin_id);

        return $this->redirect('/admin/admin_logout');
      }
    }

    $admin_posts = array();

    //管理者のアカウントを取得して表示する
    $select_admins = $this->db_manager->get('Admin')->fetchAllAdmin();
    $admin_count = count($select_admins);
    if ($select_admins) {
      while ($fetch_accounts = current($select_admins)) {
        //$count_admin_posts = $this->db_manager->get('Post')->fetchAllPostByAdminId($fetch_accounts['id']);
        $total_admin_posts = $this->db_manager->get('Post')->fetchPostCountByAdminId($fetch_accounts['id']);
        $admin_posts[] = [
          'id' => $fetch_accounts['id'],
          'name' => $fetch_accounts['name'],
          'total' => $total_admin_posts['total'],
        ];
        next($select_admins);
      }
    }

    return $this->render(array(
      'admin' => $admin,
      'admin_posts' => $admin_posts,
      'admin_count' => $admin_count,
    ), 'admin_accounts', 'admin_layout');
  }

  public function commentsAction()
  {
    //管理者のセッション情報を取得する
    $admin = $this->session->get('admin');

    if (!$this->session->isAuthenticated() || empty($admin)) {
      return $this->redirect('/admin/admin_login');
    }

    if ($this->request->isPost()) {
      //削除処理
      $delete = $this->request->getPost('delete_comment');
      if (isset($delete)) {
        $comment_id = $this->request->getPost('comment_id');
        //コメントの削除
        $this->db_manager->get('Comment')->delete($comment_id);
        $message[] = 'コメントを削除しました。';
      }
    }

    $comments = array();

    //管理者コメントの件数の取得
    $select_comments = $this->db_manager->get('Comment')->fetchAllCommentByAdminId($admin['id']);

    $count_comments = count($select_comments);

    if ($select_comments) {
      while ($fetch_accounts = current($select_comments)) {
        $select_posts = $this->db_manager->get('Post')->fetchPostId($fetch_accounts['post_id']);

        $comments[] = [
          'post_id' => $select_posts['id'],
          'post_title' => $select_posts['title'],
          'id' => $select_comments['id'],
          'user_name' => $select_comments['user_name'],
          'date' => $select_comments['date'],
          'comment' => $select_comments['comment']
        ];
        next($select_comments);
      }
    }

    return $this->render(array(
      'admin' => $admin,
      'comments' => $comments,
      'count_comments' => $count_comments,
    ), 'comments', 'admin_layout');
  }

  public function user_accountsAction()
  {
    //管理者のセッション情報を取得する
    $admin = $this->session->get('admin');

    if (!$this->session->isAuthenticated() || empty($admin)) {
      return $this->redirect('/admin/admin_login');
    }
    $select_account = $this->db_manager->get('User')->fetchAllUser();
    if ($select_account) {
      while ($fetch_accounts = current($select_account)) {
        $select_posts = $this->db_manager->get('Post')->fetchPostId($fetch_accounts['post_id']);

        //ユーザーのコメントを取得

        //ユーザーのいいねを取得





        next($select_account);
      }
    }
  }




  // プライベートでしか使用しないfunction ======================================================================
  private function Post($id, $param_status)
  {
    global $message;
    $name = $this->request->getPost('name');
    $title = $this->request->getPost('title');
    $content = $this->request->getPost('content');
    $category = $this->request->getPost('category');
    $status = $param_status;

    $result = true;
    $generateImageName = '';

    if (!empty($_FILES['image']['name'])) {
      list($result, $errMessage) = $this->validateImage();
      if ($result !== true) {
        $message[] = $errMessage;
      } else {
        $image = htmlspecialchars($_FILES['image']['name'], ENT_QUOTES);
        $generateImageName = $this->generateImageName($image);
        $imagePath = '../web/uploaded_img/' . $generateImageName;
        move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
      }
    }
    if ($result) {
      //投稿処理
      $this->db_manager->get('Post')->insert($id, $name, $title, $content, $category, $generateImageName, $status,);
    }
    return $result;
  }

  // アップロードファイルの妥当性をチェックする関数
  private function validateImage(): array
  {
    // PHPによるエラーを確認する
    if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
      return [false, 'アップロードエラーを検出しました'];
    }

    // ファイル名から拡張子をチェックする
    if (!in_array($this->getExtensions($_FILES['image']['name']), ['jpg', 'jpeg', 'png', 'gif'])) {
      return [false, '画像ファイルのみアップロード可能です'];
    }

    // ファイルの中身を見てMIMEタイプをチェックする
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $_FILES['image']['tmp_name']);
    finfo_close($finfo);
    if (!in_array($mimeType, ['image/jpeg', 'image/png', 'image/gif'])) {
      return [false, '不正確な画像ファイル形式です'];
    }

    //ファイルサイズをチェックする
    if (filesize($_FILES['image']['tmp_name']) > 1024 * 1024 * 2) {
      return [false, '画像のサイズが大きすぎます。画像サイズは2MBまでです。'];
    }
    return [true, null];
  }

  // ファイル名を元に拡張子を返す関数
  private function getExtensions($file): string
  {
    return pathinfo($file, PATHINFO_EXTENSION);
  }

  // アップロード後に保存ファイル名を生成して返す関数
  private function generateImageName($name): string
  {
    return date('Ymd-His-') . rand(100000, 99999) . '.' . $this->getExtensions($name);
  }
}
