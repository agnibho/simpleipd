<?php
require(dirname(__DIR__)."/require.php");
$error="<p>";
$imgs="<div class='card mb-3 w-100'><div class='card-body'><div class='row'>";
$pdfs="<div class='card mb-3'><div class='card-body'>";
if(!empty($_GET["pid"])){
  $pid=$_GET["pid"];
  if(!empty($_FILES)){
    if(in_array($_FILES["upload"]["type"], ["image/jpeg", "image/jpg", "image/png", "image/gif", "application/pdf"])){
      if(!empty($_GET["name"])){
        $name=$_GET["name"]."-";
      }
      else{
        $name="";
      }
      $fname=str_replace("/", "", $pid)."-".$name.time()."-".rand(1000,9999).".".pathinfo($_FILES["upload"]["name"], PATHINFO_EXTENSION);
      move_uploaded_file($_FILES["upload"]["tmp_name"], CONFIG_WWW."data/attachments/".$fname);
      if(!empty($_GET["req"])){
        $db->omitRequisition($_GET["req"]);
      }
    }
    else{
      $error=$error."Only jpg, png, gif, pdf files are supported.";
    }
  }
  elseif(!empty($_POST["delete"])){
    $file=str_replace("/","",$_POST["delete"]);
    rename(CONFIG_WWW."data/attachments/".$file, CONFIG_WWW."data/attachments/.trash/".$file);
  }

  if(checkAccess("attachments")=="all" && $db->getStatus($pid)->fetchArray()["status"]=="admitted"){
    $hideEdit="";
  }
  else{
    $hideEdit="style='display:none'";
  }

  foreach(glob("data/attachments/".str_replace("/", "", $pid)."-*") as $attach){
    if(pathinfo($attach, PATHINFO_EXTENSION)=="pdf"){
      $pdfs=$pdfs."<p><a href='".$attach."'>".pathinfo($attach, PATHINFO_BASENAME)."</a> <button type='submit' ".$hideEdit." form='delete' name='delete' value='".pathinfo($attach, PATHINFO_BASENAME)."' class='float-right btn btn-sm btn-outline-danger confirm'>Delete</button></p><hr>";
    }
    else{
      preg_match("/-([0-9]+)-/", pathinfo($attach, PATHINFO_FILENAME), $orig);
      $imgs=$imgs."<div class='col-md-6'><figure><a href='".$attach."'><img class='w-100 mb-2' src='".$attach."'></a><figcaption>Uploaded on: ".date("M d, Y h:i a", $orig[1])." <button type='submit' ".$hideEdit." form='delete' name='delete' value='".pathinfo($attach, PATHINFO_BASENAME)."' class='float-right btn btn-sm btn-outline-danger confirm'>Delete</button></figcaption></figure></div>";
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
    <?php include(CONFIG_LIB."head.php");?>
    <title>Attachments</title>
  </head>
  <body>
    <div class="container">
      <form method="post" id="delete"></form>
      <?php include(CONFIG_LIB."top.php");?>
      <?php echo getInfo($pid);?>
      <form class="mt-3 mb-3" method="post" enctype="multipart/form-data" <?php echo $hideEdit; ?>>
        <label for="upload">Select file to upload. JPG, PNG, GIF and PDF files are supported. Size limit: <span id="size-limit"><?php echo str_replace("M", "MB", ini_get("upload_max_filesize"));?></span><span id="upload-error"></span></label>
        <input type="file" name="upload" id="upload" class="form-control">
        <input type="submit" value="Upload" class="mt-2 btn btn-primary">
      </form>
      <div id="attachments">
        <?php echo $pdfs;?>
          <div class="row">
            <?php echo $imgs;?>
          </div>
      </div>
      <?php echo $error;?>
    </div>
    <?php include(CONFIG_LIB."foot.php");?>
  </body>
</html>
