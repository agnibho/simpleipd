<?php
require(dirname(__DIR__)."/require.php");
if(!empty($_GET["pid"])){
  $pid=$_GET["pid"];
  if(!empty($_POST["omit"])){
    $db->omitDrug($_POST["omit"]);
  }
  elseif(!empty($_POST["drug"])){
    $db->addDrug($pid, $_POST["drug"], $_POST["dose"], $_POST["route"], $_POST["frequency"], $_POST["date"], $_POST["time"], $_POST["duration"], $_POST["extra-note"]);
  }
  $list=$db->getDrugs($pid);
  $view="";
  while($drug=$list->fetchArray()){
    if($drug["omit"]){
      $omit="omit";
    }
    else{
      $omit="";
      try{
        if($drug["start"]+$drug["duration"]*24*3600<time()){
          $db->omitDrug($drug["rowid"]);
          $omit="omit";
        }
      } catch(TypeError $e){}
    }
    $view=$view."<tr class='".$omit."'><td>".$drug["drug"]."</td><td>".$drug["dose"]."</td><td>".$drug["route"]."</td><td>".$drug["frequency"]."</td><td>".date("M j", $drug["start"])."</td><td>".$drug["duration"]."</td><td>".$drug["addl"]."</td><td><button type='submit' class='btn btn-warning' name='omit' value='".$drug["rowid"]."' form='omitter' ".$omit." ".checkAccess("treatment", "form").">Omit</button></td></tr>";
  }
  $view=$view."</table>";
  $form=schema2form("forms/drugs.schema.json");
}
?>
<!DOCTYPE html>
<html>
  <head>
    <?php include(CONFIG_LIB."head.php");?>
    <title>Clinical Notes</title>
  </head>
  <body>
    <div class="container">
      <div class="card mb-4">
        <div class="card-body">
          <h4 class="card-title">Medicine List</h4>
          <form method='post' id='omitter'></form>
          <table class="table">
            <tr><th>Drug</th><th>Dose</th><th>Route</th><th>Frequency</th><th>Start</th><th>Duration</th><th>Note</th><th></th></tr>
            <?php echo $view;?>
          </table>
        </div>
      </div>
      <div <?php echo checkAccess("treatment","form");?>>
        <?php echo $form;?>
      </div>
    </div>
    <?php include(CONFIG_LIB."foot.php");?>
  </body>
</html>
