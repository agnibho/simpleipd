<?php
require("lib/db.php");
require("lib/functions.php");
session_start();
if(empty($_SESSION["user"])){
  header("Location: login.php");
  exit();
}
if(!empty($_GET["pid"]) && !empty($_GET["form"])){
  $pid=$_GET["pid"];
  if(!empty($_POST["date"])){
    if(!empty($_GET["id"])){
      $db->editReport($_POST, $pid, $_GET["id"], $_POST["form"]);
    }
    else{
      $db->addReport($_POST, $pid, $_POST["form"]);
    }
    //header("Location: view.php?id=".$_GET["id"]);
    //exit();
  }
  if(isSet($_GET["id"])){
    $form=schema2form("forms/".$_GET["form"].".schema.json", $pid, $_GET["id"], "reports");
  }
  else{
    $form=schema2form("forms/".$_GET["form"].".schema.json");
  }
}
?>
<!DOCTYPE html>
<html>
  <head>
    <?php include("lib/head.php");?>
    <title>Reports</title>
  </head>
  <body>
    <div class="container">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Add New Report</h4>
          <?php echo $form;?>
        </div>
      </div>
    </div>
    <?php include("lib/foot.php");?>
  </body>
</html>
