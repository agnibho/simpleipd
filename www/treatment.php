<?php
require(dirname(__DIR__)."/require.php");
if(!empty($_GET["pid"])){
  $pid=$_GET["pid"];
  if(!empty($_POST["give"])){
    $administer=$db->getAdminister($pid)->fetchArray();
    if(!empty($administer["administer"])){
      $given=(array)json_decode($administer["administer"]);
    }
    else{
      $given=[];
    }
    array_push($given, strtotime($_POST["date"]." ".$_POST["time"]));
    $db->giveDrug($_POST["give"], json_encode($given));
  }
  elseif(!empty($_POST["omit"])){
    $db->omitDrug($_POST["omit"], $_POST["date"], $_POST["time"]);
  }
  elseif(!empty($_POST["drug"])){
    $db->addDrug($pid, $_POST["drug"], $_POST["dose"], $_POST["route"], $_POST["frequency"], $_POST["date"], $_POST["time"], $_POST["duration"], $_POST["extra_note"]);
  }
  $list=$db->getDrugs($pid);
  $view="";
  while($drug=$list->fetchArray()){
    if($drug["omit"]){
      $omit="omit";
    }
    else{
      $omit="";
      //try{
      //  if($drug["start"]+$drug["duration"]*24*3600<time()){
      //    $db->omitDrug($drug["rowid"]);
      //    $omit="omit";
      //  }
      //} catch(TypeError $e){}
    }
    if(!empty($drug["administer"])){
      $administer=json_decode($drug["administer"]);
      $last=date("H:i", end($administer));
    }
    else{
      $last="";
    }
    $view=$view."<tr class='".$omit."'><td>".$drug["drug"]."</td><td>".$drug["dose"]."</td><td>".$drug["route"]."</td><td>".$drug["frequency"]."</td><td>".date("M j", $drug["start"])."</td><td>".$drug["duration"]."</td><td>".$drug["addl"]."</td><td>".$last."</td><td><button type='submit' class='btn btn-success' name='give' value='".$drug["rowid"]."' form='administer' ".$omit." ".checkAccess("nursing", "form").">Give</button></td><td><button type='submit' class='btn btn-warning confirm' name='omit' value='".$drug["rowid"]."' form='omitter' ".$omit." ".checkAccess("treatment", "form").">Omit</button></td></tr>";
  }
  $view=$view."</table>";
  $form=schema2form("forms/drugs.schema.json");
  if(checkAccess("treatment")=="all" && $db->getStatus($pid)->fetchArray()["status"]=="admitted"){
    $hideForm="";
  }
  else{
    $hideForm="style='display:none'";
  }
}
?>
<!DOCTYPE html>
<html>
  <head>
    <?php include(CONFIG_LIB."head.php");?>
    <title>Treatment</title>
  </head>
  <body>
    <div class="container">
      <?php include(CONFIG_LIB."top.php");?>
      <div class="card mb-4">
        <div class="card-body">
          <h4 class="card-title">Medicine List</h4>
          <form method='post' id='omitter'>
            <input type="hidden" name="date">
            <input type="hidden" name="time">
          </form>
          <form method='post' id='administer'>
            <input type="hidden" name="date">
            <input type="hidden" name="time">
          </form>
          <table class="table">
            <tr><th>Drug</th><th>Dose</th><th>Route</th><th>Frequency</th><th>Start</th><th>Duration</th><th>Note</th><th>Given</th><th></th><th></th></tr>
            <?php echo $view;?>
          </table>
        </div>
      </div>
      <div <?php echo $hideForm;?>>
        <?php echo $form;?>
      </div>
    </div>
    <?php include(CONFIG_LIB."foot.php");?>
  </body>
</html>
