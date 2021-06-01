<?php
class DB extends SQLite3 {
  function __construct(){
    $this->open(CONFIG_DB);
  }
  function checkUser($username, $password){
    global $log;
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
    global $log;
    $stmt=$this->prepare("SELECT usergroup FROM users WHERE user=:user");
    $stmt->bindValue(":user", $username);
    $result=$stmt->execute();
    return($result);
  }
  function getDepartment($username){
    global $log;
    $stmt=$this->prepare("SELECT department FROM users WHERE user=:user");
    $stmt->bindValue(":user", $username);
    $result=$stmt->execute();
    return($result);
  }
  function admit($post){
    global $log;
    if(!checkAccess("admission", "dbSet")) return false;
    $query=$this->prepare("SELECT count(rowid) FROM patients WHERE pid=:pid");
    $query->bindValue(":pid", $post["pid"]);
    $exist=$query->execute();
    if($exist->fetchArray()[0]==0){
      $stmt=$this->prepare("INSERT INTO patients (pid,name,age,sex,admission,status,vp,ward,bed,data) VALUES (:pid,:name,:age,:sex,:admission,:status,:vp,:ward,:bed,:data);");
    }
    else{
      $stmt=$this->prepare("UPDATE patients SET name=:name,age=:age,sex=:sex,admission=:admission,ward=:ward,bed=:bed,vp=:vp,data=:data WHERE pid=:pid;");
    }
    $stmt->bindValue(":pid", $post["pid"]);
    $stmt->bindValue(":name", $post["name"]);
    $stmt->bindValue(":age", $post["age"]);
    $stmt->bindValue(":sex", $post["sex"]);
    $stmt->bindValue(":admission", strtotime($post["date"]." ".$post["time"]));
    $stmt->bindValue(":status", "admitted");
    $stmt->bindValue(":ward", $post["ward"]);
    $stmt->bindValue(":bed", $post["bed"]);
    $stmt->bindValue(":vp", $post["vp"]);
    $stmt->bindValue(":data", json_encode($post));
    $stmt->execute();
    $log->log($post["pid"], "admit", json_encode($post));
  }
  function editCase($pid, $diagnosis, $summary){
    global $log;
    if(!checkAccess("history", "dbSet")) return false;
    $stmt=$this->prepare("UPDATE patients SET diagnosis=:diagnosis,summary=:summary WHERE pid=:pid;");
    $stmt->bindValue(":pid", $pid);
    $stmt->bindValue(":diagnosis", $diagnosis);
    $stmt->bindValue(":summary", $summary);
    $stmt->execute();
    $log->log($pid, "case_edit", json_encode([$diagnosis, $summary]));
  }
  function updateHistory($post, $pid){
    global $log;
    if(!checkAccess("history", "dbSet:")) return false;
    $stmt=$this->prepare("UPDATE patients SET history=:history WHERE pid=:pid;");
    $stmt->bindValue(":history", json_encode($post));
    $stmt->bindValue(":pid", $pid);
    $stmt->execute();
    $log->log($pid, "history", json_encode($post));
  }
  function addPhysician($post, $pid){
    global $log;
    if(!checkAccess("physician", "dbSet")) return false;
    $stmt=$this->prepare("INSERT INTO physician (pid, time, data) VALUES (:pid, :time, :data);");
    $stmt->bindValue(":pid", $pid);
    $stmt->bindValue(":time", strtotime($post["date"].$post["time"]));
    $stmt->bindValue(":data", json_encode($post));
    $stmt->execute();
    $log->log($pid, "physician_note", json_encode($post));
  }
  function editPhysician($post, $pid, $id){
    global $log;
    if(!checkAccess("physician", "dbSet")) return false;
    $stmt=$this->prepare("UPDATE physician SET time=:time,data=:data WHERE pid=:pid AND rowid=:id;");
    $stmt->bindValue(":pid", $pid);
    $stmt->bindValue(":id", $id);
    $stmt->bindValue(":time", strtotime($post["date"].$post["time"]));
    $stmt->bindValue(":data", json_encode($post));
    $stmt->execute();
    $log->log($pid, "edit_physician_note", json_encode($post));
  }
  function addNursing($post, $pid){
    global $log;
    if(!checkAccess("nursing", "dbSet")) return false;
    $stmt=$this->prepare("INSERT INTO nursing (pid, time, data) VALUES (:pid, :time, :data);");
    $stmt->bindValue(":pid", $pid);
    $stmt->bindValue(":time", strtotime($post["date"].$post["time"]));
    $stmt->bindValue(":data", json_encode($post));
    $stmt->execute();
    $log->log($pid, "nursing_note", json_encode($post));
  }
  function editNursing($post, $pid, $id){
    global $log;
    if(!checkAccess("nursing", "dbSet")) return false;
    $stmt=$this->prepare("UPDATE nursing SET time=:time,data=:data WHERE pid=:pid AND rowid=:id;");
    $stmt->bindValue(":pid", $pid);
    $stmt->bindValue(":id", $id);
    $stmt->bindValue(":time", strtotime($post["date"].$post["time"]));
    $stmt->bindValue(":data", json_encode($post));
    $stmt->execute();
    $log->log($pid, "edit_nursing_note", json_encode($post));
  }
  function addReport($post, $pid, $form){
    global $log;
    if(!checkAccess("report", "dbSet")) return false;
    $stmt=$this->prepare("INSERT INTO reports (pid, time, form, data) VALUES (:pid, :time, :form, :data);");
    $stmt->bindValue(":pid", $pid);
    if(!empty($post["time"])){
      $stmt->bindValue(":time", strtotime($post["date"].$post["time"]));
    }
    else{
      $stmt->bindValue(":time", strtotime($post["date"]));
    }
    $stmt->bindValue(":form", $post["form"]);
    $stmt->bindValue(":data", json_encode($post));
    $stmt->execute();
    $log->log($pid, "report_added", json_encode($post));
  }
  function editReport($post, $pid, $id, $form){
    global $log;
    if(!checkAccess("report", "dbSet")) return false;
    $stmt=$this->prepare("UPDATE reports SET time=:time,data=:data WHERE pid=:pid AND rowid=:id;");
    $stmt->bindValue(":pid", $pid);
    $stmt->bindValue(":id", $id);
    if(!empty($post["time"])){
      $stmt->bindValue(":time", strtotime($post["date"].$post["time"]));
    }
    else{
      $stmt->bindValue(":time", strtotime($post["date"]));
    }
    $stmt->bindValue(":data", json_encode($post));
    $stmt->execute();
    $log->log($pid, "report_edited", json_encode($post));
  }
  function addDrug($pid, $drug, $dose, $route, $frequency, $date, $time, $duration, $addl){
    global $log;
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
    $log->log($pid, "drug_added", json_encode([$drug,$dose,$route,$frequency,$date,$time,$duration,$addl]));
  }
  function omitDrug($id, $date, $time){
    global $log;
    if(!checkAccess("treatment", "dbSet")) return false;
    $stmt=$this->prepare("UPDATE treatment SET end=:end,omit=:omit WHERE rowid=:id;");
    $stmt->bindValue(":end", strtotime($date." ".$time));
    $stmt->bindValue(":omit", true);
    $stmt->bindValue(":id", $id);
    $stmt->execute();
    $log->log(null, "drug_omitted", $id);
  }
  function giveDrug($id, $given){
    global $log;
    if(!checkAccess("nursing", "dbSet")) return false;
    $stmt=$this->prepare("UPDATE treatment SET administer=:given WHERE rowid=:id;");
    $stmt->bindValue(":given", $given);
    $stmt->bindValue(":id", $id);
    $stmt->execute();
    $log->log(null, "drug_given", $id);
  }
  function addRequisition($pid, $test, $sample, $date, $time, $room, $form, $addl){
    global $log;
    if(!checkAccess("requisition", "dbSet")) return false;
    $stmt=$this->prepare("INSERT INTO requisition (pid, test, sample, time, room, form, status, addl) VALUES (:pid, :test, :sample, :time, :room, :form, :status, :addl);");
    $stmt->bindValue(":pid", $pid);
    $stmt->bindValue(":test", $test);
    $stmt->bindValue(":sample", $sample);
    $stmt->bindValue(":time", strtotime($date." ".$time));
    $stmt->bindValue(":room", $room);
    $stmt->bindValue(":form", $form);
    $stmt->bindValue(":status", "sent");
    $stmt->bindValue(":addl", $addl);
    $stmt->execute();
    $log->log($pid, "requisition_added", json_encode([$test,$room,$form]));
  }
  function receiveRequisition($id){
    global $log;
    if(!checkAccess("report", "dbSet")) return false;
    $stmt=$this->prepare("UPDATE requisition SET status=:status WHERE rowid=:id;");
    $stmt->bindValue(":status", "received");
    $stmt->bindValue(":id", $id);
    $stmt->execute();
    $log->log(null, "requisition_received", $id);
  }
  function omitRequisition($id){
    global $log;
    if(!checkAccess("requisition", "dbSet")) return false;
    $stmt=$this->prepare("UPDATE requisition SET status=:status WHERE rowid=:id;");
    $stmt->bindValue(":status", "done");
    $stmt->bindValue(":id", $id);
    $stmt->execute();
    $log->log(null, "requisition_removed", $id);
  }
  function addAdvice($pid, $drug, $dose, $route, $frequency, $duration, $addl){
    global $log;
    if(!checkAccess("discharge", "dbSet")) return false;
    $stmt=$this->prepare("INSERT INTO discharge (pid, drug, dose, route, frequency, duration, addl) VALUES (:pid, :drug, :dose, :route, :frequency, :duration, :addl);");
    $stmt->bindValue(":pid", $pid);
    $stmt->bindValue(":drug", $drug);
    $stmt->bindValue(":dose", $dose);
    $stmt->bindValue(":route", $route);
    $stmt->bindValue(":frequency", $frequency);
    $stmt->bindValue(":duration", $duration);
    $stmt->bindValue(":addl", $addl);
    $stmt->execute();
  }
  function deleteAdvice($id){
    global $log;
    if(!checkAccess("discharge", "dbSet")) return false;
    $stmt=$this->prepare("DELETE FROM discharge WHERE rowid=:id;");
    $stmt->bindValue(":id", $id);
    $stmt->execute();
  }
  function setDischarged($pid){
    global $log;
    if(!checkAccess("discharge", "dbSet")) return false;
    $stmt=$this->prepare("UPDATE patients SET status=:discharged WHERE pid=:pid;");
    $stmt->bindValue(":pid", $pid);
    $stmt->bindValue(":discharged", "discharged");
    $stmt->execute();
    $log->log($pid, "discharged", null);
  }
  function setDead($pid, $post){
    global $log;
    if(!checkAccess("death", "dbSet")) return false;
    $stmt=$this->prepare("INSERT INTO death (pid, time, data) VALUES (:pid, :time, :data);");
    $stmt->bindValue(":pid", $pid);
    $stmt->bindValue(":time", strtotime($post["date"].$post["time"]));
    $stmt->bindValue(":data", json_encode($post));
    $stmt->execute();
    $stmt=$this->prepare("UPDATE patients SET status='expired' WHERE pid=:pid;");
    $stmt->bindValue(":pid", $pid);
    $stmt->execute();
    $log->log($pid, "death_declare", json_encode($post));
  }
  function getDrugs($pid){
    global $log;
    if(!checkAccess("treatment", "dbGet")) return false;
    $stmt=$this->prepare("SELECT rowid,* FROM treatment WHERE pid=:pid ORDER BY omit,start;");
    $stmt->bindValue(":pid", $pid);
    $result=$stmt->execute();
    return($result);
  }
  function getAdminister($id){
    global $log;
    if(!checkAccess("nursing", "dbGet")) return false;
    $stmt=$this->prepare("SELECT rowid,administer FROM treatment WHERE rowid=:id;");
    $stmt->bindValue(":id", $id);
    $result=$stmt->execute();
    return($result);
  }
  function getRequisitions($pid){
    global $log;
    if(!checkAccess("requisition", "dbGet")) return false;
    $stmt=$this->prepare("SELECT rowid,* FROM requisition WHERE pid=:pid AND status!=:status ORDER BY room;");
    $stmt->bindValue(":pid", $pid);
    $stmt->bindValue(":status", "done");
    $result=$stmt->execute();
    return($result);
  }
  function getAdvice($pid){
    global $log;
    if(!checkAccess("discharge", "dbGet")) return false;
    $stmt=$this->prepare("SELECT rowid,* FROM discharge WHERE pid=:pid;");
    $stmt->bindValue(":pid", $pid);
    $result=$stmt->execute();
    return($result);
  }
  function getDeath($pid){
    global $log;
    if(!checkAccess("discharge", "dbGet")) return false;
    $stmt=$this->prepare("SELECT data FROM death WHERE pid=:pid;");
    $stmt->bindValue(":pid", $pid);
    $result=$stmt->execute();
    return($result);
  }
  function getName($pid){
    global $log;
    if(!checkAccess("info", "dbGet")) return false;
    $stmt=$this->prepare("SELECT name FROM patients WHERE pid=:pid;");
    $stmt->bindValue(":pid", $pid);
    $result=$stmt->execute();
    return($result);
  }
  function getAge($pid){
    global $log;
    if(!checkAccess("info", "dbGet")) return false;
    $stmt=$this->prepare("SELECT age FROM patients WHERE pid=:pid;");
    $stmt->bindValue(":pid", $pid);
    $result=$stmt->execute();
    return($result);
  }
  function getSex($pid){
    global $log;
    if(!checkAccess("info", "dbGet")) return false;
    $stmt=$this->prepare("SELECT sex FROM patients WHERE pid=:pid;");
    $stmt->bindValue(":pid", $pid);
    $result=$stmt->execute();
    return($result);
  }
  function getWard($pid){
    global $log;
    if(!checkAccess("info", "dbGet")) return false;
    $stmt=$this->prepare("SELECT ward FROM patients WHERE pid=:pid;");
    $stmt->bindValue(":pid", $pid);
    $result=$stmt->execute();
    return($result);
  }
  function getBed($pid){
    global $log;
    if(!checkAccess("info", "dbGet")) return false;
    $stmt=$this->prepare("SELECT bed FROM patients WHERE pid=:pid;");
    $stmt->bindValue(":pid", $pid);
    $result=$stmt->execute();
    return($result);
  }
  function getStatus($pid){
    global $log;
    if(!checkAccess("info", "dbGet")) return false;
    $stmt=$this->prepare("SELECT status FROM patients WHERE pid=:pid;");
    $stmt->bindValue(":pid", $pid);
    $result=$stmt->execute();
    return($result);
  }
  function getDiagnosis($pid){
    global $log;
    if(!checkAccess("diagnosis", "dbGet")) return false;
    $stmt=$this->prepare("SELECT diagnosis FROM patients WHERE pid=:pid;");
    $stmt->bindValue(":pid", $pid);
    $result=$stmt->execute();
    return($result);
  }
  function getPatientList(){
    global $log;
    if(!checkAccess("info", "dbGet")) return false;
    $stmt=$this->prepare("SELECT pid,ward,bed,name,diagnosis,status FROM patients ORDER BY admission;");
    $result=$stmt->execute();
    return($result);
  }
  function getAdmittedPatientList(){
    global $log;
    if(!checkAccess("info", "dbGet")) return false;
    $stmt=$this->prepare("SELECT pid,ward,bed,name,diagnosis FROM patients WHERE status='admitted' ORDER BY UPPER(ward),bed;");
    $result=$stmt->execute();
    return($result);
  }
  function getRequisitionList(){
    global $log;
    if(!checkAccess("requisition", "dbGet")) return false;
    $stmt=$this->prepare("SELECT requisition.rowid,requisition.* FROM requisition INNER JOIN patients ON requisition.pid=patients.pid WHERE requisition.status!=:status AND patients.status=:admitted ORDER BY requisition.room,requisition.test;");
    $stmt->bindValue(":status", "done");
    $stmt->bindValue(":admitted", "admitted");
    $result=$stmt->execute();
    return($result);
  }
  function getForm($id){
    global $log;
    if(!checkAccess("report", "dbGet")) return false;
    $stmt=$this->prepare("SELECT form FROM reports WHERE rowid=:id;");
    $stmt->bindValue(":id", $id);
    $result=$stmt->execute();
    return($result);
  }
  function getAdmission($pid){
    global $log;
    if(!checkAccess("admission", "dbGet")) return false;
    $stmt=$this->prepare("SELECT admission FROM patients WHERE pid=:pid;");
    $stmt->bindValue(":pid", $pid);
    $result=$stmt->execute();
    return($result);
  }
  function getAdmissionData($pid){
    global $log;
    if(!checkAccess("admission", "dbGet")) return false;
    $stmt=$this->prepare("SELECT data FROM patients WHERE pid=:pid;");
    $stmt->bindValue(":pid", $pid);
    $result=$stmt->execute();
    return($result);
  }
  function getDeparture($pid){
    global $log;
    if(!checkAccess("admission", "dbGet")) return false;
    $stmt=$this->prepare("SELECT departure FROM patients WHERE pid=:pid;");
    $stmt->bindValue(":pid", $pid);
    $result=$stmt->execute();
    return($result);
  }
  function getSummary($pid){
    global $log;
    if(!checkAccess("summary", "dbGet")) return false;
    $stmt=$this->prepare("SELECT summary FROM patients WHERE pid=:pid;");
    $stmt->bindValue(":pid", $pid);
    $result=$stmt->execute();
    return($result);
  }
  function getHistory($pid){
    global $log;
    if(!checkAccess("history", "dbGet")) return false;
    $stmt=$this->prepare("SELECT history FROM patients WHERE pid=:pid;");
    $stmt->bindValue(":pid", $pid);
    $result=$stmt->execute();
    return($result);
  }
  function getData($pid, $id, $cat){
    global $log;
    if($cat=="physician"){
      if(!checkAccess("physician", "dbGet")) return false;
      $stmt=$this->prepare("SELECT data FROM physician WHERE pid=:pid AND rowid=:id ORDER BY time DESC;");
    } elseif($cat=="nursing"){
      if(!checkAccess("nursing", "dbGet")) return false;
      $stmt=$this->prepare("SELECT data FROM nursing WHERE pid=:pid AND rowid=:id ORDER BY time DESC;");
    } elseif($cat=="reports"){
      if(!checkAccess("report", "dbGet")) return false;
      $stmt=$this->prepare("SELECT form,data FROM reports WHERE pid=:pid AND rowid=:id ORDER BY time DESC;");
    } else{
      return(false);
    }
    $stmt->bindValue(":pid", $pid);
    $stmt->bindValue(":id", $id);
    $result=$stmt->execute();
    return($result);
  }
  function getAllData($pid, $cat){
    global $log;
    if($cat=="physician"){
      if(!checkAccess("physician", "dbGet")) return false;
      $stmt=$this->prepare("SELECT rowid,data FROM physician WHERE pid=:pid ORDER BY time DESC;");
    } elseif($cat=="nursing"){
      if(!checkAccess("nursing", "dbGet")) return false;
      $stmt=$this->prepare("SELECT rowid,data FROM nursing WHERE pid=:pid ORDER BY time DESC;");
    } elseif($cat=="reports"){
      if(!checkAccess("report", "dbGet")) return false;
      $stmt=$this->prepare("SELECT rowid,form,data FROM reports WHERE pid=:pid ORDER BY time DESC;");
    } elseif($cat=="info"){
      if(!checkAccess("info", "dbGet")) return false;
      $stmt=$this->prepare("SELECT rowid,data FROM patients WHERE pid=:pid ORDER BY time DESC;");
    } elseif($cat=="history"){
      if(!checkAccess("history", "dbGet")) return false;
      $stmt=$this->prepare("SELECT rowid,history FROM patients WHERE pid=:pid ORDER BY time DESC;");
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
