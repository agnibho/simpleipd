<?php
require("lib/db.php");
require("lib/functions.php");
if(!empty($_GET["pid"])){
  $pid=$_GET["pid"];
  if(!empty($_POST["cc"])){
    $db->history($_POST, $pid);
    //header("Location: view.php?pid=".$pid);
    //exit();
  }
}
if(isSet($_GET["stamp"])){
    $form=schema2form("forms/clinical.schema.json", $_GET["stamp"], "data/".$_GET["id"]."/clinical/".$_GET["stamp"].".json");
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
  </body>
</html>
