<?php

/**
 * P271 アクションの作成手順
 * 1. データベースアクセス処理を Repository クラスに定義
 * 2. ルーティングを MiniBlogApplication クラスに定義
 * 3. コントローラクラスを定義
 * 4. コントローラクラスにアクションを定義
 * 5. アクションのビューファイルを記述
 */

class BlogApplication extends Application
{
  const ACTIVE_STATUS = 'active';
  const NON_ACTIVE_STATUS = 'deactive';
  protected $login_action = array('account', 'signin');

  public static $category_array = array(
    'education' => '教育',
    'animals' => 'ペットや動物',
    'technology' => 'テクノロジー',
    'fashion' => 'ファッション',
    'entertainment' => '娯楽',
    'movies' => '映画',
    'gaming' => 'ゲーム',
    'music' => '音楽',
    'sports' => 'スポーツ',
    'news' => 'ニュース',
    'travel' => '旅行',
    'comedy' => 'お笑い',
    'design' => 'デザインや開発',
    'food' => '食べ物',
    'lifestyle' => '生活',
    'personal' => '人物',
    'health' => '健康',
    'business' => '仕事',
    'shopping' => '買い物',
    'animations' => 'アニメ',
  );


  /**
   * ルートディレクトリへのパスを返す
   */
  public function getRootDir()
  {
    return dirname(__FILE__);
  }

  /**
   * ルーティングの設定を行う
   * ルーティング定義配列を返す。アクションを実装する段階で適宜追加する。
   */
  protected function registerRoutes()
  {
    return array(
      '/' => array('controller' => 'user', 'action' => 'index'),
      '/user/sign_in' => array('controller' => 'user', 'action' => 'sign_in'),
      '/user/sign_up' => array('controller' => 'user', 'action' => 'sign_up'),
      '/user/update_user_info' => array('controller' => 'user', 'action' => 'update_user_info'),
      '/user/category/:key' => array('controller' => 'user', 'action' => 'show_category'),
      '/user/all_category' => array('controller' => 'user', 'action' => 'show_all_category'),
      '/user/author_posts/:name' => array('controller' => 'user', 'action' => 'author_posts'),
      '/user/authors' => array('controller' => 'user', 'action' => 'view_authors'),
      '/user/view_post/:id' => array('controller' => 'user', 'action' => 'view_post'),
      '/user/posts' => array('controller' => 'user', 'action' => 'posts'),
      '/user/user_likes' => array('controller' => 'user', 'action' => 'user_likes'),
      '/user/user_comments' => array('controller' => 'user', 'action' => 'user_comments'),
      '/user/view_authors' => array('controller' => 'user', 'action' => 'view_authors'),
      '/user/:action' => array('controller' => 'user'),
      '/user/search_post' => array('controller' => 'search_post'),
      '/admin/dashboard' => array('controller' => 'admin', 'action' => 'dashboard'),
      '/admin/admin_login' => array('controller' => 'admin', 'action' => 'admin_login'),
      '/admin/admin_register' => array('controller' => 'admin', 'action' => 'admin_register'),
      '/admin/update_profile' => array('controller' => 'admin', 'action' => 'update_profile'),
      '/admin/admin_logout' => array('controller' => 'admin', 'action' => 'admin_logout'),
      '/admin/add_posts' => array('controller' => 'admin', 'action' => 'add_posts'),
      '/admin/view_posts' => array('controller' => 'admin', 'action' => 'view_posts'),
      '/admin/read_post/:post_id' => array('controller' => 'admin', 'action' => 'read_post'),
    );
  }

  /**
   * アプリケーションの設定を行うメソッド
   * ここではDBへの接続設定を記述する
   */
  protected function configure()
  {
    $this->db_manager->connect('master', array(
      'dsn' => 'mysql:host=localhost;dbname=blog_db',
      'user' => 'root',
      'password' => '',
    ));
  }
}
