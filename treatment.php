<?php
require("lib/db.php");
require("lib/functions.php");
if(!empty($_GET["pid"])){
  $pid=$_GET["pid"];
  if(!empty($_POST["omit"])){
    $db->omitDrug($_POST["omit"]);
  }
  elseif(!empty($_POST["name"])){
    $db->addDrug($pid, $_POST["name"], $_POST["dose"], $_POST["route"], $_POST["frequency"], $_POST["start"], $_POST["duration"], $_POST["note"]);
  }
  $list=$db->getDrugs($pid);
  $view="<form method='post' id='omitter'></form>";
  $view=$view."<table class='table'>";
  $view=$view."<tr><th>Drug</th><th>Dose</th><th>Route</th><th>Frequency</th><th>Start</th><th>Duration</th><th>Note</th></tr>";
  while($drug=$list->fetchArray()){
    if($drug["omit"]){
      $omit="style='display:none'";
    }
    else{
      $omit="";
    }
    $view=$view."<tr><td>".$drug["name"]."</td><td>".$drug["dose"]."</td><td>".$drug["route"]."</td><td>".$drug["frequency"]."</td><td>".$drug["start"]."</td><td>".$drug["duration"]."</td><td>".$drug["addl"]."</td><td><button class='btn btn-warning' name='omit' value='".$drug["rowid"]."' form='omitter' ".$omit.">Omit</button></td></tr>";
  }
  $view=$view."</table>";

}
?>
<!DOCTYPE html>
<html>
  <head>
    <?php include("lib/head.php");?>
    <title>Clinical Notes</title>
  </head>
  <body>
    <div class="container">
      <div class="card mb-4">
        <div class="card-body">
          <h4 class="card-title">Medicine List</h4>
          <?php echo $view;?>
        </div>
      </div>
      <form method="post">
        <div>
          <label class="form-label h4" for="drug">Add Drug</label>
          <div class="row">
            <div class="col-sm-6 mb-3">
              <input class="form-control" type="text" name="name" id="drug" placeholder="Drug" required>
            </div>
            <div class="col-sm-2 mb-3">
              <input class="form-control" type="text" name="dose" id="dose" placeholder="Dose">
            </div>
            <div class="col-sm-2 mb-3">
              <select class="form-control" name="route" id="route">
                <option value="oral">Oral</option>
                <option value="im">IM</option>
                <option value="iv">IV</option>
                <option value="sc">SubCut</option>
                <option value="infusion">Infusion</option>
                <option value="transfusion">Transfusion</option>
                <option value="nebulization">Nebulization</option>
                <option value="inhalation">Inhalation</option>
                <option value="pr">Per Rectal</option>
              </select>
            </div>
            <div class="col-sm-2 mb-3">
              <input class="form-control" type="text" name="frequency" id="frequency" placeholder="Frequency">
            </div>
            <div class="col-sm-2 mb-3">
              <input class="form-control" type="text" name="start" id="start" placeholder="Start">
            </div>
            <div class="col-sm-2 mb-3">
              <input class="form-control" type="text" name="duration" id="duration" placeholder="Duration">
            </div>
            <div class="col-sm-8 mb-3">
              <input class="form-control" type="text" name="note" id="note" placeholder="Notes">
            </div>
          </div>
          <button class="btn btn-primary" type="submit">Update</button>
        </div>
      </form>
    </div>
  </body>
</html>
