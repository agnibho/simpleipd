<?php
require("lib/functions.php");
if(isSet($_GET["id"]) && isSet($_GET["form"])){
  if(isSet($_GET["stamp"])){
    $form=schema2form("forms/".$_GET["form"].".schema.json", $_GET["stamp"], "data/".$_GET["id"]."/report/".$_GET["stamp"].".json");
  }
  else{
    $form=schema2form("forms/".$_GET["form"].".schema.json");
  }
}
else{
  $form="";
}
if(isSet($_GET["id"]) && isSet($_POST["date"])){
  $data=json_encode($_POST);
  if(!is_dir("data/".$_GET["id"]."/report")){
    mkdir("data/".$_GET["id"]."/report");
  }
  file_put_contents("data/".$_GET["id"]."/report/".$_POST["stamp"].".json", $data);
  header("Location: view.php?id=".$_GET["id"]);
  exit();
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
  </body>
</html>
