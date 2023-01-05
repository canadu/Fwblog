<?php
class CommentRepository extends DbRepository
{
  /**
   * ログインユーザーに関連するコメントを取得する。
   */
  public function fetchAllCommentByUserId($user_id)
  {
    $sql = "SELECT * FROM comments WHERE user_id = :user_id";
    return $this->fetchAll($sql, array(':user_id' => $user_id));
  }

  /**
   * 投稿記事のコメントを取得する。
   */
  public function fetchAllCommentByPostId($post_id)
  {
    $sql = "SELECT * FROM comments WHERE post_id = :post_id";
    return $this->fetchAll($sql, array(':post_id' => $post_id));
  }

  /**
   * 投稿毎のコメントを取得する。
   */
  public function fetchAllCommentByCommentAndPostId($comment, $post_id)
  {
    $sql = "SELECT * FROM comments WHERE comment = :comment AND id = :id";
    return $this->fetchAll($sql, array(':comment' => $comment, ':id' => $post_id));
  }

  /**
   * コメントを取得する
   */
  public function fetchCommentByPostIdAndAdminIdAndUserIdAnd($post_id, $admin_id, $user_id)
  {
    $sql = "SELECT * FROM comments WHERE post_id = :post_id AND admin_id = :admin_id AND user_id = :user_id";
    return $this->fetch(
      $sql,
      array(':post_id' => $post_id, ':admin_id' => $admin_id, ':user_id' => $user_id)
    );
  }

  /**
   * コメントを取得する
   */
  public function fetchCommentById($comment_id)
  {
    $sql = "SELECT * FROM comments WHERE id = :id";
    return $this->fetch(
      $sql,
      array(':id' => $comment_id)
    );
  }

  /**
   * コメントを取得する
   */
  public function fetchAllCommentByAdminId($admin_id)
  {
    $sql = "SELECT * FROM comments WHERE admin_id = :admin_id";
    return $this->fetchall(
      $sql,
      array(':admin_id' => $admin_id)
    );
  }

  /**
   * 管理者のコメントを取得する
   */
  public function fetchCountCommentByAdminId($admin_id)
  {
    $sql = "SELECT count(*) as total FROM comments WHERE admin_id = :admin_id";
    return $this->fetch(
      $sql,
      array(':admin_id' => $admin_id)
    );
  }

  /**
   * 管理者のコメントの数を取得する
   */
  public function fetchCountCommentByPostId($post_id)
  {
    $sql = "SELECT count(*) as total FROM comments WHERE post_id = :post_id";
    return $this->fetch($sql, array(':post_id' => $post_id));
  }

  // コメントの新規追加
  public function insert($post_id, $admin_id, $user_id, $user_name, $comment)
  {
    $sql = "INSERT INTO comments(post_id, admin_id, user_id, user_name, comment) VALUES(:post_id,:admin_id,:user_id,:user_name,:comment)";
    $stmt = $this->execute($sql, array(
      ':post_id' => $post_id,
      ':admin_id' => $admin_id,
      ':user_id' => $user_id,
      ':user_name' => $user_name,
      ':comment' => $comment
    ));
  }

  // コメントの修正
  public function update($comment, $post_id)
  {
    $sql = "UPDATE comments SET comment = :comment WHERE id = :post_id";
    $stmt = $this->execute($sql, array(
      ':comment' => $comment,
      ':post_id' => $post_id
    ));
  }

  // コメントの削除
  public function delete($id)
  {
    $sql = "DELETE FROM comments WHERE id = :id";
    $stmt = $this->execute($sql, array(
      ':id' => $id
    ));
  }

  // コメントの削除
  public function deleteByPostId($post_id)
  {
    $sql = "DELETE FROM comments WHERE post_id = :post_id";
    $stmt = $this->execute($sql, array(
      ':post_id' => $post_id
    ));
  }

  // コメントの削除
  public function deleteByAdminId($admin_id)
  {
    $sql = "DELETE FROM comments WHERE admin_id = :admin_id";
    $stmt = $this->execute($sql, array(
      ':admin_id' => $admin_id
    ));
  }
}
