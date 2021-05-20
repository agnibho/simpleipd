<?php
require(dirname(__DIR__)."/require.php");
if(!empty($_GET["pid"])){
  $pid=$_GET["pid"];
  if(!empty($_POST["date"]) && !empty($_POST["time"])){
    if(!empty($_GET["id"])){
      $db->editPhysician($_POST, $pid, $_GET["id"]);
    }
    else{
      $db->addPhysician($_POST, $pid);
    }
    header("Location: view.php?pid=".$_GET["pid"]);
    exit();
  }
  if(isSet($_GET["id"])){
    $form=schema2form("forms/physician.schema.json", $pid, $_GET["id"], "physician");
  }
  else{
    $form=schema2form("forms/physician.schema.json");
  }
}
?>
<!DOCTYPE html>
<html>
  <head>
    <?php include(CONFIG_LIB."head.php");?>
    <title>Physician Notes</title>
  </head>
  <body>
    <div class="container">
      <?php include(CONFIG_LIB."top.php");?>
      <?php echo getInfo($pid);?>
      <div <?php echo checkAccess("physician", "form");?>>
        <?php echo $form;?>
      </div>
    </div>
    <?php include(CONFIG_LIB."foot.php");?>
  </body>
</html>
