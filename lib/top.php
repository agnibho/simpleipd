<nav class="navbar navbar-expand-lg navbar-light bg-light">
<?php
if(!empty($_GET["pid"])){
  echo '<a href="view.php?pid='.$_GET["pid"].'" class="navbar-brand">View Patient</a>';
}
else{
  echo '<a class="navbar-brand" href="#">'.CONFIG_TITLE.'</a>';
}
?>

<div class="ml-auto">
<?php
if(!empty($_SESSION["user"])){
  echo '<span class="mr-2">'.$_SESSION["user"].'</span>';
  echo '<a href="login?action=logout" class="btn btn-sm btn-secondary">Logout</a>';
}
?>
  </div>
</nav>
