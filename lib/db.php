<?php
class DB extends SQLite3 {
  function __construct(){
    $this->open(CONFIG_DB);
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
  function getGroup($username){
    $stmt=$this->prepare("SELECT usergroup FROM users WHERE user=:user");
    $stmt->bindValue(":user", $username);
    $result=$stmt->execute();
    return($result);
  }
  function getDepartment($username){
    $stmt=$this->prepare("SELECT department FROM users WHERE user=:user");
    $stmt->bindValue(":user", $username);
    $result=$stmt->execute();
    return($result);
  }
  function admit($post){
    if(!checkAccess("admission", "dbSet")) return false;
    $query=$this->prepare("SELECT count(rowid) FROM patients WHERE pid=:pid");
    $query->bindValue(":pid", $post["pid"]);
    $exist=$query->execute();
    if($exist->fetchArray()[0]==0){
      $stmt=$this->prepare("INSERT INTO patients (pid,name,age,sex,status,vp,ward,bed,data) VALUES (:pid,:name,:age,:sex,:status,:vp,:ward,:bed,:data);");
    }
    else{
      $stmt=$this->prepare("UPDATE patients SET name=:name,age=:age,sex=:sex,ward=:ward,bed=:bed,vp=:vp,data=:data WHERE pid=:pid;");
    }
    $stmt->bindValue(":pid", $post["pid"]);
    $stmt->bindValue(":name", $post["name"]);
    $stmt->bindValue(":age", $post["age"]);
    $stmt->bindValue(":sex", $post["sex"]);
    $stmt->bindValue(":status", "admitted");
    $stmt->bindValue(":ward", $post["ward"]);
    $stmt->bindValue(":bed", $post["bed"]);
    $stmt->bindValue(":vp", $post["vp"]);
    $stmt->bindValue(":data", json_encode($post));
    $stmt->execute();
  }
  function editCase($pid, $diagnosis, $summary){
    if(!checkAccess("history", "dbSet")) return false;
    $stmt=$this->prepare("UPDATE patients SET diagnosis=:diagnosis,summary=:summary WHERE pid=:pid;");
    $stmt->bindValue(":pid", $pid);
    $stmt->bindValue(":diagnosis", $diagnosis);
    $stmt->bindValue(":summary", $summary);
    $stmt->execute();
  }
  function updateHistory($post, $pid){
    if(!checkAccess("history", "dbSet:")) return false;
    $stmt=$this->prepare("UPDATE patients SET history=:history WHERE pid=:pid;");
    $stmt->bindValue(":history", json_encode($post));
    $stmt->bindValue(":pid", $pid);
    $stmt->execute();
  }
  function addPhysician($post, $pid){
    if(!checkAccess("physician", "dbSet")) return false;
    $stmt=$this->prepare("INSERT INTO physician (pid, time, data) VALUES (:pid, :time, :data);");
    $stmt->bindValue(":pid", $pid);
    $stmt->bindValue(":time", strtotime($post["date"].$post["time"]));
    $stmt->bindValue(":data", json_encode($post));
    $stmt->execute();
  }
  function editPhysician($post, $pid, $id){
    if(!checkAccess("physician", "dbSet")) return false;
    $stmt=$this->prepare("UPDATE physician SET time=:time,data=:data WHERE pid=:pid AND rowid=:id;");
    $stmt->bindValue(":pid", $pid);
    $stmt->bindValue(":id", $id);
    $stmt->bindValue(":time", strtotime($post["date"].$post["time"]));
    $stmt->bindValue(":data", json_encode($post));
    $stmt->execute();
  }
  function addNursing($post, $pid){
    if(!checkAccess("nursing", "dbSet")) return false;
    $stmt=$this->prepare("INSERT INTO nursing (pid, time, data) VALUES (:pid, :time, :data);");
    $stmt->bindValue(":pid", $pid);
    $stmt->bindValue(":time", strtotime($post["date"].$post["time"]));
    $stmt->bindValue(":data", json_encode($post));
    $stmt->execute();
  }
  function editNursing($post, $pid, $id){
    if(!checkAccess("nursing", "dbSet")) return false;
    $stmt=$this->prepare("UPDATE nursing SET time=:time,data=:data WHERE pid=:pid AND rowid=:id;");
    $stmt->bindValue(":pid", $pid);
    $stmt->bindValue(":id", $id);
    $stmt->bindValue(":time", strtotime($post["date"].$post["time"]));
    $stmt->bindValue(":data", json_encode($post));
    $stmt->execute();
  }
  function addReport($post, $pid, $form){
    if(!checkAccess("report", "dbSet")) return false;
    $stmt=$this->prepare("INSERT INTO reports (pid, time, form, data) VALUES (:pid, :time, :form, :data);");
    $stmt->bindValue(":pid", $pid);
    $stmt->bindValue(":time", strtotime($post["date"].$post["time"]));
    $stmt->bindValue(":form", $post["form"]);
    $stmt->bindValue(":data", json_encode($post));
    $stmt->execute();
  }
  function editReport($post, $pid, $id, $form){
    if(!checkAccess("report", "dbSet")) return false;
    $stmt=$this->prepare("UPDATE reports SET time=:time,data=:data WHERE pid=:pid AND rowid=:id;");
    $stmt->bindValue(":pid", $pid);
    $stmt->bindValue(":id", $id);
    $stmt->bindValue(":time", strtotime($post["date"].$post["time"]));
    $stmt->bindValue(":data", json_encode($post));
    $stmt->execute();
  }
  function addDrug($pid, $drug, $dose, $route, $frequency, $date, $time, $duration, $addl){
    if(!checkAccess("treatment", "dbSet")) return false;
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
    if(!checkAccess("treatment", "dbSet")) return false;
    $stmt=$this->prepare("UPDATE treatment SET end=:end,omit=:omit WHERE rowid=:id;");
    $stmt->bindValue(":end", time());
    $stmt->bindValue(":omit", true);
    $stmt->bindValue(":id", $id);
    $stmt->execute();
  }
  function addRequisition($pid, $test, $date, $time, $room, $form){
    if(!checkAccess("requisition", "dbSet")) return false;
    $stmt=$this->prepare("INSERT INTO requisition (pid, test, time, room, form, status) VALUES (:pid, :test, :time, :room, :form, :status);");
    $stmt->bindValue(":pid", $pid);
    $stmt->bindValue(":test", $test);
    $stmt->bindValue(":time", strtotime($date." ".$time));
    $stmt->bindValue(":room", $room);
    $stmt->bindValue(":form", $form);
    $stmt->bindValue(":status", "active");
    $stmt->execute();
  }
  function omitRequisition($id){
    if(!checkAccess("requisition", "dbSet")) return false;
    $stmt=$this->prepare("UPDATE requisition SET status=:status WHERE rowid=:id;");
    $stmt->bindValue(":status", "done");
    $stmt->bindValue(":id", $id);
    $stmt->execute();
  }
  function addAdvice($pid, $name, $dose, $route, $frequency, $duration, $addl){
    if(!checkAccess("discharge", "dbSet")) return false;
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
    if(!checkAccess("discharge", "dbSet")) return false;
    $stmt=$this->prepare("DELETE FROM discharge WHERE rowid=:id;");
    $stmt->bindValue(":id", $id);
    $stmt->execute();
  }
  function setDischarged($pid){
    if(!checkAccess("discharge", "dbSet")) return false;
    $stmt=$this->prepare("UPDATE patients SET status=:discharged WHERE pid=:pid;");
    $stmt->bindValue(":pid", $pid);
    $stmt->bindValue(":discharged", "discharged");
    $stmt->execute();
  }
  function setDead($pid, $post){
    if(!checkAccess("death", "dbSet")) return false;
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
    if(!checkAccess("treatment", "dbGet")) return false;
    $stmt=$this->prepare("SELECT rowid,* FROM treatment WHERE pid=:pid;");
    $stmt->bindValue(":pid", $pid);
    $result=$stmt->execute();
    return($result);
  }
  function getRequisitions($pid){
    if(!checkAccess("requisition", "dbGet")) return false;
    $stmt=$this->prepare("SELECT rowid,* FROM requisition WHERE pid=:pid AND status=:status;");
    $stmt->bindValue(":pid", $pid);
    $stmt->bindValue(":status", "active");
    $result=$stmt->execute();
    return($result);
  }
  function getAdvice($pid){
    if(!checkAccess("discharge", "dbGet")) return false;
    $stmt=$this->prepare("SELECT rowid,* FROM discharge WHERE pid=:pid;");
    $stmt->bindValue(":pid", $pid);
    $result=$stmt->execute();
    return($result);
  }
  function getName($pid){
    if(!checkAccess("info", "dbGet")) return false;
    $stmt=$this->prepare("SELECT name FROM patients WHERE pid=:pid;");
    $stmt->bindValue(":pid", $pid);
    $result=$stmt->execute();
    return($result);
  }
  function getAge($pid){
    if(!checkAccess("info", "dbGet")) return false;
    $stmt=$this->prepare("SELECT age FROM patients WHERE pid=:pid;");
    $stmt->bindValue(":pid", $pid);
    $result=$stmt->execute();
    return($result);
  }
  function getSex($pid){
    if(!checkAccess("info", "dbGet")) return false;
    $stmt=$this->prepare("SELECT sex FROM patients WHERE pid=:pid;");
    $stmt->bindValue(":pid", $pid);
    $result=$stmt->execute();
    return($result);
  }
  function getWard($pid){
    if(!checkAccess("info", "dbGet")) return false;
    $stmt=$this->prepare("SELECT ward FROM patients WHERE pid=:pid;");
    $stmt->bindValue(":pid", $pid);
    $result=$stmt->execute();
    return($result);
  }
  function getBed($pid){
    if(!checkAccess("info", "dbGet")) return false;
    $stmt=$this->prepare("SELECT bed FROM patients WHERE pid=:pid;");
    $stmt->bindValue(":pid", $pid);
    $result=$stmt->execute();
    return($result);
  }
  function getStatus($pid){
    if(!checkAccess("info", "dbGet")) return false;
    $stmt=$this->prepare("SELECT status FROM patients WHERE pid=:pid;");
    $stmt->bindValue(":pid", $pid);
    $result=$stmt->execute();
    return($result);
  }
  function getDiagnosis($pid){
    if(!checkAccess("diagnosis", "dbGet")) return false;
    $stmt=$this->prepare("SELECT diagnosis FROM patients WHERE pid=:pid;");
    $stmt->bindValue(":pid", $pid);
    $result=$stmt->execute();
    return($result);
  }
  function getPatientList(){
    if(!checkAccess("info", "dbGet")) return false;
    $stmt=$this->prepare("SELECT pid,ward,bed,name,diagnosis FROM patients;");
    $result=$stmt->execute();
    return($result);
  }
  function getRequisitionList(){
    if(!checkAccess("requisition", "dbGet")) return false;
    $stmt=$this->prepare("SELECT rowid,pid,test,room,time,form FROM requisition WHERE status=:active;");
    $stmt->bindValue(":active", "active");
    $result=$stmt->execute();
    return($result);
  }
  function getForm($id){
    if(!checkAccess("report", "dbGet")) return false;
    $stmt=$this->prepare("SELECT form FROM reports WHERE rowid=:id;");
    $stmt->bindValue(":id", $id);
    $result=$stmt->execute();
    return($result);
  }
  function getAdmission($pid){
    if(!checkAccess("admission", "dbGet")) return false;
    $stmt=$this->prepare("SELECT admission FROM patients WHERE pid=:pid;");
    $stmt->bindValue(":pid", $pid);
    $result=$stmt->execute();
    return($result);
  }
  function getAdmissionData($pid){
    if(!checkAccess("admission", "dbGet")) return false;
    $stmt=$this->prepare("SELECT data FROM patients WHERE pid=:pid;");
    $stmt->bindValue(":pid", $pid);
    $result=$stmt->execute();
    return($result);
  }
  function getDeparture($pid){
    if(!checkAccess("admission", "dbGet")) return false;
    $stmt=$this->prepare("SELECT departure FROM patients WHERE pid=:pid;");
    $stmt->bindValue(":pid", $pid);
    $result=$stmt->execute();
    return($result);
  }
  function getSummary($pid){
    if(!checkAccess("summary", "dbGet")) return false;
    $stmt=$this->prepare("SELECT summary FROM patients WHERE pid=:pid;");
    $stmt->bindValue(":pid", $pid);
    $result=$stmt->execute();
    return($result);
  }
  function getHistory($pid){
    if(!checkAccess("history", "dbGet")) return false;
    $stmt=$this->prepare("SELECT history FROM patients WHERE pid=:pid;");
    $stmt->bindValue(":pid", $pid);
    $result=$stmt->execute();
    return($result);
  }
  function getData($pid, $id, $cat){
    if($cat=="physician"){
      if(!checkAccess("physician", "dbGet")) return false;
      $stmt=$this->prepare("SELECT data FROM physician WHERE pid=:pid AND rowid=:id;");
    } elseif($cat=="nursing"){
      if(!checkAccess("nursing", "dbGet")) return false;
      $stmt=$this->prepare("SELECT data FROM nursing WHERE pid=:pid AND rowid=:id;");
    } elseif($cat=="reports"){
      if(!checkAccess("report", "dbGet")) return false;
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
      if(!checkAccess("physician", "dbGet")) return false;
      $stmt=$this->prepare("SELECT rowid,data FROM physician WHERE pid=:pid;");
    } elseif($cat=="nursing"){
      if(!checkAccess("nursing", "dbGet")) return false;
      $stmt=$this->prepare("SELECT rowid,data FROM nursing WHERE pid=:pid;");
    } elseif($cat=="reports"){
      if(!checkAccess("report", "dbGet")) return false;
      $stmt=$this->prepare("SELECT rowid,data FROM reports WHERE pid=:pid;");
    } elseif($cat=="info"){
      if(!checkAccess("info", "dbGet")) return false;
      $stmt=$this->prepare("SELECT rowid,data FROM patients WHERE pid=:pid;");
    } elseif($cat=="history"){
      if(!checkAccess("history", "dbGet")) return false;
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
