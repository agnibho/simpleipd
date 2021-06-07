<nav class="navbar navbar-expand-lg navbar-light bg-light">
<?php
if(basename($_SERVER["PHP_SELF"])=="view.php"){
  echo '<a class="navbar-brand" href="'.CONFIG_URL.'">'.CONFIG_TITLE.'</a>';
}
elseif(!empty($_GET["pid"])){
  echo '<a href="view.php?pid='.$_GET["pid"].'" class="navbar-brand">View Patient</a>';
}
else{
  echo '<a class="navbar-brand" href="'.CONFIG_URL.'">'.CONFIG_TITLE.'</a>';
}
?>

<div class="ml-auto">
<?php
if(!empty($_SESSION["user"])){
  echo '<span class="mr-2">'.$_SESSION["user"].'</span>';
  echo '<a href="login.php?action=logout" class="btn btn-sm btn-secondary">Logout</a>';
}
?>
  </div>
</nav>
