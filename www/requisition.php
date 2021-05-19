<?php
require(dirname(__DIR__)."/require.php");
if(isSet($_GET["pid"])){
  $pid=$_GET["pid"];
  if(!empty($_POST["del"])){
    $db->omitRequisition($_POST["del"]);
  }
  if(!empty($_POST["test"])){
    if(file_exists($_POST["test"])){
      $form=str_replace(["forms/",".schema.json"], "", $_POST["test"]);
      $test=json_decode(file_get_contents("forms/".$form.".schema.json"))->description;
    }
    else{
      $test=$_POST["test"];
      $form="";
    }
    $db->addRequisition($pid, $test, $_POST["sample"], $_POST["date"], $_POST["time"], $_POST["room"], $form);
  }
  $inv=json_decode(file_get_contents("autocomplete/investigation.json"));
  $testList="";
  foreach(glob("forms/report*.json") as $file){
    $form=json_decode(file_get_contents($file));
    $testList=$testList."<option value='".$file."'>".$form->description."</option>";
  }
  foreach($inv->tests as $t){
    $testList=$testList."<option>".$t."</option>";
  }
  $roomList="";
  foreach($inv->rooms as $r){
    $roomList=$roomList."<option>".$r."</option>";
  }
  $roomList=$roomList."<option selected='selected'>other</option>";

  $reqList=$db->getRequisitions($pid);
  $list="";
  while($req=$reqList->fetchArray()){
    $list=$list."<tr><td>".$req["test"]."</td><td>".$req["sample"]."</td><td>".$req["room"]."</td><td>".date("M j, Y", $req["time"])."</td><td><button type='submit' class='btn btn-secondary' name='del' value='".$req["rowid"]."' form='delete' ".checkAccess("requisition","form").">Delete</button></td></tr>";
  }
}
?>
<!DOCTYPE html>
<html>
  <head>
    <?php include(CONFIG_LIB."head.php");?>
    <title>Laboratory</title>
  </head>
  <body>
    <div class="container">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">List of Requisitions</h4>
          <form method='post' id='delete'></form>
          <table class="table">
            <tr><th>Test Name</th><th>Sample</th><th>Destination</th><th>Date</th><th></th></tr>
            <?php echo $list;?>
          </table>
          <hr>
          <form method="post" <?php echo checkAccess("requisition", "form");?>>
            <div class="row">
              <div class="col">
                <select name="test">
                  <?php echo $testList;?>
                </select>
              </div>
              <div class="col">
                <input type="text" class="form-control" name="sample" placeholder="Sample">
              </div>
              <div class="col">
                <select name="room">
                  <?php echo $roomList;?>
                </select>
              </div>
              <div class="col">
                <input type="date" name="date" class="form-control">
                <input type="time" name="time" class="form-control">
              </div>
              <div class="col">
                <button class="btn btn-primary" type="submit">Submit Requisition</button>
              </div>
          </form>
            </div>
        </div>
      </div>
      <?php include(CONFIG_LIB."foot.php");?>
  </body>
</html>
