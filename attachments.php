<?php
require("lib/db.php");
require("lib/functions.php");
$error="<p>";
$imgs="<div class='card mb-3'><div class='card-body'><div class='row'>";
$pdfs="<div class='card mb-3'><div class='card-body'>";
if(!empty($_GET["pid"])){
  $pid=$_GET["pid"];
  if(!empty($_FILES)){
    if($_FILES["upload"]["size"]<8000000){
      if(in_array($_FILES["upload"]["type"], ["image/jpeg", "image/jpg", "image/png", "image/gif", "application/pdf"])){
        $fname=str_replace("/", "", $pid)."-".time()."-".rand(1000,9999).".".pathinfo($_FILES["upload"]["name"], PATHINFO_EXTENSION);
        move_uploaded_file($_FILES["upload"]["tmp_name"], "data/attachments/".$fname);
      }
      else{
        $error=$error."Only jpg, png, gif, pdf files are supported.";
      }
    }
    else{
        $error=$error."Maximum filesize exceeded. File upload failed";
    }
  }

  foreach(glob("data/attachments/".str_replace("/", "", $pid)."-*") as $attach){
    if(pathinfo($attach, PATHINFO_EXTENSION)=="pdf"){
      $pdfs=$pdfs."<a href='".$attach."'>".pathinfo($attach, PATHINFO_BASENAME)."</a>";
    }
    else{
      preg_match("/-([0-9]+)-/", pathinfo($attach, PATHINFO_FILENAME), $orig);
      $imgs=$imgs."<div class='col-md-6'><figure><img class='w-100' src='".$attach."'><figcaption>Uploaded on: ".date("M d, Y h:i a", $orig[1])."</figcaption></figure></div>";
    }
  }
}
$imgs=$imgs."</div></div></div>";
$pdfs=$pdfs."</div></div>";
$error=$error."</p>";
?>
<!DOCTYPE html>
<html>
  <head>
    <?php include("lib/head.php");?>
    <title>Attachments</title>
  </head>
  <body>
    <div class="container">
      <?php echo getInfo($pid);?>
      <div id="attachments">
        <?php echo $pdfs;?>
          <div class="row">
            <?php echo $imgs;?>
          </div>
      </div>
      <?php echo $error;?>
      <form method="post" enctype="multipart/form-data">
        <input type="file" name="upload" id="upload" class="form-control">
        <input type="submit" value="Upload" class="mt-2 btn btn-primary">
      </form>
    </div>
    <?php include("lib/foot.php");?>
  </body>
</html>
