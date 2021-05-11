<?php
require("lib/functions.php");
if(isSet($_GET["id"]) && isSet($_POST["date"]) && isSet($_POST["time"])){
  $data=json_encode($_POST);
  if(!is_dir("data/".$_GET["id"]."/clinical")){
    mkdir("data/".$_GET["id"]."/clinical");
  }
  file_put_contents("data/".$_GET["id"]."/clinical/".$_POST["stamp"].".json", $data);
  header("Location: view.php?id=".$_GET["id"]);
  exit();
}
if(isSet($_GET["stamp"])){
    $form=schema2form("forms/clinical.schema.json", $_GET["stamp"], "data/".$_GET["id"]."/clinical/".$_GET["stamp"].".json");
}
else{
  $form=schema2form("forms/clinical.schema.json");
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
      <?php echo $form;?>
    </div>
  </body>
</html>
