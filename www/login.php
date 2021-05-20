<?php
require(dirname(__DIR__)."/require.php");
$error="";
if(!empty($_GET["action"]) && $_GET["action"]=="logout"){
  $_SESSION["user"]=null;
  $_SESSION["group"]=null;
}
if(!empty($_POST["username"]) && !empty($_POST["password"])){
  if($db->checkUser($_POST["username"], $_POST["password"])){
    $_SESSION["user"]=$_POST["username"];
    $_SESSION["group"]=$db->getGroup($_SESSION["user"])->fetchArray()["usergroup"];
    $_SESSION["department"]=$db->getDepartment($_SESSION["user"])->fetchArray()["department"];
    header("Location: index.php");
    exit();
  }
  else{
      $error="<div class='alert alert-danger'>Username or password is incorrect.</div>";
  }
}
//header("Location: view.php?pid=".$_GET["pid"]);
//exit();
?>
<!DOCTYPE html>
<html>
  <head>
    <?php include(CONFIG_LIB."head.php");?>
    <title>Login</title>
  </head>
  <body>
    <div class="container">
      <?php include(CONFIG_LIB."top.php");?>
      <?php echo $error;?>
      <form method="post">
        <input class="m-2 form-control" type="text" name="username" placeholder="Username" required>
        <input class="m-2 form-control" type="password" name="password" placeholder="Password" required>
        <button class="m-2 btn btn-primary" type="submit">Login</button>
      </form>
    </div>
    <?php include(CONFIG_LIB."foot.php");?>
  </body>
</html>
