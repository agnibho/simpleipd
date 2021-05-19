<?php
require(dirname(__DIR__)."/require.php");
$list=$db->getPatientList();
$showList="";
while($arr=$list->fetchArray()){
  $pid=$arr["pid"];
  $showList=$showList."<tr><td><a href='view.php?pid=".$pid."'>".$pid."</a></td><td>".$arr["ward"]."-".$arr["bed"]."</td><td>".$arr["name"]."</td><td>".$arr["diagnosis"]."</tr>";
}
$reqs=$db->getRequisitionList();
$showReqs="";
while($arr=$reqs->fetchArray()){
  $pid=$arr["pid"];
  if(!empty($arr["form"])){
    if($arr["form"]=="report-cs"){
      $test="<a href='vitek.php?pid=".$pid."&form=".$arr["form"]."&req=".$arr["rowid"]."&src=index'>".$arr["test"]."</a>";
    }
    else{
      $test="<a href='report.php?pid=".$pid."&form=".$arr["form"]."&req=".$arr["rowid"]."&src=index'>".$arr["test"]."</a>";
    }
  }
  else{
    $test="<a href='report.php?pid=".$pid."&form=report-other&req=".$arr["rowid"]."&src=index'>".$arr["test"]."</a>";
  }
  $showReqs=$showReqs."<tr><td>".$test."</td><td>".$arr["sample"]."</td><td>".$arr["room"]."</td><td>".date("M j, Y", $arr["time"])."</td><td><a href='view.php?pid=".$pid."' target='_blank'>".$pid."</a></td></tr>";
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
      <h1><?php echo CONFIG_TITLE;?></h1>
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Patient List</h4>
          <table class="table">
            <tr><th>Patient ID</th><th>Bed Number</th><th>Name</th><th>Diagnosis</th></tr>
            <?php echo $showList;?>
          </table>
          <a class="btn btn-primary btn-lg" href="admission.php">Add New Patient</a>
        </div>
      </div>
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Requisition List</h4>
          <table class="table">
            <tr><th>Test Needed</th><th>Sample</th><th>Place</th><th>Date</th><th>Patient ID</th></tr>
            <?php echo $showReqs;?>
          </table>
        </div>
      </div>
    </div>
    <?php include(CONFIG_LIB."foot.php");?>
  </body>
</html>
