<?php
require("lib/db.php");
require("lib/functions.php");
$list=$db->getList();
$show="";
if(!empty($list)){
  while($arr=$list->fetchArray()){
    $pid=$arr["pid"];
    $name=$db->getName($pid)->fetchArray()["name"];
    $show=$show."<tr><td><a href='view.php?pid=".$pid."'>".$pid."</a></td><td>".$name."</td><td> </td></tr>";
  }
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
            <?php echo $show;?>
          </table>
          <a class="btn btn-primary btn-lg" href="admission.php">Add New Patient</a>
        </div>
      </div>
    </div>
  </body>
</html>
