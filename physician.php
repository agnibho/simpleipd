<?php
require("lib/db.php");
require("lib/functions.php");
session_start();
if(empty($_SESSION["user"])){
  header("Location: login.php");
  exit();
}
if(!empty($_GET["pid"])){
  $pid=$_GET["pid"];
  if(!empty($_POST["date"]) && !empty($_POST["time"])){
    if(!empty($_GET["id"])){
      $db->editPhysician($_POST, $pid, $_GET["id"]);
    }
    else{
      $db->addPhysician($_POST, $pid);
    }
    //header("Location: view.php?id=".$_GET["id"]);
    //exit();
  }
  if(isSet($_GET["id"])){
    $form=schema2form("forms/physician.schema.json", $pid, $_GET["id"], "clinical");
  }
  else{
    $form=schema2form("forms/physician.schema.json");
  }
}
?>
<!DOCTYPE html>
<html>
  <head>
    <?php include("lib/head.php");?>
    <title>Physician Notes</title>
  </head>
  <body>
    <div class="container">
      <?php echo getInfo($pid);?>
      <?php echo $form;?>
    </div>
    <?php include("lib/foot.php");?>
  </body>
</html>
