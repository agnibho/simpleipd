<?php
require(dirname(__DIR__)."/require.php");
$list=$db->getArchivedPatientList();
$showList="";
while($arr=$list->fetchArray()){
  $pid=$arr["pid"];
  $showList=$showList."<tr><td><a href='view.php?pid=".$pid."'>".$pid."</a></td><td>".$arr["name"]."</td><td>".$arr["diagnosis"]."</td><td>".$arr["status"]."</td></tr>";
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
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Patient Archive</h4>
          <table class="table">
            <tr><th>Patient ID</th><th>Name</th><th>Diagnosis</th><th>Status</th></tr>
            <?php echo $showList;?>
          </table>
        </div>
      </div>
    </div>
    <?php include(CONFIG_LIB."foot.php");?>
  </body>
</html>
