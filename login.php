<?php
require("lib/db.php");
require("lib/functions.php");
session_start();
$error="";
if(!empty($_GET["action"]) && $_GET["action"]=="logout"){
  $_SESSION["user"]=null;
}
if(!empty($_POST["username"]) && !empty($_POST["password"])){
  if($db->checkUser($_POST["username"], $_POST["password"])){
    $_SESSION["user"]=$_POST["username"];
    header("Location: index.php");
    exit();
  }
  else{
      $error="<div class='alert alert-danger'>Username or password is incorrect.</div>";
  }
}
//header("Location: view.php?id=".$_GET["id"]);
//exit();
?>
<!DOCTYPE html>
<html>
  <head>
    <?php include("lib/head.php");?>
    <title>Login</title>
  </head>
  <body>
    <div class="container">
      <?php echo $error;?>
      <form method="post">
        <input class="m-2 form-control" type="text" name="username" placeholder="Username" required>
        <input class="m-2 form-control" type="password" name="password" placeholder="Password" required>
        <button class="m-2 btn btn-primary" type="submit">Login</button>
      </form>
    </div>
    <?php include("lib/foot.php");?>
  </body>
</html>
