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
  if(!empty($_POST["delete"])){
    $db->deleteAdvice($_POST["delete"]);
  }
  elseif(!empty($_POST["name"])){
    $db->addAdvice($pid, $_POST["name"], $_POST["dose"], $_POST["route"], $_POST["frequency"], $_POST["duration"], $_POST["extra-note"]);
  }
  $list=$db->getAdvice($pid);
  $view="<form method='post' id='delete'></form>";
  $view=$view."<table class='table'>";
  $view=$view."<tr><th>Drug</th><th>Dose</th><th>Route</th><th>Frequency</th><th>Duration</th><th>Note</th></tr>";
  while($drug=$list->fetchArray()){
    $view=$view."<tr><td>".$drug["name"]."</td><td>".$drug["dose"]."</td><td>".$drug["route"]."</td><td>".$drug["frequency"]."</td><td>".$drug["duration"]."</td><td>".$drug["addl"]."</td><td><button class='btn btn-warning' name='delete' value='".$drug["rowid"]."' form='delete'>Delete</button></td></tr>";
  }
  $view=$view."</table>";
  $form=schema2form("forms/drugs.schema.json");
}
?>
<!DOCTYPE html>
<html>
  <head>
    <?php include("lib/head.php");?>
    <title>Discharge Notes</title>
  </head>
  <body>
    <div class="container">
      <div class="card mb-4">
        <div class="card-body">
          <h4 class="card-title">Medicine List</h4>
          <?php echo $view;?>
        </div>
      </div>
      <?php echo $form;?>
    </div>
    <?php include("lib/foot.php");?>
  </body>
</html>
