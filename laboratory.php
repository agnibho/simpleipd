<?php
require("lib/functions.php");
$list="";
if(isSet($_GET["id"])){
  foreach(glob("forms/report*.json") as $file){
    $form=json_decode(file_get_contents($file));
    $list=$list."<li class='list-group-item'><a href='report.php?id=".$_GET["id"]."&form=".str_replace(["forms/",".schema.json"], "", $file)."'>".$form->description."</a></li>";
  }
}
?>
<!DOCTYPE html>
<html>
  <head>
    <?php include("lib/head.php")?>
    <title>Laboratory</title>
  </head>
  <body>
    <div class="container">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">List of Tests</h4>
          <ul class="list-group">
            <?php echo $list;?>
          </ul>
        </div>
      </div>
    </div>
  </body>
</html>
