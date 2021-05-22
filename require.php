<?php
require(__DIR__."/config.php");
require("lib/access.php");
require("lib/log.php");
require("lib/db.php");
require("lib/functions.php");
date_default_timezone_set(CONFIG_TZ);
session_start();
$page=basename($_SERVER["PHP_SELF"]);
if($page!="login.php"){
  if(empty($_SESSION["user"])){
    header("Location: login.php");
    exit();
  }
}
if($page!="login.php" && $page!="index.php"){
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
    if(is_array($v)){
      foreach($v as $k2=>$v2){
        $v[$k2]=htmlspecialchars($v2);
      }
      $_POST["k"]=$v;
    }
    else{
      $_POST[$k]=htmlspecialchars($v);
    }
  }
}
?>
