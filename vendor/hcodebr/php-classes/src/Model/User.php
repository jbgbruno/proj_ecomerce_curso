<?php

namespace Hcode\Model;

use Hcode\Model;
use Hcode\DB\Sql;

class User extends Model
{
  const SESSION = "User";
  public static function login($login, $password)
  {
    $sql = new Sql;
    $results = $sql->select('select * from tb_users where deslogin = :LOGIN', [":LOGIN" => $login]);
    if (count($results) === 0) throw new \Exception("Usuarios Inexistente ou senha inválida");
    $data = $results[0];
    if (password_verify($password, $data["despassword"]) === true) {
      $user = new User();
      $user->setData($data);
      $_SESSION[User::SESSION] = $user->getValues();
      return $user;
    } else {
      throw new \Exception("Usuarios Inexistente ou senha inválida");
    }
  }
  public static function verifyLogin($inadmin = true)
  {
    if (!isset($_SESSION[User::SESSION]) || !$_SESSION[User::SESSION] || !(int)$_SESSION[User::SESSION]["iduser"] > 0 || (bool)$_SESSION[User::SESSION]["inadmin"] !== $inadmin) {
      header('Location: /admin/login');
      exit;
    }
  }
  public static function logout()
  {
    $_SESSION[User::SESSION] = null;
  }
  public static function listAll(){
    $sql = new Sql;
    return $sql->select('select * from tb_users a inner join tb_persons b Using(idperson) order by desperson ');
  }
  public function save()
  {
    $sql = new Sql();
    $results = $sql->select('CALL sp_users_save(:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)', [
      ":desperson"=>$this->getdesperson(),
      ":deslogin"=>$this->getdeslogin(),
      ":despassword"=>$this->getdespassword(),
      "desemail"=>$this->getdesemail(),
      ":nrphone"=>$this->getnrphone(),
      ":inadmin"=>$this->getinadmin(),
    ]);
    $this->setData($results[0]);
    
  }
  public function get($iduser)
  {
   $sql = new Sql;
   $results = $sql->select("SELECT * from tb_users a inner join tb_persons b using(idperson) where a.iduser = :iduser", [ ":iduser"=> $iduser]);
   $this->setData($results[0]);
  }
  
}
