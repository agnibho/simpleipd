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
  if(!empty($_POST["cc"])){
    $db->updateHistory($_POST, $pid);
    //header("Location: view.php?pid=".$pid);
    //exit();
  }
}
if(!empty($_GET["pid"])){
  $form=schema2form("forms/history.schema.json", $_GET["pid"]);
}
else{
  $form=schema2form("forms/history.schema.json");
}
?>
<!DOCTYPE html>
<html>
  <head>
    <?php include("lib/head.php");?>
    <title>History</title>
  </head>
  <body>
    <div class="container">
      <?php echo getInfo($pid);?>
      <?php echo $form;?>
    </div>
    <?php include("lib/foot.php");?>
  </body>
</html>
