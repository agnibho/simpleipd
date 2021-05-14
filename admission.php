<?php
require("lib/functions.php");
require("lib/db.php");
session_start();
if(empty($_SESSION["user"])){
  header("Location: login.php");
  exit();
}
session_start();
if(empty($_SESSION["user"])){
  header("Location: login.php");
  exit();
}
if(!empty($_POST["pid"]) && !empty($_POST["name"])){
  $db->admit($_POST);
  //header("Location: view.php?pid=".$_POST["pid"]);
  //exit();
}
if(!empty($_GET["pid"])){
  $form=schema2form("forms/admission.schema.json", $_GET["pid"]);
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
    <?php include("lib/foot.php");?>
  </body>
</html>
