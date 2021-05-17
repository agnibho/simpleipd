<?php
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
?>
