<?php
require("lib/functions.php");
$list="";
foreach(glob("data/*") as $id){
  $data=json_decode(file_get_contents($id."/info.json"));
  $id=str_replace("data/", "", $id);
  $list=$list."<tr><td><a href='view.php?id=".$id."'>".$id."</a></td><td>".$data->name."</td><td>".$data->ward." ".$data->bed."</td></tr>";
}
?>
<!DOCTYPE html>
<html>
  <head>
    <?php include("lib/head.php");?>
    <title>View Info</title>
  </head>
  <body>
    <div class="container">
      <h1>SimpleIPD</h1>
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Patient List</h4>
          <table class="table">
            <tr><th>Patient ID</th><th>Name</th><th>Bed Number</th></tr>
            <?php echo $list;?>
          </table>
          <a class="btn btn-primary btn-lg" href="admission.php">Add New Patient</a>
        </div>
      </div>
    </div>
  </body>
</html>
