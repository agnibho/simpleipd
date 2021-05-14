<?php
class DB extends SQLite3 {
  function __construct(){
    $this->open("data/data.db");
  }
  function admit($post){
    $quer=$this->prepare("SELECT count(rowid) FROM patients WHERE pid=:pid");
    $quer->bindValue(":pid", $post["pid"]);
    $exist=$quer->execute();
    if($exist->fetchArray()[0]==0){
      $stmt=$this->prepare("INSERT INTO patients (pid, name, age, sex, data) VALUES (:pid, :name, :age, :sex, :data);");
    }
    else{
      $stmt=$this->prepare("UPDATE patients SET name=:name,age=:age,sex=:sex,data=:data WHERE pid=:pid;");
    }
    $stmt->bindValue(":pid", $post["pid"]);
    $stmt->bindValue(":name", $post["name"]);
    $stmt->bindValue(":age", $post["age"]);
    $stmt->bindValue(":sex", $post["sex"]);
    $stmt->bindValue(":data", json_encode($post));
    $stmt->execute();
  }
  function updateHistory($post, $pid){
    $stmt=$this->prepare("UPDATE patients SET data=:history WHERE pid=:pid;");
    $stmt->bindValue(":history", json_encode($post));
    $stmt->bindValue(":pid", $pid);
    $stmt->execute();
  }
  function addClinical($post, $pid){
    $stmt=$this->prepare("INSERT INTO clinical (pid, time, data) VALUES (:pid, :time, :data);");
    $stmt->bindValue(":pid", $pid);
    $stmt->bindValue(":time", strtotime($post["date"].$post["time"]));
    $stmt->bindValue(":data", json_encode($post));
    $stmt->execute();
  }
  function editClinical($post, $pid, $id){
    $stmt=$this->prepare("UPDATE clinical SET time=:time,data=:data WHERE pid=:pid AND rowid=:id;");
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
  function addDrug($pid, $name, $dose, $route, $frequency, $start, $duration, $addl){
    $stmt=$this->prepare("INSERT INTO drugs (pid, name, dose, route, frequency, start, duration, omit, addl) VALUES (:pid, :name, :dose, :route, :frequency, :start, :duration, :omit, :addl);");
    $stmt->bindValue(":pid", $pid);
    $stmt->bindValue(":name", $name);
    $stmt->bindValue(":dose", $dose);
    $stmt->bindValue(":route", $route);
    $stmt->bindValue(":frequency", $frequency);
    $stmt->bindValue(":start", $start);
    $stmt->bindValue(":duration", $duration);
    $stmt->bindValue(":addl", $addl);
    $stmt->bindValue(":omit", false);
    $stmt->execute();
  }
  function omitDrug($id){
    $stmt=$this->prepare("UPDATE drugs SET omit=:omit WHERE rowid=:id;");
    $stmt->bindValue(":omit", true);
    $stmt->bindValue(":id", $id);
    $stmt->execute();
  }
  function getDrugs($pid){
    $stmt=$this->prepare("SELECT rowid,* FROM drugs WHERE pid=:pid;");
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
  function getData($pid, $id, $cat){
    if($cat=="clinical"){
      $stmt=$this->prepare("SELECT data FROM clinical WHERE pid=:pid AND rowid=:id;");
    } elseif($cat=="reports"){
      $stmt=$this->prepare("SELECT data FROM reports WHERE pid=:pid AND rowid=:id;");
    } elseif($cat=="history"){
      $stmt=$this->prepare("SELECT data FROM patients WHERE pid=:pid AND rowid=:id;");
    }
    else{
      return(false);
    }
    $stmt->bindValue(":pid", $pid);
    $stmt->bindValue(":id", $id);
    $result=$stmt->execute();
    return($result);
  }
  function getAllData($pid, $cat){
    if($cat=="clinical"){
      $stmt=$this->prepare("SELECT rowid,data FROM clinical WHERE pid=:pid;");
    } elseif($cat=="reports"){
      $stmt=$this->prepare("SELECT rowid,data FROM reports WHERE pid=:pid;");
    } elseif($cat=="history"){
      $stmt=$this->prepare("SELECT rowid,data FROM patients WHERE pid=:pid;");
    }
    $stmt->bindValue(":pid", $pid);
    $result=$stmt->execute();
    return($result);
  }
}
$db = new DB();
?>
