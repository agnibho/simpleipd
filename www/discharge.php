<?php
require(dirname(__DIR__)."/require.php");
if(!empty($_GET["pid"])){
  $pid=$_GET["pid"];
  if(!empty($_POST["discharge"]) && $_POST["discharge"]=="discharge"){
    $db->setDischarged($pid);
    header("Location: view.php?pid=".$_GET["pid"]);
    exit();
  }
  if(!empty($_POST["delete"])){
    $db->deleteAdvice($_POST["delete"]);
  }
  elseif(!empty($_POST["drug"])){
    $db->addAdvice($pid, $_POST["drug"], $_POST["dose"], $_POST["route"], $_POST["frequency"], $_POST["duration"], $_POST["extra_note"]);
  }
  $list=$db->getAdvice($pid);
  $view="<form method='post' id='delete'></form>";
  $view=$view."<table class='table'>";
  $view=$view."<tr><th>Drug</th><th>Dose</th><th>Route</th><th>Frequency</th><th>Duration</th><th>Note</th></tr>";
  while($drug=$list->fetchArray()){
    $view=$view."<tr><td>".$drug["drug"]."</td><td>".$drug["dose"]."</td><td>".$drug["route"]."</td><td>".$drug["frequency"]."</td><td>".$drug["duration"]."</td><td>".$drug["addl"]."</td><td><button class='btn btn-warning' name='delete' value='".$drug["rowid"]."' form='delete'>Delete</button></td></tr>";
  }
  $view=$view."</table>";
  $form=schema2form("forms/drugs.schema.json");
}
?>
<!DOCTYPE html>
<html>
  <head>
    <?php include(CONFIG_LIB."head.php");?>
    <title>Discharge Notes</title>
  </head>
  <body>
    <div class="container">
      <?php include(CONFIG_LIB."top.php");?>
      <div class="card mb-4">
        <div class="card-body">
          <h4 class="card-title">Medicine List</h4>
          <?php echo $view;?>
        </div>
      </div>
      <div <?php echo checkAccess("discharge", "form");?>>
        <?php echo $form;?>
      </div>
      <form method="post" class="mt-4" <?php echo checkAccess("discharge", "form");?>>
        <input type="hidden" name="discharge" value="discharge">
        <button type="submit" class="btn btn-danger confirm">Discharge Patient</button>
      </form>
    </div>
    <?php include(CONFIG_LIB."foot.php");?>
  </body>
</html>
