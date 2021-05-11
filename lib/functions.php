<?php
function schema2form($file, $stamp=false, $fill=false){
  $schema=json_decode(file_get_contents($file));

  if($fill!==false){
    $data=json_decode(file_get_contents($fill));
  }
  if($stamp===false){
    $stamp=time()."-".rand(1000,9999);
  }

  $form="<form method='post'>";
  $form=$form."<input type='hidden' name='stamp' value='".$stamp."'>";
  $form=$form."<input type='hidden' name='form' value='".str_replace(["forms/", ".schema.json"], "", $file)."'>";

  foreach($schema->properties as $field=>$prop){
    if($prop->type == "integer") $prop->type="number";
    if($prop->type == "string") $prop->type="text";
    if($fill!==false){
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
    else{
      $form=$form."<input class='form-control' ".$req." type='".$type."' name='".$field."' id='".$field."' ".$value.">";
    }
    $form=$form."</div>";

  }
  $form=$form."<div><label class='form-label' for='extra-note'>Notes</label><textarea class='form-control' name='extra-note' id='extra-note'></textarea></div>";
  $form=$form."<button class='btn btn-primary mt-3' type='submit'>Save</button>";
  $form=$form."</form>";
  return $form;
}

function view_data($file, $edit=false){
  if(is_file($file)){
    $data=json_decode(file_get_contents($file));
  }
  else{
    return "";
  }
  $view="<table class='table'>";
  foreach($data as $field=>$value){
    $view=$view."<tr><td>".$field."</td><td>".$value."</td></tr>";
  }
  if($edit!==false){
    $view=$view."<tr><td><a href='".$edit."&form=".$data->form."&stamp=".$data->stamp."'>Edit</a>";
  }
  $view=$view."</table>";
  return $view;
}

function add_drug($file, $drug){
  if(is_file($file)){
    $druglist=json_decode(file_get_contents($file));
  }
  else{
    $druglist=[];
  }
  $drug["omit"]=false;
  array_push($druglist, $drug);
  file_put_contents($file, json_encode($druglist));
}

function omit_drug($file, $id){
  if(is_file($file)){
    $druglist=json_decode(file_get_contents($file));
    var_dump($druglist);
    $druglist[$id]->omit=true;
    file_put_contents($file, json_encode($druglist));
  }
  else{
    echo "boo";
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
