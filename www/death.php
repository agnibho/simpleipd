<?php
require(dirname(__DIR__)."/require.php");
if(checkAccess("death")!="all"){
  header("Location: error.php");
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
    <?php include(CONFIG_LIB."head.php");?>
    <title>Death Note</title>
  </head>
  <body>
    <div class="container">
      <?php echo getInfo($pid);?>
      <?php echo str_replace("Save", "Declare Death", $form);?>
    </div>
    <?php include(CONFIG_LIB."foot.php");?>
  </body>
</html>
