<?php
require(dirname(__DIR__)."/require.php");
if(checkAccess("report")!="all"){
  header("Location: error.php");
  exit();
}
if(!empty($_GET["pid"]) && !empty($_GET["form"])){
  $pid=$_GET["pid"];
  if(!empty($_POST["date"])){
    if(!empty($_GET["id"])){
      $db->editReport($_POST, $pid, $_GET["id"], $_POST["form"]);
    }
    else{
      $db->addReport($_POST, $pid, $_POST["form"]);
    }
    if(!empty($_GET["req"])){
      $db->omitRequisition($_GET["req"]);
    }
    if(!empty($_GET["src"]) && $_GET["src"]=="index"){
      header("Location: index.php");
      exit();
    }
    else{
      header("Location: view.php?pid=".$_GET["pid"]);
      exit();
    }
  }
  if(isSet($_GET["id"])){
    $form=schema2form("forms/".$_GET["form"].".schema.json", $pid, $_GET["id"], "reports");
  }
  else{
    $form=schema2form("forms/".$_GET["form"].".schema.json");
  }
}
?>
<!DOCTYPE html>
<html>
  <head>
    <?php include(CONFIG_LIB."head.php");?>
    <title>Reports</title>
  </head>
  <body>
    <div class="container">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Add New Report</h4>
          <?php echo $form;?>
          <?php if(!empty($_GET["req"])){echo "<p class='text-right'><a href='attachments.php?pid=".$pid."&req=".$_GET["req"]."'>Upload PDF instead</a></p>";}?>
        </div>
      </div>
    </div>
    <?php include(CONFIG_LIB."foot.php");?>
  </body>
</html>
