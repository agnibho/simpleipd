<?php
require("lib/functions.php");
if(isSet($_GET["id"])){
  $id=$_GET["id"];
}
else{
  $id=false;
}
$info=view_data("data/".$id."/info.json");
$clinical=[];
foreach(glob("data/".$id."/clinical/*.json") as $f){
  array_push($clinical, view_data($f, "clinical.php?id=".$_GET["id"]));
}
$report=[];
foreach(glob("data/".$id."/report/*.json") as $f){
  array_push($report, view_data($f, "report.php?id=".$_GET["id"]));
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
        <a class="mb-3 btn btn-primary" href="admission.php?id=<?php echo $id;?>">Edit Information</a>
        <div id="clinical">
          <?php foreach($clinical as $c) echo $c;?>
          <a class="mb-3 btn btn-primary" href="clinical.php?id=<?php echo $id;?>">Add Clinical Note</a>
        </div>
        <div id="report">
          <?php foreach($report as $r) echo $r;?>
          <a class="mb-3 btn btn-primary" href="laboratory.php?id=<?php echo $id;?>">Add Laboratory Report</a>
        </div>
        <div id="treatment" <?php if($info=="") echo "style='display:none'";?>>
          <a class="btn btn-success btn-lg" href="treatment.php?id=<?php echo $id;?>">Treatment</a>
        </div>
        <div <?php if($info!="") echo "style='display:none'";?>>
          <h1>Please enter a valid patient ID</h1>
          <form>
            <input name="id">
            <button type="submit">View</button>
          </form>
        </div>
      </div>
    </div>
  </body>
</html>
