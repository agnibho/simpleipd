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
    $data=$db->getData($pid, $_GET["id"], "reports");
    $data=json_decode($data->fetchArray()["data"]);
  }
  $abx=json_decode(file_get_contents(CONFIG_WWW."autocomplete/vitek.json"));
  if($_GET["form"]=="report-as-grampos"){
    $list=$abx->gram_positive;
  }
  elseif($_GET["form"]=="report-as-gramneg"){
    $list=$abx->gram_negative;
  }
  elseif($_GET["form"]=="report-as-fungal"){
    $list=$abx->fungal;
  }
  $form="<form method='post' id='antibiogram'><input type='hidden' name='form' value='".$_GET["form"]."'></form>";
  $form=$form."<table class='table'>";
  if(!empty($data)){
    $date="value='".$data->date."'";
    $sample="value='".$data->sample."'";
    $labid="value='".$data->labid."'";
    $organism="value='".$data->organism."'";
  }
  elseif(!empty($_GET["time"]) || !empty($_GET["sample"])){
    if(!empty($_GET["time"])){
      $date="value='".date("Y-m-d", $_GET["time"])."'";
    }
    if(!empty($_GET["sample"])){
      $sample="value='".$_GET["sample"]."'";
    }
    $labid="";
    $organism="";
  }
  else{
    $date="";
    $sample="";
    $labid="";
    $organism="";
  }
  $form=$form."<tr><td>Sample Date</td><td colspan='3'><input type='date' class='form-control' name='date' ".$date." form='antibiogram'></td></tr>";
  $form=$form."<tr><td>Report Date</td><td colspan='3'><input type='date' class='form-control' name='rdate' form='antibiogram'></td></tr>";
  $form=$form."<tr><td>Sample</td><td colspan='3'><input type='text' class='form-control' name='sample' ".$sample." form='antibiogram' required></td></tr>";
  $form=$form."<tr><td>Lab ID</td><td colspan='3'><input type='text' class='form-control' name='labid' ".$labid." form='antibiogram'></td></tr>";
  $form=$form."<tr><td>Organism</td><td colspan='3'><input type='text' class='form-control' name='organism' ".$organism." form='antibiogram' required></td></tr>";
  $form=$form."<tr><th>Antibiotic</th><th>MIC</th><th>Interpretation</th>";
  foreach($list as $k=>$v){
    if(!empty($data)){
      $mic="value='".$data->$k->mic."'";
      $interpretation="value='".$data->$k->interpretation."'";
    }
    else{
      $mic="";
      $interpretation="";
    }
    $form=$form."<tr><td><input type='hidden' name='".$k."[name]' value='".$v."' form='antibiogram'><input type='text' form='antibiogram' class='form-control' name='' value='".$v."' title='".$v."' data-toggle='popover' readonly></td><td><input type='text' form='antibiogram' class='form-control' name='".$k."[mic]' ".$mic."></td><td><input type='text' form='antibiogram' class='form-control abinter' name='".$k."[interpretation]' ".$interpretation." step='any'></td></tr>";
  }
  if(!empty($data)){
    $name="value='".$data->other->name."'";
    $mic="value='".$data->other->mic."'";
    $interpretation="value='".$data->other->interpretation."'";
  }
  else{
    $name="";
    $mic="";
    $interpretation="";
  }
  $form=$form."<tr><td><input type='text' form='antibiogram' class='form-control' placeholder='Any other' name='other[name]' ".$name."></td><td><input type='text' form='antibiogram' class='form-control' name='other[mic]' ".$mic."></td><td><input type='text' form='antibiogram' class='form-control abinter' step='any' name='other[interpretation]' ".$interpretation."></td></tr>";
  if(!empty($data)){
    $note=$data->note;
  }
  else{
    $note="";
  }
  $form=$form."<tr><td colspan='3'><textarea class='w-100' name='note' form='antibiogram' placeholder='Notes' ".$note."></textarea></td></tr>";
  $form=$form."<tr><td colspan='3'><button type='submit' form='antibiogram' class='btn btn-primary btn-lg'>Save</button></td></tr>";
  $form=$form."</table>";
}
?>
<!DOCTYPE html>
<html>
  <head>
    <?php include(CONFIG_LIB."head.php");?>
    <title>Antibiogram</title>
  </head>
  <body>
    <div class="container">
      <?php include(CONFIG_LIB."top.php");?>
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Add New Report</h4>
          <?php echo $form;?>
          <a href="attachments.php?pid=<?php echo $pid;?>&name=vitek" target="_blank">Attach a PDF copy of report</a>
        </div>
      </div>
    </div>
    <?php include(CONFIG_LIB."foot.php");?>
  </body>
</html>
