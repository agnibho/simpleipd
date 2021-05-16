<?php
class DB extends SQLite3 {
  function __construct(){
    $this->open("data/data.db");
  }
  function checkUser($username, $password){
    $stmt=$this->prepare("SELECT hash FROM users WHERE user=:user");
    $stmt->bindValue(":user", $username);
    $result=$stmt->execute();
    $hash=$result->fetchArray();
    if($hash){
      return(password_verify($password, $hash["hash"]));
    }
    else{
      return(false);
    }
  }
  function admit($post){
    $quer=$this->prepare("SELECT count(rowid) FROM patients WHERE pid=:pid");
    $quer->bindValue(":pid", $post["pid"]);
    $exist=$quer->execute();
    if($exist->fetchArray()[0]==0){
      $stmt=$this->prepare("INSERT INTO patients (pid,name,age,sex,status,summary,ward,bed,diagnosis,data) VALUES (:pid,:name,:age,:sex,'admitted',:summary,:ward,:bed,:diagnosis,:data);");
    }
    else{
      $stmt=$this->prepare("UPDATE patients SET name=:name,age=:age,sex=:sex,ward=:ward,bed=:bed,diagnosis=:diagnosis,summary=:summary,data=:data WHERE pid=:pid;");
    }
    $stmt->bindValue(":pid", $post["pid"]);
    $stmt->bindValue(":name", $post["name"]);
    $stmt->bindValue(":age", $post["age"]);
    $stmt->bindValue(":sex", $post["sex"]);
    $stmt->bindValue(":status", "admitted");
    $stmt->bindValue(":ward", $post["ward"]);
    $stmt->bindValue(":bed", $post["bed"]);
    $stmt->bindValue(":diagnosis", $post["diagnosis"]);
    $stmt->bindValue(":summary", $post["summary"]);
    $stmt->bindValue(":data", json_encode($post));
    $stmt->execute();
  }
  function updateHistory($post, $pid){
    $stmt=$this->prepare("UPDATE patients SET history=:history WHERE pid=:pid;");
    $stmt->bindValue(":history", json_encode($post));
    $stmt->bindValue(":pid", $pid);
    $stmt->execute();
  }
  function addPhysician($post, $pid){
    $stmt=$this->prepare("INSERT INTO physician (pid, time, data) VALUES (:pid, :time, :data);");
    $stmt->bindValue(":pid", $pid);
    $stmt->bindValue(":time", strtotime($post["date"].$post["time"]));
    $stmt->bindValue(":data", json_encode($post));
    $stmt->execute();
  }
  function editPhysician($post, $pid, $id){
    $stmt=$this->prepare("UPDATE physician SET time=:time,data=:data WHERE pid=:pid AND rowid=:id;");
    $stmt->bindValue(":pid", $pid);
    $stmt->bindValue(":id", $id);
    $stmt->bindValue(":time", strtotime($post["date"].$post["time"]));
    $stmt->bindValue(":data", json_encode($post));
    $stmt->execute();
  }
  function addNursing($post, $pid){
    $stmt=$this->prepare("INSERT INTO nursing (pid, time, data) VALUES (:pid, :time, :data);");
    $stmt->bindValue(":pid", $pid);
    $stmt->bindValue(":time", strtotime($post["date"].$post["time"]));
    $stmt->bindValue(":data", json_encode($post));
    $stmt->execute();
  }
  function editNursing($post, $pid, $id){
    $stmt=$this->prepare("UPDATE nursing SET time=:time,data=:data WHERE pid=:pid AND rowid=:id;");
    $stmt->bindValue(":pid", $pid);
    $stmt->bindValue(":id", $id);
    $stmt->bindValue(":time", strtotime($post["date"].$post["time"]));
    $stmt->bindValue(":data", json_encode($post));
    $stmt->execute();
  }
  function addReport($post, $pid, $form){
    $stmt=$this->prepare("INSERT INTO reports (pid, time, form, data) VALUES (:pid, :time, :form, :data);");
    $stmt->bindValue(":pid", $pid);
    $stmt->bindValue(":time", strtotime($post["date"].$post["time"]));
    $stmt->bindValue(":form", $post["form"]);
    $stmt->bindValue(":data", json_encode($post));
    $stmt->execute();
  }
  function editReport($post, $pid, $id, $form){
    $stmt=$this->prepare("UPDATE reports SET time=:time,data=:data WHERE pid=:pid AND rowid=:id;");
    $stmt->bindValue(":pid", $pid);
    $stmt->bindValue(":id", $id);
    $stmt->bindValue(":time", strtotime($post["date"].$post["time"]));
    $stmt->bindValue(":data", json_encode($post));
    $stmt->execute();
  }
  function addDrug($pid, $drug, $dose, $route, $frequency, $date, $time, $duration, $addl){
    $stmt=$this->prepare("INSERT INTO treatment (pid, drug, dose, route, frequency, start, duration, omit, addl) VALUES (:pid, :drug, :dose, :route, :frequency, :start, :duration, :omit, :addl);");
    $stmt->bindValue(":pid", $pid);
    $stmt->bindValue(":drug", $drug);
    $stmt->bindValue(":dose", $dose);
    $stmt->bindValue(":route", $route);
    $stmt->bindValue(":frequency", $frequency);
    $stmt->bindValue(":start", strtotime($date." ".$time));
    $stmt->bindValue(":duration", $duration);
    $stmt->bindValue(":addl", $addl);
    $stmt->bindValue(":omit", false);
    $stmt->execute();
  }
  function omitDrug($id){
    $stmt=$this->prepare("UPDATE treatment SET end=:end,omit=:omit WHERE rowid=:id;");
    $stmt->bindValue(":end", time());
    $stmt->bindValue(":omit", true);
    $stmt->bindValue(":id", $id);
    $stmt->execute();
  }
  function addAdvice($pid, $name, $dose, $route, $frequency, $duration, $addl){
    $stmt=$this->prepare("INSERT INTO discharge (pid, name, dose, route, frequency, duration, addl) VALUES (:pid, :name, :dose, :route, :frequency, :duration, :addl);");
    $stmt->bindValue(":pid", $pid);
    $stmt->bindValue(":name", $name);
    $stmt->bindValue(":dose", $dose);
    $stmt->bindValue(":route", $route);
    $stmt->bindValue(":frequency", $frequency);
    $stmt->bindValue(":duration", $duration);
    $stmt->bindValue(":addl", $addl);
    $stmt->execute();
  }
  function deleteAdvice($id){
    $stmt=$this->prepare("DELETE FROM discharge WHERE rowid=:id;");
    $stmt->bindValue(":id", $id);
    $stmt->execute();
  }
  function setDischarged($pid){
    $stmt=$this->prepare("UPDATE patients SET status=:discharged WHERE pid=:pid;");
    $stmt->bindValue(":pid", $pid);
    $stmt->execute();
  }
  function setDead($pid, $post){
    $stmt=$this->prepare("INSERT INTO death (pid, time, data) VALUES (:pid, :time, :data);");
    $stmt->bindValue(":pid", $pid);
    $stmt->bindValue(":time", strtotime($post["date"].$post["time"]));
    $stmt->bindValue(":data", json_encode($post));
    $stmt->execute();
    $stmt=$this->prepare("UPDATE patients SET status='expired' WHERE pid=:pid;");
    $stmt->bindValue(":pid", $pid);
    $stmt->execute();
  }
  function getDrugs($pid){
    $stmt=$this->prepare("SELECT rowid,* FROM treatment WHERE pid=:pid;");
    $stmt->bindValue(":pid", $pid);
    $result=$stmt->execute();
    return($result);
  }
  function getAdvice($pid){
    $stmt=$this->prepare("SELECT rowid,* FROM discharge WHERE pid=:pid;");
    $stmt->bindValue(":pid", $pid);
    $result=$stmt->execute();
    return($result);
  }
  function getName($pid){
    $stmt=$this->prepare("SELECT name FROM patients WHERE pid=:pid;");
    $stmt->bindValue(":pid", $pid);
    $result=$stmt->execute();
    return($result);
  }
  function getAge($pid){
    $stmt=$this->prepare("SELECT age FROM patients WHERE pid=:pid;");
    $stmt->bindValue(":pid", $pid);
    $result=$stmt->execute();
    return($result);
  }
  function getSex($pid){
    $stmt=$this->prepare("SELECT sex FROM patients WHERE pid=:pid;");
    $stmt->bindValue(":pid", $pid);
    $result=$stmt->execute();
    return($result);
  }
  function getWard($pid){
    $stmt=$this->prepare("SELECT ward FROM patients WHERE pid=:pid;");
    $stmt->bindValue(":pid", $pid);
    $result=$stmt->execute();
    return($result);
  }
  function getBed($pid){
    $stmt=$this->prepare("SELECT bed FROM patients WHERE pid=:pid;");
    $stmt->bindValue(":pid", $pid);
    $result=$stmt->execute();
    return($result);
  }
  function getStatus($pid){
    $stmt=$this->prepare("SELECT status FROM patients WHERE pid=:pid;");
    $stmt->bindValue(":pid", $pid);
    $result=$stmt->execute();
    return($result);
  }
  function getDiagnosis($pid){
    $stmt=$this->prepare("SELECT diagnosis FROM patients WHERE pid=:pid;");
    $stmt->bindValue(":pid", $pid);
    $result=$stmt->execute();
    return($result);
  }
  function getList(){
    $stmt=$this->prepare("SELECT pid FROM patients;");
    $result=$stmt->execute();
    return($result);
  }
  function getForm($id){
      $stmt=$this->prepare("SELECT form FROM reports WHERE rowid=:id;");
      $stmt->bindValue(":id", $id);
      $result=$stmt->execute();
      return($result);
  }
  function getAdmission($pid){
      $stmt=$this->prepare("SELECT data FROM patients WHERE pid=:pid;");
      $stmt->bindValue(":pid", $pid);
      $result=$stmt->execute();
      return($result);
  }
  function getHistory($pid){
      $stmt=$this->prepare("SELECT history FROM patients WHERE pid=:pid;");
      $stmt->bindValue(":pid", $pid);
      $result=$stmt->execute();
      return($result);
  }
  function getData($pid, $id, $cat){
    if($cat=="physician"){
      $stmt=$this->prepare("SELECT data FROM physician WHERE pid=:pid AND rowid=:id;");
    } elseif($cat=="nursing"){
      $stmt=$this->prepare("SELECT data FROM nursing WHERE pid=:pid AND rowid=:id;");
    } elseif($cat=="reports"){
      $stmt=$this->prepare("SELECT data FROM reports WHERE pid=:pid AND rowid=:id;");
    } else{
      return(false);
    }
    $stmt->bindValue(":pid", $pid);
    $stmt->bindValue(":id", $id);
    $result=$stmt->execute();
    return($result);
  }
  function getAllData($pid, $cat){
    if($cat=="physician"){
      $stmt=$this->prepare("SELECT rowid,data FROM physician WHERE pid=:pid;");
    } elseif($cat=="nursing"){
      $stmt=$this->prepare("SELECT rowid,data FROM nursing WHERE pid=:pid;");
    } elseif($cat=="reports"){
      $stmt=$this->prepare("SELECT rowid,data FROM reports WHERE pid=:pid;");
    } elseif($cat=="info"){
      $stmt=$this->prepare("SELECT rowid,data FROM patients WHERE pid=:pid;");
    } elseif($cat=="history"){
      $stmt=$this->prepare("SELECT rowid,history FROM patients WHERE pid=:pid;");
    } else{
      return(false);
    }
    $stmt->bindValue(":pid", $pid);
    $result=$stmt->execute();
    return($result);
  }
}
$db = new DB();
?>
