<?php
function schema2form($file, $pid=null, $id=null, $cat=null, $data=null){
  global $db;
  $schema=json_decode(file_get_contents($file));

  $lockpid="";
  if(!empty($pid) && !empty($id) && !empty($cat)){
    $data=json_decode($db->getData($pid, $id, $cat)->fetchArray()["data"]);
  }
  elseif(!empty($pid) && $file=="forms/admission.schema.json"){
    $data=json_decode($db->getAdmissionData($pid)->fetchArray()["data"]);
    $lockpid="readonly";
  }
  elseif(!empty($pid) && $file=="forms/history.schema.json"){
    $data=json_decode($db->getHistory($pid)->fetchArray()["history"]);
    $lockpid="readonly";
  }

  $form="<form method='post'>";
  $form=$form."<input type='hidden' name='form' value='".str_replace(["forms/",".schema.json"], "", $file)."'>";

  foreach($schema->properties as $field=>$prop){
    if($prop->type == "integer") $prop->type="number";
    if($prop->type == "string") $prop->type="text";
    if(!empty($data->$field)){
      $value="value='".$data->$field."'";
    }
    else{
      $value="";
    }
    if(in_array($field, $schema->required)){
      $req="required";
    }
    else{
      $req="";
    }
    if(isSet($prop->format)){
      $type=$prop->format;
    }
    else{
      $type=$prop->type;
    }

    $form=$form."<div>";
    $form=$form."<label class='form-label' for='".$field."'>".$prop->description."</label>";
    if(isSet($prop->enum)){
      $form=$form."<select class='form-control' ".$req." name='".$field."' id='".$field."'>";
      foreach($prop->enum as $opt){
        $form=$form."<option>".$opt."</option>";
      }
      $form=$form."</select>";
    }
    elseif(isSet($prop->format) && $prop->format=="textarea"){
      $form=$form."<textarea class='form-control' name='".$field."' id='".$field."'>".$value."</textarea>";
    }
    elseif($field=="pid"){
      $form=$form."<input class='form-control' ".$lockpid." ".$req." type='".$type."' step='any' name='".$field."' id='".$field."' ".$value.">";
    }
    else{
      $form=$form."<input class='form-control' ".$req." type='".$type."' step='any' name='".$field."' id='".$field."' ".$value.">";
    }
    $form=$form."</div>";

  }
  $form=$form."<div><label class='form-label' for='extra-note'>Extra Notes</label><textarea class='form-control' name='extra-note' id='extra-note'></textarea></div>";
  $form=$form."<button class='btn btn-primary mt-3' type='submit'>Save</button>";
  $form=$form."</form>";
  return $form;
}

function getInfo($pid){
  global $db;
  $info="<table class='table'>";
  $info=$info."<tr><td>ID</td><td>".$pid."</td></tr>";
  $info=$info."<tr><td>Name</td><td>".$db->getName($pid)->fetchArray()["name"]."</td></tr>";
  $info=$info."<tr><td>Age</td><td>".$db->getAge($pid)->fetchArray()["age"]."</td></tr>";
  $info=$info."<tr><td>Sex</td><td>".$db->getSex($pid)->fetchArray()["sex"]."</td></tr>";
  $info=$info."<tr><td>Bed</td><td>".$db->getWard($pid)->fetchArray()["ward"]."-".$db->getBed($pid)->fetchArray()["bed"]."</td></tr>";
  $info=$info."<tr><td>Diagnosis</td><td>".$db->getDiagnosis($pid)->fetchArray()["diagnosis"]."</td></tr>";
  $info=$info."</table>";
  return $info;
}

function viewData($data, $edit=null){
  if(!empty($data)){
    $data=json_decode($data);
    if(!empty($data->form)){
      $schema=json_decode(file_get_contents("forms/".$data->form.".schema.json"));
    }
    unset($data->cat);
    $view="<table class='table'>";
    foreach($data as $field=>$value){
      if($field!="form"){
        if(!empty($schema->properties->$field)){
          $view=$view."<tr><td>".$schema->properties->$field->description."</td><td>".$value."</td></tr>";
        }
        elseif($field=="extra-note"){
          $view=$view."<tr><td>Extra Notes</td><td>".$value."</td></tr>";
        }
        else{
          $view=$view."<tr><td>".$field."</td><td>".$value."</td></tr>";
        }
      }
    }
    if(!empty($edit)){
      $view=$view."<tr><td><a href='".$edit."'>Edit</a>";
    }
    $view=$view."</table>";
    return $view;
  }
  else{
    return "";
  }
}

function view_drug($file){
  if(is_file($file)){
    $druglist=json_decode(file_get_contents($file));
  }
  else{
    $druglist=[];
  }
  $view="<form method='post' id='omitter'></form><table class='table'>";
  $view=$view."<tr><th>Drug</th><th>Dose</th><th>Route</th><th>Frequency</th><th>Duration</th></tr>";
  foreach($druglist as $id=>$drug){
    if($drug->omit){
      $omit="style='display:none'";
    }
    else{
      $omit="";
    }
    $view=$view."<tr><td>".$drug->drug."</td><td>".$drug->dose."</td><td>".$drug->route."</td><td>".$drug->freq."</td><td>".$drug->duration."</td><td>".$drug->note."</td><td><button name='omit' value='".$id."' form='omitter' ".$omit.">Omit</button></td></tr>";
  }
  $view=$view."</table>";
  return $view;
}
?>
