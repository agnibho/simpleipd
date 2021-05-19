<?php
require(dirname(__DIR__)."/require.php");
$pid=$_GET["pid"];
if($_GET["req"]){
  $req="&req=".$_GET["req"];
}
else{
  $req="";
}
if(!empty($_GET["src"]) && $_GET["src"]=="index"){
  $src="&src=index";
}
else{
  $src="";
}
?>
<!DOCTYPE html>
<html>
  <head>
    <?php include(CONFIG_LIB."head.php");?>
    <title>Culture/Sensitivity</title>
  </head>
  <body>
    <div class="container">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Type of Organism</h4>
          <ul class="list-group">
            <li class="list-group-item"><a href="antibiogram.php?pid=<?php echo $pid;?>&form=report-as-grampos<?php echo $req;?><?php echo $src;?>">Vitek Report (Gram Positive)</a></li>
            <li class="list-group-item"><a href="antibiogram.php?pid=<?php echo $pid;?>&form=report-as-gramneg<?php echo $req;?><?php echo $src;?>">Vitek Report (Gram Negative)</a></li>
            <li class="list-group-item"><a href="antibiogram.php?pid=<?php echo $pid;?>&form=report-as-fungal<?php echo $req;?><?php echo $src;?>">Vitek Report (Fungal)</a></li>
          </ul>
        </div>
      </div>
    </div>
    <?php include(CONFIG_LIB."foot.php");?>
  </body>
</html>
