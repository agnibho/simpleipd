<?php
require(dirname(__DIR__)."/require.php");
if(!empty($_GET["pid"])){
  $pid=$_GET["pid"];
  if(empty($_POST["intake"]) && empty($_POST["output"])){
    if(!empty($_POST["io_from"])) $_POST["io_from"]="";
    if(!empty($_POST["io_to"])) $_POST["io_to"]="";
  }
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
  $all=$db->getAllData($pid, "nursing");
  $io="";
  $lastIO="";
  while($io=="" && $a=$all->fetchArray()){
    $d=json_decode($a["data"]);
    $io=$d->intake.$d->output;
    $lastIO=$d->io_to;
  }
  if($lastIO==""){
    $d=$db->getAdmission($pid)->fetchArray();
    $dt=new DateTime();
    $dt->setTimestamp($d["admission"]);
    $lastIO=$dt->format("H:i");
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
    <input type="hidden" id="io_to_val" name="lastIO" value="<?php echo $lastIO;?>">
    <div class="container">
      <?php include(CONFIG_LIB."top.php");?>
      <?php echo getInfo($pid);?>
      <div <?php echo checkAccess("nursing", "form");?>>
        <?php echo $form;?>
      </div>
    </div>
    <?php include(CONFIG_LIB."foot.php");?>
  </body>
</html>
