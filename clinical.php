<?php
require("lib/db.php");
require("lib/functions.php");
if(!empty($_GET["pid"])){
  $pid=$_GET["pid"];
  if(!empty($_POST["date"]) && !empty($_POST["time"])){
    if(!empty($_GET["id"])){
      $db->editClinical($_POST, $pid, $_GET["id"]);
    }
    else{
      $db->addClinical($_POST, $pid);
    }
    //header("Location: view.php?id=".$_GET["id"]);
    //exit();
  }
  if(isSet($_GET["id"])){
    $form=schema2form("forms/clinical.schema.json", $pid, $_GET["id"], "clinical");
  }
  else{
    $form=schema2form("forms/clinical.schema.json");
  }
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
      <?php echo getInfo($pid);?>
      <?php echo $form;?>
    </div>
  </body>
</html>
