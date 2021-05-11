<?php
require("lib/functions.php");
if(isSet($_GET["id"]) && isSet($_POST["omit"])){
  if(filter_var($_POST["omit"], FILTER_VALIDATE_INT)!==false){
    omit_drug("data/".$_GET["id"]."/treatment.json", $_POST["omit"]);
  }
}
if(isSet($_GET["id"]) && isSet($_POST["drug"])){
  if($_POST["drug"]!==""){
    add_drug("data/".$_GET["id"]."/treatment.json", $_POST);
  }
}
$list=view_drug("data/".$_GET["id"]."/treatment.json");
?>
<!DOCTYPE html>
<html>
  <head>
    <?php include("lib/head.php");?>
    <title>Clinical Notes</title>
  </head>
  <body>
    <div class="container">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Medicine List</h4>
          <?php echo $list;?>
        </div>
      </div>
      <form method="post">
        <div>
          <label class="form-label" for="drug">Add Drug</label>
          <div class="row">
            <div class="col-sm-4">
              <input class="form-control" type="text" name="drug" id="drug" placeholder="Drug">
            </div>
            <div class="col-sm-2">
              <input class="form-control" type="number" name="dose" id="dose" placeholder="Dose">
            </div>
            <div class="col-sm-2">
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
            <div class="col-sm-2">
              <input class="form-control" type="text" name="freq" id="freq" placeholder="Frequency">
            </div>
            <div class="col-sm-2">
              <input class="form-control" type="text" name="duration" id="duration" placeholder="Duration">
            </div>
          </div>
          <input class="form-control" type="text" name="note" id="note" placeholder="Notes">
          <button class="btn btn-primary" type="submit">Update</button>
        </div>
      </form>
    </div>
  </body>
</html>
