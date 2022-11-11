<?php
class PostRepository extends DbRepository
{
  /**
   * 投稿カテゴリー毎のデータを取得する
   */
  public function fetchByPostByCategory($category, $status)
  {
    $sql = "SELECT * FROM posts WHERE category = :category AND status = :status";
    return $this->fetchAll($sql, array(':category' => $category, ':status' => $status));
  }

  /**
   * 投稿カテゴリー毎のデータを取得する
   */
  public function fetchByPostByStatus($status)
  {
    $sql = "SELECT * FROM posts WHERE status = :status";
    return $this->fetchAll($sql, array(':status' => $status));
  }

  /**
   * 投稿データを取得する
   */
  public function fetchAllByPostByStatusAndId($status, $id)
  {
    $sql = "SELECT * FROM posts WHERE status = :status AND id = :id";
    return $this->fetchAll($sql, array(':status' => $status, 'id' => $id));
  }

  /**
   * 投稿データを取得する
   */
  public function fetchAllByPostByStatusAndName($status, $name)
  {
    $sql = "SELECT * FROM posts WHERE status = :status AND name = :name";
    return $this->fetchAll($sql, array(':status' => $status, 'name' => $name));
  }


  /**
   * 投稿カテゴリー毎のデータを取得する
   */
  public function fetchCountByPostByStatusAndAdminId($status, $admin_id)
  {
    $sql = "SELECT COUNT(*)  AS total FROM posts WHERE status = :status AND admin_id = :admin_id";
    return $this->fetch($sql, array(':status' => $status, 'admin_id' => $admin_id));
  }


  /**
   * 投稿を取得する
   */
  public function fetchPostId($id)
  {
    $sql = "SELECT * FROM posts WHERE id = :id";
    return $this->fetch($sql, array('id' => $id));
  }
}
