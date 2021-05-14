<?php
require("lib/functions.php");
require("lib/db.php");
if(!empty($_POST["pid"]) && !empty($_POST["name"]) && !empty($_POST["age"]) && !empty($_POST["sex"])){
  $db->admit($_POST);
  //header("Location: view.php?pid=".$_POST["pid"]);
  //exit();
}
$form=schema2form("forms/admission.schema.json");
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
