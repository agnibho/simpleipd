<?php
require(dirname(__DIR__)."/require.php");

if(isSet($_GET["pid"])){
  $pid=$_GET["pid"];
  if(!empty($_GET["get"]) && $_GET["get"]=="nursing"){
    $nursing=[];
    $nursingArray=$db->getAllData($pid, "nursing");
    while($item=$nursingArray->fetchArray()){
      array_push($nursing, json_decode($item["data"]));
    }
    echo json_encode($nursing);
    exit();
  }
  if(!empty($_GET["get"]) && $_GET["get"]=="physician"){
    $physician=[];
    $physicianArray=$db->getAllData($pid, "physician");
    while($item=$physicianArray->fetchArray()){
      array_push($physician, json_decode($item["data"]));
    }
    echo json_encode($physician);
    exit();
  }
  if(!empty($_GET["get"]) && $_GET["get"]=="reports"){
    $reports=[];
    $reportsArray=$db->getAllData($pid, "reports");
    while($item=$reportsArray->fetchArray()){
      array_push($reports, json_decode($item["data"]));
    }
    echo json_encode($reports);
    exit();
  }
  if(!empty($_GET["get"]) && $_GET["get"]=="treatment"){
    $treatment=[];
    $treatmentArray=$db->getDrugs($pid);
    while($item=$treatmentArray->fetchArray()){
      if($item["omit"]==0){
        array_push($treatment, $item);
      }
    }
    echo json_encode($treatment);
    exit();
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
      <div class="alert alert-warning"><p>This page contains experimental features. Please double check for the time being.</p></div>
      <h1>Patient Data</h1>
      <?php echo getInfo($pid);?>
      <hr>
      <div class="alert alert-light d-none" id="ioAlert">Possible inconsistent intake output data. (<span id="ioInconsistency"></span>)</div>
      <div class="card d-none" id="ioData">
        <div class="card-body">
          <h4 class="card-heading">Intake/Output</h4>
          <ul>
            <li>Approximate <span class="ioGap"></span> hours intake: <span id="approxIn"></span> ml</li>
            <li>Approximate <span class="ioGap"></span> hours output: <span id="approxOut"></span> ml</li>
          </ul>
        </div>
      </div>
      <div class="card">
        <div class="card-body">
          <p id="crcl"></p>
        </div>
      </div>
      <hr>
      <h4>Clinical Parameters</h4>
      <table class="table">
        <thead>
          <tr><th>Date/Time</th><th><select id="clinVar"><option disabled selected>--Select Parameter--</option></select></th></tr>
        </thead>
        <tbody id="clinData">
        </tbody>
      </table>
      <canvas id="clinChart"></canvas>
      <hr>
      <h4>Reports</h4>
      <table class="table">
        <thead>
          <tr><th>Date/Time</th><th><select id="reportsVar"><option disabled selected>--Select Parameter--</option></select></th></tr>
        </thead>
        <tbody id="reportsData">
        </tbody>
      </table>
      <canvas id="reportsChart"></canvas>
      <hr>
      <h4>Drugs</h4>
      <table class="table">
        <tr><td><select id="drugVar"><option disabled selected>--Select Drug--</option></select></td><td id="drugData1"></td><td id="drugData2"></td></tr>
      </table>
      <canvas id="drugsChart"></canvas>
      <?php include(CONFIG_LIB."foot.php");?>
      <script>var pid="<?php echo $pid;?>"</script>
      <script src="res/calc.js"></script>
    </div>
  </body>
</html>
