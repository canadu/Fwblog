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

  public function hashPassword($password)
  {
    return password_hash($password, ENT_QUOTES);
  }

  public function fetchByUserName($name)
  {
    $sql = "SELECT * FROM admin WHERE name = :name";
    return $this->fetch($sql, array(':name' => $name));
  }

  public function updatePassword($id, $password)
  {
    $password = $this->hashPassword($password);
    $sql = "UPDATE admin SET password = :password WHERE id = :id";
    $stmt = $this->execute($sql, array(
      ':id' => $id,
      ':password' => $password,
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
}
