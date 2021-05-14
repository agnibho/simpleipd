<?php
require("lib/db.php");
require("lib/functions.php");
$info="";
$clinical=[];
$reports=[];
if(isSet($_GET["pid"])){
  $pid=$_GET["pid"];
  $info=getInfo($pid);
  $clinicalArray=$db->getAllData($pid, "clinical");
  while($c=$clinicalArray->fetchArray()){
    array_push($clinical, viewData($c["data"], "clinical.php?pid=".$pid."&id=".$c["rowid"]));
  }
  $reportsArray=$db->getAllData($pid, "reports");
  while($r=$reportsArray->fetchArray()){
    var_dump($r);
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
      <div <?php if(empty($pid)) echo "style='display:none'";?>>
        <ul class="nav nav-tabs">
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="#info">Info</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#clinical">Clinical Notes</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#report">Lab Reports</a>
          </li>
        </ul>
        <div id="info">
          <?php echo $info;?>
          <a class="mb-3 btn btn-primary" href="admission.php?pid=<?php echo $pid;?>">Edit Information</a>
          <div id="clinical">
            <?php foreach($clinical as $c) echo $c;?>
            <a class="mb-3 btn btn-primary" href="clinical.php?pid=<?php echo $pid;?>">Add Clinical Note</a>
          </div>
          <div id="report">
            <?php foreach($reports as $r) echo $r;?>
            <a class="mb-3 btn btn-primary" href="laboratory.php?pid=<?php echo $pid;?>">Add Laboratory Report</a>
          </div>
          <div id="treatment" <?php if($info=="") echo "style='display:none'";?>>
            <a class="btn btn-success btn-lg" href="treatment.php?pid=<?php echo $pid;?>">Treatment</a>
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
    </div>
  </body>
</html>
