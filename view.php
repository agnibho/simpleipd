<?php
require("lib/db.php");
require("lib/functions.php");
session_start();
if(empty($_SESSION["user"])){
  header("Location: login.php");
  exit();
}
$info="";
$physician=[];
$nursing=[];
$reports=[];
if(isSet($_GET["pid"])){
  $pid=$_GET["pid"];
  $status=$db->getStatus($pid)->fetchArray()["status"];
  $info=viewData($db->getAdmission($pid)->fetchArray()["data"]);
  $history=viewData($db->getHistory($pid)->fetchArray()["history"]);
  $physicianArray=$db->getAllData($pid, "physician");
  while($c=$physicianArray->fetchArray()){
    array_push($physician, viewData($c["data"], "physician.php?pid=".$pid."&id=".$c["rowid"]));
  }
  $nursingArray=$db->getAllData($pid, "nursing");
  while($c=$nursingArray->fetchArray()){
    array_push($nursing, viewData($c["data"], "nursing.php?pid=".$pid."&id=".$c["rowid"]));
  }
  $reportsArray=$db->getAllData($pid, "reports");
  while($r=$reportsArray->fetchArray()){
    array_push($reports, viewData($r["data"], "report.php?pid=".$pid."&id=".$r["rowid"]."&form=".$db->getForm($r["rowid"])->fetchArray()["form"]));
  }
}
?>
<!DOCTYPE html>
<html>
  <head>
    <?php include("lib/head.php");?>
    <title>View Info</title>
  </head>
  <body>
    <div class="container">
      <h1>Patient Data</h1>
      <div class="card">
        <div class="card-body">
            <a class="mb-3 btn btn-secondary" href="admission.php?pid=<?php echo $pid;?>">Edit Information</a>
            <a class="mb-3 btn btn-secondary" href="history.php?pid=<?php echo $pid;?>">Edit History</a>
            <a class="mb-3 btn btn-secondary" href="physician.php?pid=<?php echo $pid;?>">Add Physician Note</a>
            <a class="mb-3 btn btn-secondary" href="nursing.php?pid=<?php echo $pid;?>">Add Nursing Note</a>
            <a class="mb-3 btn btn-secondary" href="laboratory.php?pid=<?php echo $pid;?>">Add Laboratory Report</a>
            <a class="mb-3 btn btn-secondary" href="requisition.php?pid=<?php echo $pid;?>">Add Requisition</a>
        </div>
      </div>
      <div <?php if(empty($pid)) echo "style='display:none'";?>>
        <ul class="nav nav-tabs" id="listtabs" role="tablist">
          <li class="nav-item" role="presentation">
            <a class="nav-link active" id="info-tab" data-toggle="tab" href="#info" role="tab" aria-controls="info" aria-selected="true">Info</a>
          </li>
          <li class="nav-item" role="presentation">
            <a class="nav-link" id="history-tab" data-toggle="tab" href="#history" role="tab" aria-controls="history" aria-selected="false">History</a>
          </li>
          <li class="nav-item" role="presentation">
            <a class="nav-link" id="physician-tab" data-toggle="tab" href="#physician" role="tab" aria-controls="physician" aria-selected="false">Physician Notes</a>
          </li>
          <li class="nav-item" role="presentation">
            <a class="nav-link" id="nursing-tab" data-toggle="tab" href="#nursing" role="tab" aria-controls="clinical" aria-selected="false">Nursing Notes</a>
          </li>
          <li class="nav-item" role="presentation">
            <a class="nav-link" id="report-tab" data-toggle="tab" href="#report" role="tab" aria-controls="report" aria-selected="false">Lab Reports</a>
          </li>
        </ul>
        <div class="tab-content" id="viewtabs">
          <div class="tab-pane fade show active" id="info" role="tabpanel" aria-labelledby="info-tab">
            <div class='card'><div class='card-body'>Status: <?php echo $status;?></div></div>
            <?php echo $info;?>
          </div>
          <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="history-tab">
            <?php echo $history;?>
          </div>
          <div class="tab-pane fade" id="physician" role="tabpanel" aria-labelledby="physician-tab">
            <?php foreach($physician as $p) echo $p;?>
          </div>
          <div class="tab-pane fade" id="nursing" role="tabpanel" aria-labelledby="nursing-tab">
            <?php foreach($nursing as $n) echo $n;?>
          </div>
          <div class="tab-pane fade" id="report" role="tabpanel" aria-labelledby="report-tab">
            <?php foreach($reports as $r) echo $r;?>
          </div>
        </div>
        <hr>
        <div class="row">
          <div class="mb-2 col-md-3" id="treatment" <?php if($info=="") echo "style='display:none'";?>>
            <a class="btn btn-success btn-lg" href="treatment.php?pid=<?php echo $pid;?>">Treatment</a>
          </div>
          <div class="mb-2 col-md-3" id="attachment" <?php if($info=="") echo "style='display:none'";?>>
            <a class="btn btn-primary btn-lg" href="attachments.php?pid=<?php echo $pid;?>">Attachments</a>
          </div>
          <div <?php if($status!="admitted") echo "style='display:none'";?> class="mb-2 col-md-3" id="discharge" <?php if($info=="") echo "style='display:none'";?>>
            <a class="btn btn-warning btn-lg" href="discharge.php?pid=<?php echo $pid;?>">Discharge</a>
          </div>
          <div <?php if($status!="admitted") echo "style='display:none'";?> class="mb-2 col-md-3" id="death" <?php if($info=="") echo "style='display:none'";?>>
            <a class="btn btn-danger btn-lg" href="death.php?pid=<?php echo $pid;?>">Death</a>
          </div>
        </div>
      </div>
    </div>
    <div <?php if(!empty($pid)) echo "style='display:none'";?>>
      <h1>Please enter a valid patient ID</h1>
      <form>
        <input class="form-control" name="pid">
        <button class="form-control" type="submit">View</button>
      </form>
    </div>
    <?php include("lib/foot.php");?>
  </body>
</html>
