<?php
class LikeRepository extends DbRepository
{

  /**
   * いいねを新規登録する
   */
  public function insert($user_id, $post_id, $admin_id)
  {
    //パスワードはハッシュ化した上でDBに登録する
    $sql = "INSERT INTO likes(user_id, post_id, admin_id) VALUES(:user_id, :post_id, :admin_id)";
    $stmt = $this->execute($sql, array(
      ':user_id' => $user_id,
      ':post_id' => $post_id,
      ':admin_id' => $admin_id,
    ));
  }

  /**
   * ログインユーザーに関連するいいねを取得する。
   */
  public function fetchAllLikeByUserId($user_id)
  {
    $sql = "SELECT * FROM likes WHERE user_id = :user_id";
    return $this->fetchAll($sql, array(':user_id' => $user_id));
  }

  /**
   * 管理者に関連するいいねを取得する。
   */
  public function fetchCountLikeByAdminId($admin_id)
  {
    $sql = "SELECT Count(*) total FROM likes WHERE admin_id = :admin_id";
    return $this->fetch($sql, array(':admin_id' => $admin_id));
  }


  /**
   * 投稿毎のいいねの数を取得する
   */
  public function fetchCountLikeByPostId($post_id)
  {
    $sql = "SELECT count(*) as total FROM likes WHERE post_id = :post_id";
    return $this->fetch($sql, array(':post_id' => $post_id));
  }


  /**
   * 投稿毎のいいねを取得する。
   */
  public function fetchAllLikeByPostId($post_id)
  {
    $sql = "SELECT * FROM likes WHERE post_id = :post_id";
    return $this->fetchAll($sql, array(':post_id' => $post_id));
  }

  /**
   * ユーザーがいいねした投稿を取得
   */
  public function fetchLikeByUserIdPostId($user_id, $post_id)
  {
    $sql = "SELECT * FROM likes WHERE user_id = :user_id AND post_id = :post_id";
    return $this->fetch($sql, array(':user_id' => $user_id, ':post_id' => $post_id));
  }

  /**
   * ユーザーがいいねした投稿件数を取得
   */
  public function fetchCountLikeByUserIdPostId($user_id, $post_id)
  {
    $sql = "SELECT COUNT(*) as total FROM likes WHERE user_id = :user_id AND post_id = :post_id";
    return $this->fetch($sql, array(':user_id' => $user_id, ':post_id' => $post_id));
  }


  /**
   * ユーザーのいいねを削除
   */
  public function DelLike($post_id, $user_id)
  {
    $sql = "DELETE FROM likes WHERE post_id = :post_id and user_id = :user_id";
    return $this->execute($sql, array(':post_id' => $post_id, ':user_id' => $user_id));
  }


  /**
   * 管理者ののいいねを削除
   */
  public function DelLikeByAdminId($admin_id)
  {
    $sql = "DELETE FROM likes WHERE admin_id = :admin_id";
    return $this->execute($sql, array(':admin_id' => $admin_id));
  }

  /**
   * 投稿のいいねを削除
   */
  public function deleteByPostId($post_id)
  {
    $sql = "DELETE FROM likes WHERE post_id = :post_id";
    return $this->execute($sql, array(':post_id' => $post_id));
  }
}
