<?php
require(__DIR__."/config.php");
require("lib/access.php");
require("lib/db.php");
require("lib/functions.php");
session_start();
$page=basename($_SERVER["PHP_SELF"]);
if($page!="login.php" && $page!="index.php"){
  if(empty($_SESSION["user"])){
    header("Location: login.php");
    exit();
  }
  $access=checkAccess(basename($_SERVER["PHP_SELF"], ".php"));
  if($access!="all" && $access!="view"){
    header("Location: error.php");
    exit();
  }
}
if(!empty($_GET)){
  foreach($_GET as $k=>$v){
    $_GET[$k]=htmlspecialchars($v);
  }
}
if(!empty($_POST)){
  foreach($_POST as $k=>$v){
    $_POST[$k]=htmlspecialchars($v);
  }
}
?>
