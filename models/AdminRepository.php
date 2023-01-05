<?php
class AdminRepository extends DbRepository
{

  public function insert($name, $password)
  {
    $password = $this->hashPassword($password);
    $sql = "INSERT INTO admin(name,password) VALUES(:name, :password)";
    $stmt = $this->execute($sql, array(
      ':name' => $name,
      ':password' => $password,
    ));
  }

  public function fetchByUserName($name)
  {
    $sql = "SELECT * FROM admin WHERE name = :name";
    return $this->fetch($sql, array(':name' => $name));
  }


  public function fetchById($id)
  {
    $sql = "SELECT * FROM admin WHERE id = :id";
    return $this->fetch($sql, array(':id' => $id));
  }

  public function fetchAllAdmin()
  {
    $sql = "SELECT * FROM admin";
    return $this->fetchAll($sql);
  }

  public function fetchAllAdminLimit10()
  {
    $sql = "SELECT DISTINCT name FROM admin LIMIT 10";
    return $this->fetchAll($sql);
  }

  public function updatePassword($id, $password)
  {
    $hash = $this->hashPassword($password);
    $sql = "UPDATE admin SET password = :password WHERE id = :id";
    $stmt = $this->execute($sql, array(
      ':id' => $id,
      ':password' => $hash,
    ));
  }

  public function updateName($id, $name)
  {
    $sql = "UPDATE admin SET name = :name WHERE id = :id";
    $stmt = $this->execute($sql, array(
      ':id' => $id,
      ':name' => $name,
    ));
  }

  // アカウントの削除
  public function delete($id)
  {
    $sql = "DELETE FROM admin WHERE id = :id";
    $stmt = $this->execute($sql, array(
      ':id' => $id
    ));
  }
}
