<?php
require(dirname(__DIR__)."/require.php");
if(checkAccess("admission")!="all"){
  header("Location: error.php");
  exit();
}
if(!empty($_POST["pid"]) && !empty($_POST["name"])){
  $db->admit($_POST);
  header("Location: view.php?pid=".$_POST["pid"]);
  exit();
}
if(!empty($_GET["pid"])){
  $pid=$_GET["pid"];
  if(!empty($_POST["diagnosis"]) || !empty($_POST["summary"])){
    $db->editCase($pid, $_POST["diagnosis"], $_POST["summary"]);
  }
  $form=schema2form("forms/admission.schema.json", $pid);
}
else{
  $form=schema2form("forms/admission.schema.json");
}
?>
<!DOCTYPE html>
<html>
  <head>
    <?php include(CONFIG_LIB."head.php");?>
    <title>Admission</title>
  </head>
  <body>
    <div class="container">
      <?php include(CONFIG_LIB."top.php");?>
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Patient Information</h4>
          <form method="post" class="mb-4" <?php echo checkAccess("history", "form");?>>
            <label for="case-diagnosis">Diagnosis</label>
            <input type="text" class="form-control" name="diagnosis" id="case-diagnosis" value="<?php if(!empty($pid)) echo $db->getDiagnosis($pid)->fetchArray()["diagnosis"];?>">
            <label for="case-summary">Summary</label>
            <textarea type="text" class="form-control" name="summary" id="case-summary"><?php if(!empty($pid)) echo $db->getSummary($pid)->fetchArray()["summary"];?></textarea>
            <button class="btn btn-primary mt-3" type="submit">Save</button>
          </form>
          <div class="alert alert-danger"><strong>Admission ID can NOT be changed</strong> after entry. Hence, make abolutely sure that you enter the correct admission ID before saving.</div>
          <?php echo $form;?>
        </div>
      </div>
    </div>
    <?php include(CONFIG_LIB."foot.php");?>
  </body>
</html>
