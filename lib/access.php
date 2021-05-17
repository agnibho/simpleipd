<?php
function checkAccess($target, $type="page"){
  global $config;
  $registry=json_decode(file_get_contents(CONFIG_ROOT."access.json"));
  $access="none";
  if(!empty($_SESSION["group"])){
    $group=$_SESSION["group"];
    if(!empty($registry->$target->$group)){
      $access=$registry->$target->$group;
    }
  }
  if($type=="form"){
    if($access=="all"){
      return "";
    }
    else{
      return "style='display:none'";
    }
  }
  if($type=="dbSet"){
    if($access=="all"){
      return true;
    }
    else{
      return false;
    }
  }
  if($type=="dbGet"){
    if($access=="all" || $access=="view"){
      return true;
    }
    else{
      return false;
    }
  }
  else{
    return $access;
  }
}
?>
