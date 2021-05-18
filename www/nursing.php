<?php
require(dirname(__DIR__)."/require.php");
if(!empty($_GET["pid"])){
  $pid=$_GET["pid"];
  if(!empty($_POST["date"]) && !empty($_POST["time"])){
    if(!empty($_GET["id"])){
      $db->editNursing($_POST, $pid, $_GET["id"]);
    }
    else{
      $db->addNursing($_POST, $pid);
    }
    header("Location: view.php?pid=".$_GET["pid"]);
    exit();
  }
  if(isSet($_GET["id"])){
    $form=schema2form("forms/nursing.schema.json", $pid, $_GET["id"], "nursing");
  }
  else{
    $form=schema2form("forms/nursing.schema.json");
  }
}
?>
<!DOCTYPE html>
<html>
  <head>
    <?php include(CONFIG_LIB."head.php");?>
    <title>Nursing Notes</title>
  </head>
  <body>
    <div class="container">
      <?php echo getInfo($pid);?>
      <div <?php echo checkAccess("nursing", "form");?>>
        <?php echo $form;?>
      </div>
    </div>

    <?php include(CONFIG_LIB."foot.php");?>
  </body>
</html>
