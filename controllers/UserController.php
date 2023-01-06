<?php
class UserController extends Controller
{

  public function indexAction()
  {
    //セッションからユーザー情報を取得
    $user = $this->session->get('user');

    $comments = array();
    $likes = array();

    if (!empty($user)) {
      //ログインユーザーに関連する投稿を取得
      $comments = $this->db_manager->get('Comment')->fetchAllCommentByUserId($user['id']);
      $likes = $this->db_manager->get('Like')->fetchAllLikeByUserId($user['id']);
    }

    //管理者アカウントを取得
    $select_authors = $this->db_manager->get('Admin')->fetchAllAdminLimit10();


    $count_post_likes = array();
    $count_post_comments = array();
    $confirm_likes = array();

    //カテゴリがActiveの投稿を取得
    $select_posts = $this->db_manager->get('Post')->fetchByPostByStatus($this->application::ACTIVE_STATUS);

    if (count($select_posts) > 0) {

      foreach ($select_posts as $post) {

        //記事が公開されている場合
        $post_id = $post['id'];

        //各投稿毎のいいねの件数を取得
        $count_post_likes[] = $this->db_manager->get('Like')->fetchAllLikeByPostId($post_id);

        //各投稿毎のコメントの件数を取得
        $count_post_comments[] = $this->db_manager->get('Comment')->fetchAllCommentByPostId($post_id);

        //ユーザー毎のいいねをした投稿を取得
        $confirm_likes[] = $this->db_manager->get('Like')->fetchLikeByUserIdPostId($user['user_id'], $post_id);
      }
    }

    return $this->render(array(
      'user' => $user,
      'comments' => $comments,
      'likes' => $likes,
      'authors' => $select_authors,
      'select_posts' => $select_posts,
      'count_post_likes' => $count_post_likes,
      'count_post_comments' => $count_post_comments,
      'confirm_likes' => $confirm_likes,
      '_token' => $this->generateCsrfToken('user/sign_up'),
      'category' => $this->application::$category_array,
    ), 'home', 'user_layout');
  }

  //ログインページ
  public function sign_inAction()
  {
    return $this->render(array(
      '_token' => $this->generateCsrfToken('user/sign_in'),
    ), 'signin', 'user_login_layout');
  }

  //ユーザー登録
  public function sign_upAction()
  {
    return $this->render(array(
      '_token' => $this->generateCsrfToken('user/sign_up'),
    ), 'signup', 'user_login_layout');
  }

  //ユーザー更新
  public function update_user_infoAction()
  {

    //セッションからユーザー情報を取得
    $user = $this->session->get('user');

    if (!$this->session->isAuthenticated() || empty($user)) {
      return $this->redirect('/');
    }

    return $this->render(array(
      'user' => $user,
      '_token' => $this->generateCsrfToken('user/update_user_info'),
    ), 'update', 'user_layout');
  }

  /**
   * ログイン状態か確認する
   */
  public function authenticateAction()
  {

    //アクセスチェック
    //ログイン状態か？
    if ($this->session->isAuthenticated()) {
      return $this->redirect('/');
    }
    //POST送信か?
    if (!$this->request->isPost()) {
      $this->forward404();
    }

    //CSRFトークンは正しいか?
    $token = $this->request->getPost('_token');
    if (!$this->checkCsrfToken('user/sign_in', $token)) {
      return $this->redirect('/user/sign_in');
    }

    //フォームの入力内容を変数に格納
    $email = $this->request->getPost('email');
    $password = $this->request->getPost('password');

    /**バリデーション */
    $errors = array();
    if (!mb_strlen($email)) {
      $errors[] = 'メールアドレスを入力してください';
    }
    if (!mb_strlen($password)) {
      $errors[] = 'パスワードを入力してください';
    }

    if (count($errors) === 0) {

      // ユーザーリポジトリのインスタンスを生成する
      $user_repository = $this->db_manager->get('User');

      //ユーザー情報を取得する
      $user = $user_repository->fetchByUser($email);

      if (!$user || (!password_verify($password, $user['password']))) {
        $errors[] = 'ユーザーIDかパスワードが不正です。';
      } else {

        //ログイン状態の制御
        $this->session->setAuthenticated(true);

        //DBから取得したユーザー情報をセッションにセット
        $this->session->set('user', $user);

        //ユーザーホームページへリダイレクト
        return $this->redirect('/');
      }
    }
    return $this->render(array(
      'email' => $email,
      'password' => $password,
      'errors' => $errors,
      //トークンを作成する
      '_token' => $this->generateCsrfToken('user/sign_in'),
    ), 'signin', 'user_login_layout');
  }


  /**
   *ユーザーの登録処理
   */
  public function registerAction()
  {

    //HTTPメソッドのチェック
    if (!$this->request->isPost()) {
      $this->forward404();
    }

    //CSRFトークンのチェック
    $token = $this->request->getPost('_token'); {
      if (!$this->checkCsrfToken('user/sign_up', $token)) {
        return $this->redirect('/user/sign_up');
      }
    }

    //フォームから送信された値を取得
    $name = $this->request->getPost('name');
    $email = $this->request->getPost('email');
    $password = $this->request->getPost('password');
    $confirm_password = $this->request->getPost('confirm_password');

    //バリデーション
    $errors = array();

    if (!mb_strlen($name)) {
      $errors[] = 'ユーザー名を入力して下さい';
    } else if (!preg_match('/^\w{3,20}$/', $name)) {
      $errors[] = 'ユーザーIDは半角英数字およびアンダースコアを3～20文字で入力して下さい';
    }
    if (!mb_strlen($email)) {
      $errors[] = 'メールアドレスを入力して下さい';
    } else if (!preg_match('/^[a-z0-9._+^~-]+@[a-z0-9.-]+$/i', $email)) {
      $errors[] = '正しいメールアドレスを入力して下さい';
    }
    if (!mb_strlen($password)) {
      $errors[] = 'パスワードを入力して下さい';
    } else if (4 > mb_strlen($password) || mb_strlen($password) > 30) {
      $errors[] = 'パスワードは4～30文字以内で入力して下さい';
    }
    if (!mb_strlen($confirm_password)) {
      $errors[] = ' 確認用パスワードを入力して下さい';
    } else if (4 > mb_strlen($confirm_password) || mb_strlen($confirm_password) > 30) {
      $errors[] = '確認用パスワードは4～30文字以内で入力して下さい';
    }
    if ($password != $confirm_password) {
      $errors[] = 'パスワードが一致しません。';
    }
    if (!$this->db_manager->get('User')->isUniqueUserAccount($email)) {
      $errors[] = 'メールアドレスは既に登録されています';
    }

    if (count($errors) === 0) {

      //=====正常な処理============
      // レコードの登録
      $this->db_manager->get('User')->insert($name, $email, $password);

      //ログイン状態の制御
      $this->session->setAuthenticated(true);

      //レコードの取得
      $user = $this->db_manager->get('User')->fetchByUser($email);

      //セッションにユーザー情報を格納
      $this->session->set('user', $user);

      //リダイレクト
      return $this->redirect('/');
    }

    //エラーの場合は再度signup.phpをレンダリングしてエラーを表示する
    return $this->render(array(
      'email' => $email,
      'password' => $password,
      'errors' => $errors,
      //トークンを作成する
      '_token' => $this->generateCsrfToken('account/signup'),
    ), 'signup', 'user_login_layout');
  }


  /**
   *ユーザーの更新処理
   */
  public function updateAction()
  {
    //セッションからユーザー情報を取得
    $user = $this->session->get('user');

    if (!$this->session->isAuthenticated() || empty($user)) {
      return $this->redirect('/');
    }

    if (!$this->request->isPost()) {
      $this->forward404();
    }

    //CSRFトークンは正しいか?
    $token = $this->request->getPost('_token');
    if (!$this->checkCsrfToken('user/update_user_info', $token)) {
      return $this->redirect('/user/update_user_info');
    }

    //フォームから送信された値を取得
    $name = $this->request->getPost('name');
    $email = $this->request->getPost('email');
    $old_password = $this->request->getPost('old_password');
    $new_password = $this->request->getPost('new_password');
    $confirm_password = $this->request->getPost('confirm_password');

    //バリデーション
    $errors = array();
    $errFg = false;
    if (mb_strlen($name) > 0) {
      if (!preg_match('/^\w{3,20}$/', $name)) {
        $errors[] = 'ユーザーIDは半角英数字およびアンダースコアを3～20文字で入力して下さい';
        $errFg = true;
      }
    }
    if (mb_strlen($email) > 0) {
      if (!preg_match('/^[a-z0-9._+^~-]+@[a-z0-9.-]+$/i', $email)) {
        $errors[] = '正しいメールアドレスを入力して下さい';
        $errFg = true;
      }
    }

    if ($errFg) {
      goto GOTO_ERROR;
    }

    if (mb_strlen($new_password) > 0) {
      if (4 > mb_strlen($new_password) || mb_strlen($new_password) > 30) {
        $errors[] = 'パスワードは4～30文字以内で入力して下さい';
        $errFg = true;
      }
    }
    if ($errFg) {
      goto GOTO_ERROR;
    } else {

      // ユーザー名の更新
      if (mb_strlen($name) > 0) {
        $this->db_manager->get('User')->updateUserNameById($name, $user['id']);
      }

      // メールアドレスの更新
      if (mb_strlen($email) > 0) {
        $confirm_exist_email = $this->db_manager->get('User')->fetchByUser($email);
        if (count($confirm_exist_email) > 0) {
          $errors[] = "このメールアドレスは使用できません。";
          $errFg = true;
        } else {
          //emailの更新
          $this->db_manager->get('User')->updateEmailById($email, $user['id']);
        }
      }

      $select_prev_pass = $this->db_manager->get('User')->fetchByUserById($user['id']);
      $prev_password = $select_prev_pass['password'];

      //パスワードの更新
      if (!empty($old_password)) {
        if (!password_verify($old_password, $prev_password)) {
          $errors[]  = '古いパスワードが一致しません。';
          $errFg = true;
        } elseif ($new_password != $confirm_password) {
          $errors[]  = 'パスワードが一致しません。';
          $errFg = true;
        } else {
          $this->db_manager->get('User')->updatePasswordById($confirm_password, $user['id']);
          $errors[]  = 'パスワードを更新しました。';
        }
      }
    }

    GOTO_ERROR:

    if (!$errFg) {

      //レコードの取得
      $user = $this->db_manager->get('User')->fetchByUserById($user['id']);

      //セッションにユーザー情報を格納
      $this->session->set('user', $user);

      //リダイレクト
      return $this->redirect('/');
    }

    //エラーの場合は再度update.phpをレンダリングしてエラーを表示する
    return $this->render(array(
      'user' => $user,
      'errors' => $errors,
      //トークンを作成する
      '_token' => $this->generateCsrfToken('user/update_user_info'),
    ), 'update', 'user_layout');
  }

  /**
   *カテゴリページの表示
   */
  public function show_categoryAction($params)
  {

    //セッションからユーザー情報を取得
    $user = $this->session->get('user');

    if (!$this->session->isAuthenticated() || empty($user)) {
      return $this->redirect('/');
    }

    $count_post_likes = array();
    $count_post_comments = array();
    $confirm_likes = array();

    //カテゴリー毎の投稿を取得
    $select_posts = $this->db_manager->get('Post')->fetchByPostByCategory($params['key'], $this->application::ACTIVE_STATUS);

    if (count($select_posts) > 0) {

      foreach ($select_posts as $post) {

        //記事が公開されている場合
        $post_id = $post['id'];

        //各投稿毎のいいねの件数を取得
        $count_post_likes[] = $this->db_manager->get('Like')->fetchAllLikeByPostId($post_id);

        //各投稿毎のコメントの件数を取得
        $count_post_comments[] = $this->db_manager->get('Comment')->fetchAllCommentByPostId($post_id);

        //ユーザー毎のいいねをした投稿を取得
        $confirm_likes[] = $this->db_manager->get('Like')->fetchLikeByUserIdPostId($user['user_id'], $post_id);
      }
    }

    return $this->render(array(
      'user' => $user,
      'select_posts' => $select_posts,
      'count_post_likes' => $count_post_likes,
      'count_post_comments' => $count_post_comments,
      'confirm_likes' => $confirm_likes,
    ), 'show_category', 'user_layout');
  }

  /**
   *全カテゴリページの表示
   */
  public function show_all_categoryAction()
  {
    return $this->render(array(
      'category' => $this->application::$category_array,
    ), 'show_all_category', 'user_layout');
  }

  // 投稿ページを表示
  public function postsAction()
  {

    //セッションからユーザー情報を取得
    $user = $this->session->get('user');

    if (!$this->session->isAuthenticated() || empty($user)) {
      return $this->redirect('/');
    }

    // いいねをクリックした場合 =======================================================
    if ($this->request->isPost()) {

      $like_post = $this->request->getPost('like_post');

      if (isset($like_post)) {

        if (isset($user)) {

          $post_id = $this->request->getPost('post_id');
          $admin_id = $this->request->getPost('admin_id');

          //いいねした投稿記事を取得
          $select_post_like = $this->db_manager->get('Like')->fetchLikeByUserIdPostId($user['id'], $post_id);

          if ($select_post_like) {
            //既にレコードがある場合(いいねが押されている場合)
            $this->db_manager->get('Like')->DelLike($post_id);
            $errors[] = 'いいねを取り消しました';
          } else {
            //いいねが押されていない場合
            $this->db_manager->get('Like')->insert($user['id'], $post_id, $admin_id);
            $errors[] = 'いいねを追加しました';
          }
        } else {
          $errors[] = '最初にログインしてください。';
        }
      }
    }
    // ===============================================================================

    $count_post_likes = array();
    $count_post_comments = array();
    $confirm_likes = array();

    //カテゴリがActiveの投稿を取得
    $select_posts = $this->db_manager->get('Post')->fetchByPostByStatus($this->application::ACTIVE_STATUS);

    if (count($select_posts) > 0) {

      foreach ($select_posts as $post) {

        //記事が公開されている場合
        $post_id = $post['id'];

        //各投稿毎のいいねの件数を取得
        $count_post_likes[] = $this->db_manager->get('Like')->fetchAllLikeByPostId($post_id);

        //各投稿毎のコメントの件数を取得
        $count_post_comments[] = $this->db_manager->get('Comment')->fetchAllCommentByPostId($post_id);

        //ユーザー毎のいいねをした投稿を取得
        $confirm_likes[] = $this->db_manager->get('Like')->fetchLikeByUserIdPostId($user['user_id'], $post_id);
      }
    }

    return $this->render(array(
      'user' => $user,
      'select_posts' => $select_posts,
      'count_post_likes' => $count_post_likes,
      'count_post_comments' => $count_post_comments,
      'confirm_likes' => $confirm_likes,
      'category' => $this->application::$category_array,
      'errors' => $errors,
    ), 'posts', 'user_layout');
  }

  //投稿ページを表示
  public function view_postAction($params)
  {
    //セッションからユーザー情報を取得
    $user = $this->session->get('user');

    $errors = array();

    $post_id = $params['id'];

    $count_post_likes = array();
    $count_post_comments = array();
    $confirm_likes = array();

    if ($this->request->isPost()) {

      // いいねをクリックした場合 =======================================================
      $like_post = $this->request->getPost('like_post');

      if (isset($like_post)) {

        if (isset($user)) {

          $post_id = $this->request->getPost('post_id');
          $admin_id = $this->request->getPost('admin_id');

          //いいねした投稿記事を取得
          $select_post_like = $this->db_manager->get('Like')->fetchLikeByUserIdPostId($user['id'], $post_id);

          if ($select_post_like) {
            //既にレコードがある場合(いいねが押されている場合)
            $this->db_manager->get('Like')->DelLike($post_id);
            $errors[] = 'いいねを取り消しました';
          } else {
            //いいねが押されていない場合
            $this->db_manager->get('Like')->insert($user['id'], $post_id, $admin_id);
            $errors[] = 'いいねを追加しました';
          }
        } else {
          $errors[] = '最初にログインしてください。';
        }
        // ===============================================================================

      } else {

        // POSTの場合
        //CSRFトークンは正しいか
        $token = $this->request->getPost('_token');
        if (!$this->checkCsrfToken('user/view_post', $token)) {
          return $this->redirect('/');
        }

        // 画面下部の編集ボタン
        $open_edit_box = $this->request->getPost('open_edit_box');
        if (isset($open_edit_box)) {
          $comment_id = $this->request->getPost('comment_id');
          //コメントを取得
          $select_edit_comment = $this->db_manager->get('Comment')->fetchCommentById($comment_id);
        };

        // コメントの新規追加
        $add_comment = $this->request->getPost('add_comment');
        if (isset($add_comment)) {
          $admin_id = $this->request->getPost('admin_id');
          $user_name = $this->request->getPost('user_name');
          $comment = $this->request->getPost('comment');

          $verify_comment = $this->db_manager->get('Comment')->fetchCommentByPostIdAndAdminIdAndUserIdAnd($post_id, $admin_id, $user['id']);

          if ($verify_comment) {
            $errors[] = 'コメントは既に追加されています。';
          } else {
            //コメントの登録
            $this->db_manager->get('Comment')->insert($post_id, $admin_id, $user['id'], $user_name, $comment);
            $errors[] = '新しいコメントを追加しました。';
          }
        };

        // コメントの編集
        $edit_comment = $this->request->getPost('edit_comment');
        if (isset($edit_comment)) {
          $edit_comment_id = $this->request->getPost('edit_comment_id');
          $comment_edit_box = $this->request->getPost('comment_edit_box');

          $verify_comment = $this->db_manager->get('Comment')->fetchAllCommentByCommentAndPostId($comment_edit_box, $edit_comment_id);

          if (count($verify_comment) > 0) {
            $errors[] = 'コメントは既に追加されています。';
          } else {
            //コメントの登録
            $this->db_manager->get('Comment')->update($comment_edit_box, $edit_comment_id);
            $errors[] = 'コメントを修正しました。';
          }
        };

        // コメントの削除
        $delete_comment = $this->request->getPost('delete_comment');
        if (isset($delete_comment)) {
          $delete_comment_id = $this->request->getPost('comment_id');
          $delete_comment = $this->db_manager->get('Comment')->delete($delete_comment_id);
          $errors[] = 'コメントを削除しました。';
        };
      }
    }


    //カテゴリがActiveの投稿を取得
    $select_posts = $this->db_manager->get('Post')->fetchAllByPostByStatusAndId($this->application::ACTIVE_STATUS, $post_id);

    if (count($select_posts) > 0) {

      foreach ($select_posts as $post) {

        //記事が公開されている場合
        // $post_id = $post['id'];

        //各投稿毎のいいねの件数を取得
        $count_post_likes[] = $this->db_manager->get('Like')->fetchAllLikeByPostId($post_id);

        //各投稿毎のコメントの件数を取得
        $count_post_comments[] = $this->db_manager->get('Comment')->fetchAllCommentByPostId($post_id);

        //ユーザー毎のいいねをした投稿を取得
        $confirm_likes[] = $this->db_manager->get('Like')->fetchLikeByUserIdPostId($user['user_id'], $post_id);
      }
    }

    //投稿を取得
    $select_admin_id = $this->db_manager->get('Post')->fetchPostId($user['id']);

    //投稿毎のコメントを取得
    $select_comments = $this->db_manager->get('Comment')->fetchAllCommentByPostId($post_id);

    return $this->render(array(
      'user' => $user,
      'post_id' => $post_id,
      //トークンを作成する
      '_token' => $this->generateCsrfToken('user/view_post'),
      'select_edit_comment' => $select_edit_comment,
      'select_posts' => $select_posts,
      'count_post_likes' => $count_post_likes,
      'count_post_comments' => $count_post_comments,
      'confirm_likes' => $confirm_likes,
      'select_admin_id' => $select_admin_id,
      'select_comments' => $select_comments,
      'errors' => $errors,
    ), 'view_post', 'user_layout');
  }

  //管理者一覧ページを表示
  public function view_authorsAction()
  {

    $count_admin_posts = array();
    $count_admin_likes = array();
    $count_admin_comments = array();

    //カテゴリがActiveの投稿を取得
    $select_authors = $this->db_manager->get('Admin')->fetchAllAdmin();

    if (isset($select_authors)) {

      foreach ($select_authors as $author) {

        //記事を取得
        $count_admin_posts[] = $this->db_manager->get('Post')->fetchCountByPostByStatusAndAdminId($this->application::ACTIVE_STATUS, $author['id']);

        //いいねの件数を取得
        $count_admin_likes[] = $this->db_manager->get('Like')->fetchCountLikeByAdminId($author['id']);

        //コメントの件数を取得
        $count_admin_comments[] = $this->db_manager->get('Comment')->fetchCountCommentByAdminId($author['id']);
      }
    }

    return $this->render(array(
      'select_author' => $select_authors,
      'count_admin_posts' => $count_admin_posts,
      'count_admin_likes' => $count_admin_likes,
      'count_admin_comments' => $count_admin_comments,
    ), 'authors', 'user_layout');
  }

  //管理の投稿ページを表示
  public function author_postsAction($params)
  {

    //セッションからユーザー情報を取得
    $user = $this->session->get('user');

    if (!$this->session->isAuthenticated() || empty($user)) {
      return $this->redirect('/');
    }

    // いいねをクリックした場合 =======================================================
    if ($this->request->isPost()) {

      $like_post = $this->request->getPost('like_post');

      if (isset($like_post)) {

        if (isset($user)) {

          $post_id = $this->request->getPost('post_id');
          $admin_id = $this->request->getPost('admin_id');

          //いいねした投稿記事を取得
          $select_post_like = $this->db_manager->get('Like')->fetchLikeByUserIdPostId($user['id'], $post_id);

          if ($select_post_like) {
            //既にレコードがある場合(いいねが押されている場合)
            $this->db_manager->get('Like')->DelLike($post_id);
            $errors[] = 'いいねを取り消しました';
          } else {
            //いいねが押されていない場合
            $this->db_manager->get('Like')->insert($user['id'], $post_id, $admin_id);
            $errors[] = 'いいねを追加しました';
          }
        } else {
          $errors[] = '最初にログインしてください。';
        }
      }
    }
    // ===============================================================================

    $author = $params['name'];

    $count_post_likes = array();
    $count_post_comments = array();
    $confirm_likes = array();

    //管理者の投稿を取得
    $select_posts = $this->db_manager->get('Post')->fetchAllByPostByStatusAndName($this->application::ACTIVE_STATUS, $author);

    if (isset($select_posts)) {

      foreach ($select_posts as $post) {

        $post_id = $post['id'];

        //いいねの件数を取得
        $count_post_likes[] = $this->db_manager->get('Like')->fetchCountLikeByPostId($post_id);

        //コメントの件数を取得
        $count_post_comments[] = $this->db_manager->get('Comment')->fetchCountCommentByPostId($post_id);

        //ユーザーのいいねを取得
        $confirm_likes[] = $this->db_manager->get('Like')->fetchCountLikeByUserIdPostId($user['id'], $post_id);
      }
    }

    return $this->render(array(
      'select_posts' => $select_posts,
      'count_post_likes' => $count_post_likes,
      'count_post_comments' => $count_post_comments,
      'confirm_likes' => $confirm_likes,
    ), 'author_posts', 'user_layout');
  }



  //userがいいねをした投稿記事を表示
  public function user_likesAction()
  {

    //セッションからユーザー情報を取得
    $user = $this->session->get('user');

    if (!$this->session->isAuthenticated() || empty($user)) {
      return $this->redirect('/');
    }

    // いいねをクリックした場合 =======================================================
    if ($this->request->isPost()) {

      $like_post = $this->request->getPost('like_post');

      if (isset($like_post)) {

        if (isset($user)) {

          $post_id = $this->request->getPost('post_id');
          $admin_id = $this->request->getPost('admin_id');

          //いいねした投稿記事を取得
          $select_post_like = $this->db_manager->get('Like')->fetchLikeByUserIdPostId($user['id'], $post_id);

          if ($select_post_like) {
            //既にレコードがある場合(いいねが押されている場合)
            $this->db_manager->get('Like')->DelLike($post_id);
            $errors[] = 'いいねを取り消しました';
          } else {
            //いいねが押されていない場合
            $this->db_manager->get('Like')->insert($user['id'], $post_id, $admin_id);
            $errors[] = 'いいねを追加しました';
          }
        } else {
          $errors[] = '最初にログインしてください。';
        }
      }
    }
    // ===============================================================================

    $select_posts = array();

    //ユーザーのいいねを取得
    $select_likes = $this->db_manager->get('Like')->fetchAllLikeByUserId($user['id']);

    if (isset($select_likes)) {

      foreach ($select_likes as $like) {

        $post_id = $like['post_id'];

        $count_post_likes = 0;
        $count_post_comments = 0;

        //いいねした投稿記事を取得
        $like_post = $this->db_manager->get('Post')->fetchPostId($post_id);

        if (isset($like_post)) {
          if ($like_post['status'] != $this->application::NON_ACTIVE_STATUS) {
            //投稿記事のいいね件数を取得
            $count_post_likes = $this->db_manager->get('Like')->fetchCountLikeByPostId($post_id);
            //コメントの件数を取得
            $count_post_comments = $this->db_manager->get('Comment')->fetchCountCommentByPostId($post_id);
          }
        }
        $like_post['total_post_likes'] = $count_post_likes['total'];
        $like_post['total_post_comments'] = $count_post_comments['total'];
        $select_posts[] = $like_post;
      }
    }
    return $this->render(array(
      'select_posts' => $select_posts,
      'errors' => $errors,
    ), 'user_likes', 'user_layout');
  }

  //userがコメントをした投稿記事を表示
  public function user_commentsAction()
  {
    //セッションからユーザー情報を取得
    $user = $this->session->get('user');

    if (!$this->session->isAuthenticated() || empty($user)) {
      return $this->redirect('/');
    }

    $select_edit_comment = array();

    if ($this->request->isPost()) {

      // いいねをクリックした場合 =======================================================
      $like_post = $this->request->getPost('like_post');

      if (isset($like_post)) {

        if (isset($user)) {

          $post_id = $this->request->getPost('post_id');
          $admin_id = $this->request->getPost('admin_id');

          //いいねした投稿記事を取得
          $select_post_like = $this->db_manager->get('Like')->fetchLikeByUserIdPostId($user['id'], $post_id);

          if ($select_post_like) {
            //既にレコードがある場合(いいねが押されている場合)
            $this->db_manager->get('Like')->DelLike($post_id);
            $errors[] = 'いいねを取り消しました';
          } else {
            //いいねが押されていない場合
            $this->db_manager->get('Like')->insert($user['id'], $post_id, $admin_id);
            $errors[] = 'いいねを追加しました';
          }
        } else {
          $errors[] = '最初にログインしてください。';
        }
      }
      // ===============================================================================

      $edit_comment = $this->request->getPost('edit_comment');

      if (isset($edit_comment)) {
        $edit_comment_id = $this->request->getPost('edit_comment_id');
        $comment_edit_box = $this->request->getPost('comment_edit_box');
        //コメントを取得
        $verify_comment = $this->db_manager->get('Comment')->fetchAllCommentByCommentAndPostId($comment_edit_box, $edit_comment_id);

        if ($verify_comment) {
          //既にレコードがある場合
          $errors[] = 'コメントは既に追加されています';
        } else {
          //いいねが押されていない場合
          $this->db_manager->get('Comment')->update($comment_edit_box, $edit_comment_id);
          $errors[] = 'コメントを修正しました';
        }
      }

      $delete_comment = $this->request->getPost('delete_comment');
      if (isset($delete_comment)) {
        $delete_comment_id = $this->request->getPost('comment_id');
        //コメントを取得
        $verify_comment = $this->db_manager->get('Comment')->delete($delete_comment_id);
        $errors[] = 'コメントを削除しました';
      }

      //コメント編集をクリックした場合、IDからコメントを取得
      $open_edit_box = $this->request->getPost('open_edit_box');
      if (isset($open_edit_box)) {
        $comment_id = $this->request->getPost('comment_id');
        $select_edit_comment = $this->db_manager->get('Comment')->fetchCommentById($comment_id);
      }
    }

    //ユーザーのコメントを取得
    $select_comments = $this->db_manager->get('Comment')->fetchAllCommentByUserId($user['id']);

    $select_posts = array();
    if (isset($select_comments)) {
      foreach ($select_comments as $comment) {
        $post_id = $comment['post_id'];
        $select_posts[] = $this->db_manager->get('Post')->fetchPostId($post_id);
      }
    }

    return $this->render(array(
      'select_edit_comment' => $select_edit_comment,
      'select_comments' => $select_comments,
      'select_posts' => $select_posts,
      'errors' => $errors,
    ), 'user_comments', 'user_layout');
  }



  public function search_postAction()
  {

    //セッションからユーザー情報を取得
    $user = $this->session->get('user');

    if (!$this->session->isAuthenticated() || empty($user)) {
      return $this->redirect('/');
    }

    $select_posts = '';

    $count_post_likes = array();
    $count_post_comments = array();
    $confirm_likes = array();

    if ($this->request->isPost()) {

      $like_post = $this->request->getPost('like_post');

      if (isset($like_post)) {
        // いいねをクリックした場合 =======================================================
        if (isset($user)) {

          $post_id = $this->request->getPost('post_id');
          $admin_id = $this->request->getPost('admin_id');

          //いいねした投稿記事を取得
          $select_post_like = $this->db_manager->get('Like')->fetchLikeByUserIdPostId($user['id'], $post_id);

          if ($select_post_like) {
            //既にレコードがある場合(いいねが押されている場合)
            $this->db_manager->get('Like')->DelLike($post_id);
            $errors[] = 'いいねを取り消しました';
          } else {
            //いいねが押されていない場合
            $this->db_manager->get('Like')->insert($user['id'], $post_id, $admin_id);
            $errors[] = 'いいねを追加しました';
          }
        } else {
          $errors[] = '最初にログインしてください。';
        }
        // ===============================================================================
      }

      $search_box = $this->request->getPost('search_box');
      $search_btn = $this->request->getPost('search_btn');

      if (isset($search_box) or isset($search_btn)) {

        //いいねした投稿記事を取得
        $select_posts = $this->db_manager->get('Post')->fetchAllPostByInputWords($search_box, $this->application::ACTIVE_STATUS);

        if (isset($select_posts)) {

          foreach ($select_posts as $select_post) {

            $post_id = $select_post['post_id'];

            //いいねの件数を取得
            $count_post_likes[] = $this->db_manager->get('Like')->fetchCountLikeByPostId($post_id);

            //コメントの件数を取得
            $count_post_comments[] = $this->db_manager->get('Comment')->fetchCountCommentByPostId($post_id);

            //ユーザーのいいねを取得
            $confirm_likes[] = $this->db_manager->get('Like')->fetchCountLikeByUserIdPostId($user['id'], $post_id);
          }
        }
      }
    }

    return $this->render(array(
      'select_posts' => $select_posts,
      'count_post_likes' => $count_post_likes,
      'count_post_comments' => $count_post_comments,
      'confirm_likes' => $confirm_likes,
      'errors' => $errors,
    ), 'search_post', 'user_layout');
  }
}
