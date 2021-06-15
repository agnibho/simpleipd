<?php
require(dirname(__DIR__)."/require.php");
if(!empty($_GET["pid"])){
  $pid=$_GET["pid"];
  if(!empty($_POST["give"])){
    $administer=$db->getAdminister($_POST["give"])->fetchArray();
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
  elseif(!empty($_POST["delete"])){
    $db->deleteDrug($_POST["delete"]);
  }
  elseif(!empty($_POST["diet"])){
    $db->advice($_POST, $pid);
  }
  elseif(!empty($_POST["drug"])){
    $db->addDrug($pid, $_POST["drug"], $_POST["dose"], $_POST["route"], $_POST["frequency"], $_POST["date"], $_POST["time"], $_POST["duration"], $_POST["extra_note"]);
  }
  $advice=$db->getAdvice($pid)->fetchArray();
  if(!empty($advice["data"])){
    $advice=$advice["data"];
  }
  $list=$db->getDrugs($pid);
  $view="";
  if(checkAccess("treatment")=="all" && $db->getStatus($pid)->fetchArray()["status"]=="admitted"){
    $hideEdit="";
  }
  else{
    $hideEdit="style='display:none'";
  }
  while($drug=$list->fetchArray()){
    if($drug["omit"]){
      $omit="omit";
    }
    else{
      $omit="nomit";
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
    if(!empty($drug["end"])){
      $end=" to ".date("M j", $drug["end"]);
    }
    else{
      $end="";
    }
    if(filter_var($drug["duration"], FILTER_VALIDATE_INT)){
      $drug["duration"]=$drug["duration"]. " days";
    }
    $view=$view."<tr class='".$omit." drug-entry' data-drug='".$drug["drug"]."' data-dose='".$drug["dose"]."' data-route='".$drug["route"]."' data-frequency='".$drug["frequency"]."' data-duration='".$drug["duration"]."' data-addl='".$drug["addl"]."'><td>".$drug["drug"]."</td><td>".$drug["dose"]."</td><td>".$drug["route"]."</td><td>".$drug["frequency"]."</td><td>".date("M j", $drug["start"]).$end."</td><td>".$drug["duration"]."</td><td>".$drug["addl"]."</td><td>".$last."</td><td><button type='submit' class='btn btn-success nomit confirm' name='give' value='".$drug["rowid"]."' form='administer' ".$omit." ".checkAccess("nursing", "form").">Give</button><button type='submit' ".$hideEdit." class='btn btn-warning nomit confirm' name='omit' value='".$drug["rowid"]."' form='omitter' ".$omit." ".checkAccess("treatment", "form").">Omit</button><button type='submit' ".$hideEdit." class='btn btn-secondary omit confirm' name='delete' value='".$drug["rowid"]."' form='delete' ".$omit." ".checkAccess("treatment", "form").">Delete</button></td><td class='copier'></td></tr>";
  }
  $form=schema2form("forms/drugs.schema.json");
  $form2=schema2form("forms/advice.schema.json", null, null, null, json_decode($advice));
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
      <?php echo getInfo($pid);?>
      <div class="card mb-4">
        <div class="card-body">
          <h4 class="card-title">Advice</h4>
          <?php echo viewData($advice);?>
          <a id="to-form-advice" href="#forms" class="btn btn-primary float-right mb-2" <?php echo $hideEdit; ?>>Edit General Measures</a>
        </div>
      </div>
      <div class="card mb-4">
        <div class="card-body">
          <h4 class="card-title">Medicine List</h4>
          <a id="to-form-drug" href="#forms" class="btn btn-primary float-right mb-2" <?php echo $hideEdit; ?>>Add New Drug</a>
          <form method='post' id='omitter'>
            <input type="hidden" name="date">
            <input type="hidden" name="time">
          </form>
          <form method='post' id='administer'>
            <input type="hidden" name="date">
            <input type="hidden" name="time">
          </form>
          <form method='post' id='delete'>
            <input type="hidden" name="date">
            <input type="hidden" name="time">
          </form>
          <table class="table">
            <tr><th>Drug</th><th>Dose</th><th>Route</th><th>Frequency</th><th>Start</th><th>Duration</th><th>Note</th><th>Given</th><th></th><th></th></tr>
            <?php echo $view;?>
          </table>
        </div>
      </div>
      <div <?php echo $hideEdit;?>>
        <ul class="nav nav-tabs" id="form-navs" rold="tablist">
          <li class="nav-item" role="presentation">
            <a class="nav-link active" id="nav-drug" data-toggle="tab" href="#form-drug" role="tab" aria-controls="form-drug" aria-selected="true">Add Drug</a>
          </li>
          <li class="nav-item" role="presentation">
            <a class="nav-link" id="nav-advice" data-toggle="tab" href="#form-advice" role="tab" aria-controls="form-advice" aria-selected="false">General Measures</a>
          </li>
        </ul>
        <div class="tab-content" id="forms">
          <div class="tab-pane show active" id="form-drug" role="tabpanel" aria-labelledby="nav-drug-tab">
            <?php echo $form;?>
          </div>
          <div class="tab-pane" id="form-advice" role="tabpanel" aria-labelledby="nav-advice-tab">
            <?php echo $form2;?>
          </div>
        </div>
      </div>
    </div>
    <?php include(CONFIG_LIB."foot.php");?>
<script>
$(document).ready(function(){
  $("#to-form-drug").click(function(){
    $("#nav-drug").tab("show");
  });
  $("#to-form-advice").click(function(){
    $("#nav-advice").tab("show");
  });
  $(".drug-entry").each(function(){
    if($(this).find("[name=omit]").is(":visible") || $(this).find("[name=delete]").is(":visible")){
      $(this).find(".copier").html("<button class='btn btn-outline-secondary btn-copy'>Copy</button>");
      $(this).on("click", "td>.btn-copy", function(){
        drugEntry=$(this).parent().parent();
        $("#drug").val(drugEntry.data("drug"));
        $("#dose").val(drugEntry.data("dose"));
        $("#route").val(drugEntry.data("route"));
        $("#frequency").val(drugEntry.data("frequency"));
        $("#duration").val(drugEntry.data("duration"));
        $("#addl").val(drugEntry.data("addl"));
        $("#nav-drug").tab("show");
        window.location.hash="form-drug";
      });
    }
  });
});
</script>
  </body>
</html>
