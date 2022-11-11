<?php
class UserRepository extends DbRepository
{
  /**
   * ログインユーザーに関連するコメントを取得する。
   */
  public function fetchByUser($email)
  {
    $sql = "SELECT * FROM users WHERE email = :email";
    return $this->fetch($sql, array(':email' => $email));
  }

  /**
   * ログインユーザーに関連するコメントを取得する。
   */
  public function fetchByUserById($id)
  {
    $sql = "SELECT * FROM users WHERE id = :id";
    return $this->fetch($sql, array(':id' => $id));
  }


  /**
   * ユーザーIDの重複を調べる
   */
  public function isUniqueUserAccount($email)
  {
    $sql = "SELECT COUNT(id) as count FROM users WHERE email = :email";
    $row = $this->fetch($sql, array(':email' => $email));
    if ($row['count'] === '0') {
      return true;
    }
    return false;
  }

  /**
   * ユーザーを新規登録する
   */
  public function insert($name, $email, $password)
  {
    //パスワードはハッシュ化した上でDBに登録する
    $password = $this->hashPassword($password);
    $sql = "INSERT INTO users(name, email, password) VALUES(:name, :email, :password)";
    $stmt = $this->execute($sql, array(
      ':name' => $name,
      ':email' => $email,
      ':password' => $password,
    ));
  }

  /**
   * ユーザー名を更新する
   */
  public function updateUserNameById($name, $id)
  {
    $sql = "UPDATE users SET name = :name WHERE id = :id";
    $stmt = $this->execute($sql, array(
      ':name' => $name,
      ':id' => $id,
    ));
  }

  /**
   * メールアドレスを更新する
   */
  public function updateEmailById($email, $id)
  {
    $sql = "UPDATE users SET email = :email WHERE id = :id";
    $stmt = $this->execute($sql, array(
      ':email' => $email,
      ':id' => $id,
    ));
  }

  /**
   * パスワードを更新する
   */
  public function updatePasswordById($password, $id)
  {
    //パスワードはハッシュ化した上でDBに登録する
    $password = $this->hashPassword($password);
    $sql = "UPDATE users SET password = :password WHERE id = :id";
    $stmt = $this->execute($sql, array(
      ':password' => $password,
      ':id' => $id,
    ));
  }
}
