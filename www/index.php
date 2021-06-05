<?php
require(dirname(__DIR__)."/require.php");
if(!empty($_POST["req"])){
  $db->receiveRequisition($_POST["req"]);
}
$list=$db->getAdmittedPatientList();
$showList="";
while($arr=$list->fetchArray()){
  $pid=$arr["pid"];
  $showList=$showList."<tr><td><a href='view.php?pid=".$pid."'>".$pid."</a></td><td>".$arr["ward"]."-".$arr["bed"]."</td><td>".$arr["name"]."</td><td>".$arr["diagnosis"]."</td></tr>";
}
$reqs=$db->getRequisitionList();
$showReqs="";
while($arr=$reqs->fetchArray()){
  $pid=$arr["pid"];
  if(!empty($arr["form"])){
    if($arr["form"]=="report-cs"){
      $test="<a href='vitek.php?pid=".$pid."&form=".$arr["form"]."&req=".$arr["rowid"]."&sample=".$arr["sample"]."&time=".$arr["time"]."&src=index'>".$arr["test"]."</a>";
    }
    else{
      $test="<a href='report.php?pid=".$pid."&form=".$arr["form"]."&req=".$arr["rowid"]."&sample=".$arr["sample"]."&time=".$arr["time"]."&src=index'>".$arr["test"]."</a>";
    }
  }
  else{
    $test="<a href='report.php?pid=".$pid."&form=report-other&req=".$arr["rowid"]."&src=index'>".$arr["test"]."</a>";
  }
  if($arr["status"]=="received"){
    $received="<span class='badge badge-success'>Sample Received</span>";
  }
  elseif(checkAccess("report")=="all"){
    $received="<button class='btn btn-sm btn-outline-danger confirm' form='sample' name='req' value='".$arr["rowid"]."'>Receive Sample</button>";
  }
  else{
    $received="<span class='badge badge-warning'>Sample Not Received</span>";
  }
  $showReqs=$showReqs."<tr><td>".$test."</td><td>".$arr["sample"]."</td><td>".$arr["room"]."</td><td>".date("M j", $arr["time"])."</td><td><a href='view.php?pid=".$pid."' target='_blank'>".$pid." (".$db->getWard($pid)->fetchArray()["ward"]."-".$db->getBed($pid)->fetchArray()["bed"].")</a></td></tr><tr><td></td><td colspan='3'>".$arr["addl"]."</td><td>".$received."</td></tr>";
}
?>
<!DOCTYPE html>
<html>
  <head>
    <?php include(CONFIG_LIB."head.php");?>
    <title>View Info</title>
  </head>
  <body>
    <div class="container">
      <?php include(CONFIG_LIB."top.php");?>
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Patient List</h4>
          <table class="table">
            <tr><th>Patient ID</th><th>Bed Number</th><th>Name</th><th>Diagnosis</th></tr>
            <?php echo $showList;?>
          </table>
          <a class="btn btn-primary btn-lg" href="admission.php">Add New Patient</a>
          <a href="archive.php" class="btn btn-outline-secondary float-right">Archive</a>
        </div>
      </div>
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Requisition List</h4>
          <form id="sample" method="post"></form>
          <table class="table table-striped">
            <tr><th>Test Needed</th><th>Sample</th><th>Place</th><th>Date</th><th>Patient ID</th></tr>
            <?php echo $showReqs;?>
          </table>
        </div>
      </div>
    </div>
    <?php include(CONFIG_LIB."foot.php");?>
  </body>
</html>
