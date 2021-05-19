<?php
class LG extends SQLite3 {
  function __construct(){
    $this->open(CONFIG_LOG);
  }
  function log($pid, $action, $data){
    $stmt=$this->prepare("INSERT INTO log (pid, user, ip, action, time, data) VALUES (:pid, :user, :ip, :action, :time, :data)");
    $stmt->bindValue(":pid", $pid);
    $stmt->bindValue(":user", $_SESSION["user"]);
    $stmt->bindValue(":ip", $_SERVER["REMOTE_ADDR"]);
    $stmt->bindValue(":action", $action);
    $stmt->bindValue(":time", time());
    $stmt->bindValue(":data", $data);
    $stmt->execute();
  }
}
$log = new LG();
?>
