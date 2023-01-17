<?php
class AdminController extends Controller
{
  //ダッシュボード
  public function dashboardAction()
  {

    //セッションからユーザー情報を取得
    $admin = $this->session->get('admin');

    if (!$this->session->isAdminAuthenticated() || empty($admin)) {
      return $this->redirect('/admin/admin_login');
    }

    //管理者の投稿総数
    $select_posts = $this->db_manager->get('Post')->fetchAllPostByAdminId($admin['id']);
    $count_posts = count($select_posts);

    //公開されている投稿
    $count_active_posts = $this->db_manager->get('Post')->fetchCountByPostByStatusAndAdminId($this->application::ACTIVE_STATUS, $admin['id']);

    //非公開な投稿
    $count_deactive_posts = $this->db_manager->get('Post')->fetchCountByPostByStatusAndAdminId($this->application::NON_ACTIVE_STATUS, $admin['id']);

    //ユーザーアカウント
    $select_users = $this->db_manager->get('User')->fetchAllUser();
    $count_users = count($select_users);

    //管理者アカウント
    $select_admins = $this->db_manager->get('Admin')->fetchAllAdmin();
    $count_admins = count($select_admins);

    //コメント
    $count_comments = $this->db_manager->get('Comment')->fetchCountCommentByAdminId($admin['id']);

    //総いいね
    $count_likes = $this->db_manager->get('Like')->fetchCountLikeByAdminId($admin['id']);

    return $this->render(array(
      'admin' => $admin,
      'count_posts' => $count_posts,
      'count_active_posts' => $count_active_posts['total'],
      'count_deactive_posts' => $count_deactive_posts['total'],
      'count_users' => $count_users,
      'count_admins' => $count_admins,
      'count_comments' => $count_comments['total'],
      'count_likes' => $count_likes['total'],
    ), 'dashboard', 'admin_layout');
  }

  // //管理者登録
  // public function signupAction()
  // {
  //   return $this->render(array(
  //     'name' => '',
  //     'password' => '',
  //     '_token' => $this->generateCsrfToken('account/signup'),
  //   ));
  // }

  //新規投稿
  public function add_postsAction()
  {
    //セッションからユーザー情報を取得
    $admin = $this->session->get('admin');

    if (!$this->session->isAdminAuthenticated() || empty($admin)) {
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
      'category' => $this->application::$category_array,
      'errors' => $message,
    ), 'add_posts', 'admin_layout');
  }

  //ログイン
  public function admin_loginAction()
  {
    //アクセスチェック
    if ($this->session->isAdminAuthenticated()) {
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
          $this->session->setAdminAuthenticated(true);
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

    if (!$this->session->isAdminAuthenticated() || empty($admin)) {
      return $this->redirect('/admin/admin_login');
    }

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

            //一回ログアウトしてから再ログインを行う
            $this->logout();

            //ログイン状態の制御
            $this->session->setAdminAuthenticated(true);

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

    if (!$this->session->isAdminAuthenticated() || empty($admin)) {
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

          //管理者名の更新
          $this->db_manager->get('Post')->updateName($admin['id'], $name);

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
    $this->logout();
    return $this->redirect('/admin/admin_login');
  }


  public function view_postsAction($params)
  {

    //管理者のセッション情報を取得する
    $admin = $this->session->get('admin');

    if (!$this->session->isAdminAuthenticated() || empty($admin)) {
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

          //いいねの削除
          $this->db_manager->get('Like')->deleteByPostId($id);

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
      'view_posts' => $view_posts,
    ), 'view_posts', 'admin_layout');
  }

  public function read_postAction($params)
  {
    //管理者のセッション情報を取得する
    $admin = $this->session->get('admin');

    if (!$this->session->isAdminAuthenticated() || empty($admin)) {
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
        $comment_id = $this->request->getPost('comment_id');
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
    $post_comments = $this->db_manager->get('Comment')->fetchAllCommentByPostId($post_id);

    return $this->render(array(
      'errors' => $message,
      'admin' => $admin,
      'view_posts' => $view_posts,
      'comments' => $post_comments,
    ), 'read_post', 'admin_layout');
  }

  public function admin_accountsAction()
  {
    //管理者のセッション情報を取得する
    $admin = $this->session->get('admin');

    if (!$this->session->isAdminAuthenticated() || empty($admin)) {
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

    if (!$this->session->isAdminAuthenticated() || empty($admin)) {
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
      while ($fetch_comments = current($select_comments)) {
        $select_posts = $this->db_manager->get('Post')->fetchPostId($fetch_comments['post_id']);

        $comments[] = [
          'post_id' => $select_posts['id'],
          'post_title' => $select_posts['title'],
          'id' => $fetch_comments['id'],
          'user_name' => $fetch_comments['user_name'],
          'date' => $fetch_comments['date'],
          'comment' => $fetch_comments['comment']
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

    if (!$this->session->isAdminAuthenticated() || empty($admin)) {
      return $this->redirect('/admin/admin_login');
    }

    $select_account = $this->db_manager->get('User')->fetchAllUser();

    $users_list = array();
    if ($select_account) {

      while ($fetch_accounts = current($select_account)) {

        //ユーザーのコメントを取得
        $count_user_comments = $this->db_manager->get('Comment')->fetchAllCommentByUserId($fetch_accounts['id']);
        $total_user_comments = count($count_user_comments);

        //ユーザーのいいねを取得
        $count_user_likes = $this->db_manager->get('Like')->fetchAllLikeByUserId($fetch_accounts['id']);
        $total_user_likes = count($count_user_likes);

        $users_list[] = [
          'user_id' => $fetch_accounts['id'],
          'name' => $fetch_accounts['name'],
          'total_user_comments' => $total_user_comments,
          'total_user_likes' => $total_user_likes
        ];
        next($select_account);
      }
    }

    return $this->render(array(
      'admin' => $admin,
      'users_list' => $users_list,
    ), 'user_accounts', 'admin_layout');
  }

  //投稿の編集
  public function edit_postAction($param)
  {

    //管理者のセッション情報を取得する
    $admin = $this->session->get('admin');

    if (!$this->session->isAdminAuthenticated() || empty($admin)) {
      return $this->redirect('/admin/admin_login');
    }

    if ($this->request->isPost()) {

      //編集処理/
      $save = $this->request->getPost('save');
      if (isset($save)) {

        //編集
        //$post_id = $this->request->getPost('id');
        $post_id = $param['post_id'];
        $title = $this->request->getPost('title');
        $content = $this->request->getPost('content');
        $category = $this->request->getPost('category');
        $status = $this->request->getPost('status');

        $old_image = $this->request->getPost('old_image');
        $image = htmlspecialchars($_FILES['image']['name']);
        $image_size = $_FILES['image']['size'];
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_folder = '../web/upload_img/' . $image;
        $select_image = $this->db_manager->get('Post')->fetchPostByImageAndAdminId($image, $admin['id']);

        if (!empty($image)) {
          if ($image_size > $this->application::MAX_IMAGE_FILE_SIZE) {
            $message[] = '画像サイズが大きすぎます。';
          } elseif ($select_image != false and $image != '') {
            $message[] = '画像ファイル名が同じです。';
          } else {
            $generateImageName = $this->generateImageName($image);
            $imagePath = '../web/uploaded_img/' . $generateImageName;
            move_uploaded_file($image_tmp_name, $imagePath);
            $this->db_manager->get('Post')->updateImage($generateImageName, $post_id);
            if ($old_image != $image and $old_image != '') {
              unlink('../web/upload_img/' . $old_image);
            }
            $message[] = '画像を更新しました。';
          }
        }

        //投稿の削除
        if (count($message) == 0) {
          $this->db_manager->get('Post')->update($title, $content, $category, $status, $post_id);
          $message[] = '更新しました。';
        }
      }

      //投稿を削除
      $delete_post = $this->request->getPost('delete_post');
      if (isset($delete_post)) {

        //$post_id = $this->request->getPost('id');
        $post_id = $param['post_id'];
        $delete_image = $this->db_manager->get('Post')->fetchPostId($post_id);
        //画像ファイルを削除
        if ($delete_image['image'] != '') {
          unlink('../web/uploaded_img/' . $delete_image['image']);
        }

        //投稿を削除
        $this->db_manager->get('Post')->delete($post_id);

        //コメントを削除
        $this->db_manager->get('Comment')->deleteByPostId($post_id);
        $message[] = '投稿を削除しました。';
      }

      //画像のみを削除
      $delete_image = $this->request->getPost('delete_image');
      if (isset($delete_image)) {
        $empty_image = '';
        $post_id = $param['post_id'];
        $delete_image = $this->db_manager->get('Post')->fetchPostId($post_id);
        //画像ファイルを削除
        if ($delete_image['image'] != '') {
          unlink('../web/uploaded_img/' . $delete_image['image']);
        }

        //画像フィールドを更新
        $this->db_manager->get('Post')->updateImage($empty_image, $post_id);
        $message[] = '画像を削除しました。';
      }
    }

    $post_id = $param['post_id'];
    $select_posts = $this->db_manager->get('Post')->fetchPostId($post_id);

    return $this->render(array(
      'admin' => $admin,
      'errors' => $message,
      'category' => $this->application::$category_array,
      'select_posts' => $select_posts,
    ), 'edit_post', 'admin_layout');
  }

  //検索ページ
  public function search_pageAction()
  {
    //管理者のセッション情報を取得する
    $admin = $this->session->get('admin');

    if (!$this->session->isAdminAuthenticated() || empty($admin)) {
      return $this->redirect('/admin/admin_login');
    }

    if ($this->request->isPost()) {
      //削除処理
      $delete = $this->request->getPost('delete');
      if (isset($delete)) {

        $post_id = $this->request->getPost('id');
        $delete_image = $this->db_manager->get('Post')->fetchPostId($post_id);

        //画像ファイルを削除
        if ($delete_image['image'] != '') {
          unlink('../web/uploaded_img/' . $delete_image['image']);
        }

        //投稿を削除
        $this->db_manager->get('Post')->delete($post_id);

        //コメントを削除
        $this->db_manager->get('Comment')->deleteByPostId($post_id);
        $message[] = '投稿を削除しました。';
      }

      $search_box = $this->request->getPost('search_box');
      $search_btn = $this->request->getPost('search_btn');
      $search_posts = array();

      if (isset($search_box) || isset($search_btn)) {

        //対象管理者の投稿を取得して表示する
        if (empty($search_box)) {
          $select_posts =  $this->db_manager->get('Post')->fetchAllPostByAdminId($admin['id']);
        } else {
          $select_posts =  $this->db_manager->get('Post')->fetchAllPostByInputWordsAndAdminId($admin['id'], $search_box);
        }

        if ($select_posts) {
          while ($fetch_posts = current($select_posts)) {
            $post_id = $fetch_posts['id'];
            //コメントを取得
            $total_post_comments = $this->db_manager->get('Comment')->fetchCountCommentByPostId($post_id);

            //いいねを取得
            $total_post_likes = $this->db_manager->get('Like')->fetchCountLikeByPostId($post_id);

            $search_posts[] = [
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
      }
    }
    return $this->render(array(
      'admin' => $admin,
      'errors' => $message,
      'search_posts' => $search_posts,
    ), 'search_page', 'admin_layout');
  }


  // プライベートでしか使用しないfunction ======================================================================
  private function logout()
  {
    $this->session->clear();
    $this->session->setAdminAuthenticated(false);
  }

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
