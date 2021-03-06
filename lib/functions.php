<?php
function resolveRange($arr, $val=false){
  if($val){
    if(!empty($arr[0]) && $val<$arr[0]){
      return "text-danger";
    }
    elseif(!empty($arr[1]) && $val>$arr[1]){
      return "text-danger";
    }
    else{
      return "";
    }
  }
  else{
    $part=["","","",""];
    if(!empty($arr[0])){
      $part[1]=$arr[0];
    }
    else{
      $part[0]="&lt;";
    }
    if(!empty($arr[1])){
      $part[3]=$arr[1];
    }
    else{
      $part[0]="&gt";
    }
    if(!empty($arr[0]) && !empty($arr[1])){
      $part[2]="-";
    }
    return implode("",$part);
  }
}
function schema2form($file, $pid=null, $id=null, $cat=null, $data=null, $time=null){
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
      $value2=$data->$field;
    }
    elseif(!empty($time) && $field=="date"){
      $value="value='".date("Y-m-d", $time)."'";
      $value2=date("Y-m-d", $time);
    }
    elseif(!empty($time) && $field=="time"){
      $value="value='".date("H:i", $time)."'";
      $value2=date("H:i", $time);
    }
    else{
      $value="";
      $value2="";
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
        if($opt==$value2){
          $form=$form."<option selected>".$opt."</option>";
        }
        else{
          $form=$form."<option>".$opt."</option>";
        }
      }
      $form=$form."</select>";
    }
    elseif(isSet($prop->format) && $prop->format=="textarea"){
      $form=$form."<textarea class='form-control' name='".$field."' id='".$field."'>".$value2."</textarea>";
    }
    elseif($field=="pid"){
      $form=$form."<input class='form-control' ".$lockpid." ".$req." type='".$type."' step='any' name='".$field."' id='".$field."' ".$value.">";
    }
    else{
      $form=$form."<input class='form-control' ".$req." type='".$type."' step='any' name='".$field."' id='".$field."' ".$value.">";
    }
    $form=$form."</div>";

  }
  if(!empty($data->extra_note)){
    $extra_note=$data->extra_note;
  }
  else{
    $extra_note="";
  }
  $form=$form."<div><label class='form-label' for='extra_note'>Extra Notes</label><textarea class='form-control' name='extra_note' id='extra_note'>".$extra_note."</textarea></div>";
  $form=$form."<button class='btn btn-primary mt-3' type='submit'>Save</button>";
  $form=$form."</form>";
  return $form;
}

function getInfo($pid){
  global $db;
  $info="<table class='table'>";
  $info=$info."<tr><td>ID</td><td>".$pid."</td></tr>";
  $info=$info."<tr><td>Name</td><td id='info-name'>".$db->getName($pid)->fetchArray()["name"]."</td></tr>";
  $info=$info."<tr><td>Age</td><td id='info-age'>".$db->getAge($pid)->fetchArray()["age"]."</td></tr>";
  $info=$info."<tr><td>Sex</td><td id='info-sex'>".$db->getSex($pid)->fetchArray()["sex"]."</td></tr>";
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
    if(!empty($schema->description)){
      $description=$schema->description;
    }
    else{
      $description="";
    }
    if(!empty($data->date)){
      if(!empty($data->time)){
        $date=date("M d, Y h:i a", strtotime($data->date." ".$data->time));
      }
      else{
        $date=$data->date;
      }
    }
    else{
      $date="";
    }
    if(!empty($data->rdate)){
      if(!empty($data->rtime)){
        $rdate=date("M d, Y h:i a", strtotime($data->rdate." ".$data->rtime));
      }
      else{
        $rdate=$data->rdate;
      }
    }
    else{
      $rdate="";
    }
    $view=$view."<tr><th class='w-25'>".$description."</th><th>".$date."</th>";
    $view=$view."<th></th>";
    $view=$view."</tr>";
    if(!empty($rdate)){
      $view=$view."<tr><td class='w-25'>Reported</td><td>".$rdate."</td>";
      $view=$view."<td></td>";
      $view=$view."</tr>";
    }
    foreach($data as $field=>$value){
      $warn="";
      if(!empty($schema->properties->$field->range)){
        $warn=resolveRange($schema->properties->$field->range, $value);
      }
      else{
        $warn="";
      }
      if(!empty($value) && $field!="form" && $field!="date" && $field!="time" && $field!="rdate" && $field!="rtime"){
        if(!empty($schema->properties->$field)){
          $view=$view."<tr><td>".$schema->properties->$field->description."</td><td class='".$warn."'>".$value."</td>";
          if(!empty($schema->properties->$field->range)){
            $view=$view."<td>".resolveRange($schema->properties->$field->range)."</td>";
          }
          else{
            $view=$view."<td></td>";
          }
          $view=$view."</tr>";
        }
        elseif($field=="extra_note"){
          $view=$view."<tr><td>Extra Notes</td><td><pre>".$value."</pre></td><td></td></tr>";
        }
        else{
          $view=$view."<tr><td>".$field."</td><td>".$value."</td><td></td></tr>";
        }
      }
    }
    if(!empty($edit)){
      $view=$view."<tr><td colspan='3'><a href='".$edit."'>Edit</a>";
    }
    $view=$view."</table>";
    return $view;
  }
  else{
    return "";
  }
}

function viewAntibiogram($data, $edit=null){
  $data=json_decode($data);
  $view="<table class='table table-striped'>";
  $view=$view."<tr><th>Vitek Report</th><th colspan='2'>".$data->date."</th></tr>";
  if(!empty($data->rdate)){
    $view=$view."<tr><td>Reported on</td><td colspan='2'>".$data->rdate."</td></tr>";
  }
  $view=$view."<tr><td>Sample</td><td colspan='2'>".$data->sample."</td></tr>";
  $view=$view."<tr><td>Lab ID</td><td colspan='2'>".$data->labid."</td></tr>";
  $view=$view."<tr><td>Organism</td><td colspan='2'>".$data->organism."</td></tr>";
  $view=$view."<tr><th>Antibiotic</th><th>MIC</th><th>Interpretation</th>";
  foreach($data as $k=>$v){
    if(is_object($v)){
      $view=$view."<tr><td>".$v->name."</td><td>".$v->mic."</td><td>".$v->interpretation."</td></tr>";
    }
  }
  if(!empty($data->note)){
    $view=$view."<tr><td>Note</td><td colspan='2'>".$data->note."</td></tr>";
  }
  if(!empty($edit)){
    $view=$view."<tr><td colspan='2'><a href='".$edit."'>Edit</a></td></tr>";
  }
  $view=$view."</table>";
  return $view;
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
