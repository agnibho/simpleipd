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
  if(!empty($_POST["date"]) && !empty($_POST["time"]) && !empty($_POST["diagnosis"])){
    $db->setDead($pid, $_POST);
    //header("Location: view.php?id=".$_GET["id"]);
    //exit();
  }
  $form=schema2form("forms/death.schema.json", $pid, null, null, (object)["diagnosis"=>$db->getDiagnosis($pid)->fetchArray()["diagnosis"]]);
}
?>
<!DOCTYPE html>
<html>
  <head>
    <?php include("lib/head.php");?>
    <title>Death Note</title>
  </head>
  <body>
    <div class="container">
      <?php echo getInfo($pid);?>
      <?php echo str_replace("Save", "Declare Death", $form);?>
    </div>
    <?php include("lib/foot.php");?>
  </body>
</html>
