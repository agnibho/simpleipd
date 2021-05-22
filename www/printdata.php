<?php
require(dirname(__DIR__)."/require.php");
$physician=[];
$nursing=[];
$reports=[];
$treatments="<h4>Treatment Received</h4><ol>";
$discharge="<h4>Advice on Discharge</h4><ol>";
$death="<h4>Medical Cause of Death</h4>";
if($_GET["pid"]){
  $pid=$_GET["pid"];
  $status=$db->getStatus($pid)->fetchArray()["status"];
  $info=getInfo($pid);
  $history=viewData($db->getHistory($pid)->fetchArray()["history"]);
  $physicianArray=$db->getAllData($pid, "physician");
  while($c=$physicianArray->fetchArray()){
    array_push($physician, viewData($c["data"]));
  }
  $nursingArray=$db->getAllData($pid, "nursing");
  while($c=$nursingArray->fetchArray()){
    array_push($nursing, viewData($c["data"]));
  }
  $reportsArray=$db->getAllData($pid, "reports");
  while($r=$reportsArray->fetchArray()){
    if(in_array($r["form"], ["report-as-grampos", "report-as-gramneg", "report-as-fungal"])){
      array_push($reports, viewAntibiogram($r["data"]));
    }
    else{
      array_push($reports, viewData($r["data"]));
    }
  }
  $treatmentArray=$db->getDrugs($pid);
  while($t=$treatmentArray->fetchArray()){
    $start="";
    if(!empty($t["start"])){
      $start="from ".date("M j, Y", $t["start"]);
    }
    $end="";
    if(!empty($t["end"])){
      $end="till ".date("M j, Y", $t["end"]);
    }
    $addl="";
    if(!empty($t["addl"])){
      $addl="(".$t["addl"].")";
    }
    $treatments=$treatments."<li>".$t["drug"]." ".$t["dose"]." ".$t["route"]." ".$t["frequency"]." ".$start." ".$end." ".$addl;
  }
  $dischargeArray=$db->getAdvice($pid);
  while($t=$dischargeArray->fetchArray()){
    $discharge=$discharge."<li>".$t["drug"]." ".$t["dose"]." ".$t["route"]." ".$t["frequency"]." for ".$t["duration"]." ".$t["addl"];
  }
  $deathArray=$db->getDeath($pid);
  while($d=$deathArray->fetchArray()){
    $death=$death.viewData($d["data"]);
  }
}
$treatments=$treatments."</ol>";
$discharge=$discharge."</ol>";
?>
<!DOCTYPE html>
<html>
  <head>
    <?php include(CONFIG_LIB."head.php");?>
    <title>Print Data</title>
  </head>
  <body>
    <div class="container">
      <h2><?php echo CONFIG_TITLE;?></h2>
      <h4><?php echo "Patient Record";?></h4>
      <?php echo $info;?>
      <?php echo "<p><strong>Status: </strong>".$status."</p>";?>
      <?php echo $history;?>
      <?php foreach($physician as $p) echo $p;?>
      <?php foreach($nursing as $n) echo $n;?>
      <?php foreach($reports as $r) echo $r;?>
      <hr>
      <?php echo $treatments;?>
      <hr>
      <?php if($status=="expired"){ echo $death; } else { echo $discharge; }?>
      <hr>
      <?php echo "Retrieved on: ".date("M d, Y H:i T", time());?>
    </div>
  </body>
</html>
