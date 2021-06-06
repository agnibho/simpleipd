<?php
require(dirname(__DIR__)."/require.php");

$info="";
$physician=[];
$nursing=[];
$reports=[];
if(isSet($_GET["pid"])){
  $pid=$_GET["pid"];

  if(!empty($_POST["shiftWard"]) && !empty($_POST["ward"]) && !empty($_POST["bed"])){
    $ward=$_POST["ward"];
    $bed=$_POST["bed"];
    $oldward=$db->getWard($pid)->fetchArray()["ward"];
    $oldbed=$db->getBed($pid)->fetchArray()["bed"];
    $rec="Transfer: ".$oldward."-".$oldbed." -> ".$ward."-".$bed." @ ".$_POST["date"]." ".$_POST["time"];
    $data=json_decode($db->getAdmissionData($pid)->fetchArray()["data"]);
    $data->ward=$ward;
    $data->bed=$bed;
    $data->extra_note=$data->extra_note."\n".$rec;
    $db->admit((array) $data);
  }

  $status=$db->getStatus($pid)->fetchArray()["status"];
  if($status!="admitted"){
    $archive="disabled";
  }
  else{
    $archive="";
  }
  $info=viewData($db->getAdmissionData($pid)->fetchArray()["data"]);
  $history=$db->getHistory($pid)->fetchArray()["history"];
  if(!empty(json_decode($history)->onset)){
    $diff="<tr><th>Day of illness: </th><td>".date_diff(new DateTime(), new DateTime(json_decode($history)->onset))->format("%a")."</td></tr>";
  }
  else{
    $diff="";
  }
  $history=viewData($history);
  $physicianArray=$db->getAllData($pid, "physician");
  while($c=$physicianArray->fetchArray()){
    array_push($physician, viewData($c["data"], "physician.php?pid=".$pid."&id=".$c["rowid"]));
  }
  $nursingArray=$db->getAllData($pid, "nursing");
  while($c=$nursingArray->fetchArray()){
    array_push($nursing, viewData($c["data"], "nursing.php?pid=".$pid."&id=".$c["rowid"]));
  }
  $reportsArray=$db->getAllData($pid, "reports");
  while($r=$reportsArray->fetchArray()){
    if(in_array($r["form"], ["report-as-grampos", "report-as-gramneg", "report-as-fungal"])){
      array_push($reports, viewAntibiogram($r["data"], "antibiogram.php?pid=".$pid."&id=".$r["rowid"]."&form=".$r["form"]));
    }
    else{
      array_push($reports, viewData($r["data"], "report.php?pid=".$pid."&id=".$r["rowid"]."&form=".$r["form"]));
    }
  }
}
?>
<!DOCTYPE html>
<html>
  <head>
    <?php include(CONFIG_LIB."head.php");?>
    <title>View Info</title>
  </head>
  <body>
    <div class="container">
      <?php include(CONFIG_LIB."top.php");?>
      <h1>Patient Data</h1>
      <div class="card">
        <div class="card-body">
          <div class="row">
            <div class="mb-2 col-md-3" <?php if($info=="") echo "style='display:none'";?>>
              <a class="btn btn-success btn-lg btn-block" href="treatment.php?pid=<?php echo $pid;?>">Treatment</a>
            </div>
            <div class="mb-2 col-md-3" <?php if($info=="") echo "style='display:none'";?>>
              <a class="mb-2 btn btn-primary btn-lg btn-block <?php echo $archive;?>" href="physician.php?pid=<?php echo $pid;?>">Add Physician Note</a>
            </div>
            <div class="mb-2 col-md-3" <?php if($info=="") echo "style='display:none'";?>>
              <a class="mb-2 btn btn-warning btn-lg btn-block <?php echo $archive;?>" href="nursing.php?pid=<?php echo $pid;?>">Add Nursing Note</a>
            </div>
            <div class="mb-2 col-md-3" <?php if($info=="") echo "style='display:none'";?>>
              <a class="mb-2 btn btn-danger btn-lg btn-block <?php echo $archive;?>" href="requisition.php?pid=<?php echo $pid;?>">Add Requisition</a>
            </div>
          </div>
          <div class="row">
            <div class="mb-2 col-md-2" <?php if($info=="") echo "style='display:none'";?>>
              <a class="mb-2 btn btn-secondary <?php echo $archive;?>" href="admission.php?pid=<?php echo $pid;?>">Edit Information</a>
            </div>
            <div class="mb-2 col-md-2" <?php if($info=="") echo "style='display:none'";?>>
              <a class="mb-2 btn btn-secondary <?php echo $archive;?>" href="history.php?pid=<?php echo $pid;?>">Add History</a>
            </div>
            <div class="mb-2 col-md-2" <?php if($info=="") echo "style='display:none'";?>>
              <a class="btn btn-secondary <?php echo $archive;?>" href="attachments.php?pid=<?php echo $pid;?>">Attachments</a>
            </div>
            <div class="mb-2 col-md-2" id="discharge" <?php if($info=="") echo "style='display:none'";?>>
              <a class="btn btn-secondary <?php echo $archive;?>" href="discharge.php?pid=<?php echo $pid;?>">Discharge</a>
            </div>
            <div class="mb-2 col-md-2" id="death" <?php if($info=="") echo "style='display:none'";?>>
              <a class="btn btn-secondary <?php echo $archive;?>" href="death.php?pid=<?php echo $pid;?>">Death</a>
            </div>
            <div class="mb-2 col-md-2" <?php if($info=="") echo "style='display:none'";?>>
              <a class="btn btn-secondary" href="printdata.php?pid=<?php echo $pid;?>">Print Data</a>
            </div>
          </div>
        </div>
      </div>
      <div <?php if(empty($pid)) echo "style='display:none'";?>>
        <ul class="nav nav-tabs" id="listtabs" role="tablist">
          <li class="nav-item" role="presentation">
            <a class="nav-link active" id="info-tab" data-toggle="tab" href="#info" role="tab" aria-controls="info" aria-selected="true">Info</a>
          </li>
          <li class="nav-item" role="presentation">
            <a class="nav-link" id="history-tab" data-toggle="tab" href="#history" role="tab" aria-controls="history" aria-selected="false">History</a>
          </li>
          <li class="nav-item" role="presentation">
            <a class="nav-link" id="physician-tab" data-toggle="tab" href="#physician" role="tab" aria-controls="physician" aria-selected="false">Physician Notes</a>
          </li>
          <li class="nav-item" role="presentation">
            <a class="nav-link" id="nursing-tab" data-toggle="tab" href="#nursing" role="tab" aria-controls="clinical" aria-selected="false">Nursing Notes</a>
          </li>
          <li class="nav-item" role="presentation">
            <a class="nav-link" id="report-tab" data-toggle="tab" href="#report" role="tab" aria-controls="report" aria-selected="false">Lab Reports</a>
          </li>
          <li class="nav-item" role="presentation">
            <a class="nav-link" href="chart.php?pid=<?php echo $pid;?>">Charts</a>
          </li>
        </ul>
        <div class="tab-content" id="viewtabs">
          <div class="tab-pane fade show active" id="info" role="tabpanel" aria-labelledby="info-tab">
            <div class='card'><div class='card-body'>Status: <?php echo $status;?></div></div>
            <div class="row">
              <div class="col-md-6">
                <?php echo $info;?>
              </div>
              <div class="col-md-6">
                <table class="table">
                  <tr><th>Diagnosis</th><td><?php echo $db->getDiagnosis($pid)->fetchArray()["diagnosis"];?></td></tr>
                  <tr><th>Summary</th><td><?php echo $db->getSummary($pid)->fetchArray()["summary"];?></td></tr>
                  <?php echo $diff;?>
                </table>
              </div>
            </div>
            <p><a href="#" class="btn btn-outline-primary <?php echo $archive;?>" id="showtransfer">Transfer</a></p>
            <form method="post" id="transfer" class="d-none">
              <div class="form-row">
                <input type="hidden" name="shiftWard" value="true">
                <div class="col">
                  <input type="text" name="ward" class="form-control" placeholder="Ward" required>
                </div>
                <div class="col">
                  <input type="text" name="bed" class="form-control" placeholder="Bed" required>
                </div>
                <div class="col">
                  <input type="date" name="date" class="form-control" placeholder="Date">
                </div>
                <div class="col">
                  <input type="time" name="time" class="form-control" placeholder="Time">
                </div>
                <div class="col">
                  <button type="submit" class="btn btn-primary">Transfer</button>
                </div>
              </div>
            </form>
            <p <?php if(!in_array($_SESSION["group"],["admin","visiting","resident"])) echo "class='d-none'";?>><a class="mb-2 btn btn-outline-success <?php echo $archive;?>" href="laboratory.php?pid=<?php echo $pid;?>">Add Report</a></p>
          </div>
          <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="history-tab">
            <?php echo $history;?>
            <p><a href="history.php?pid=<?php echo $pid;?>">Edit</a></p>
          </div>
          <div class="tab-pane fade" id="physician" role="tabpanel" aria-labelledby="physician-tab">
            <?php foreach($physician as $p) echo $p;?>
          </div>
          <div class="tab-pane fade" id="nursing" role="tabpanel" aria-labelledby="nursing-tab">
            <?php foreach($nursing as $n) echo $n;?>
          </div>
          <div class="tab-pane fade" id="report" role="tabpanel" aria-labelledby="report-tab">
            <?php foreach($reports as $r) echo $r;?>
          </div>
        </div>
        <hr>
      </div>
    </div>
    <div <?php if(!empty($pid)) echo "style='display:none'";?>>
      <h1>Please enter a valid patient ID</h1>
      <form>
        <input class="form-control" name="pid">
        <button class="form-control" type="submit">View</button>
      </form>
    </div>
    <?php include(CONFIG_LIB."foot.php");?>
<script>
$(document).ready(function(){
  $("#showtransfer").click(function(){
    $("#transfer").removeClass("d-none");
  });
});
</script>
  </body>
</html>
