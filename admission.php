<?php
require("lib/functions.php");
if(isSet($_POST["id"])){
  $data=json_encode($_POST);
  if(!is_dir("data/".$_POST["id"])){
    mkdir("data/".$_POST["id"]);
  }
  file_put_contents("data/".$_POST["id"]."/info.json", $data);
  header("Location: view.php?id=".$_POST["id"]);
  exit();
}
if(isSet($_GET["id"])){
  $form=schema2form("forms/admission.schema.json", "data/".$_GET["id"]."/info.json");
}
else{
  $form=schema2form("forms/admission.schema.json");
}
?>
<!DOCTYPE html>
<html>
  <head>
    <?php include("lib/head.php");?>
    <title>Admission</title>
  </head>
  <body>
    <div class="container">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">New Patient Information</h4>
          <?php echo $form;?>
        </div>
      </div>
    </div>
  </body>
</html>
