<?php
require(dirname(__DIR__)."/require.php");
function json2tex($data){
    $data=json_decode($data);
    if(!empty($data->form)){
      $schema=json_decode(file_get_contents("forms/".$data->form.".schema.json"));
    }
    unset($data->cat);
    $view="\begin{tabularx}{\\textwidth}{l X}\n";
    foreach($data as $field=>$value){
      if($field!="form" && !empty($value)){
        if(!empty($schema->properties->$field)){
          $view=$view.$schema->properties->$field->description." & ".$value."\\\\\n";
        }
        else{
          $view=$view.$field." & ".$value."\n";
        }
      }
    }
    $view=$view."\\end{tabularx}\n";
    return $view;
}
if(!empty($_GET["pid"])){
  $pid=$_GET["pid"];
  $template=file_get_contents("discharge.tex");
  if(!empty($_POST["discharge-note"])){
    $template=str_replace("%[note]%", $_POST["discharge-note"], $template);
  }
  $template=str_replace("%[name]%", $db->getName($pid)->fetcharray()["name"], $template);
  $template=str_replace("%[age]%", $db->getAge($pid)->fetcharray()["age"], $template);
  $template=str_replace("%[sex]%", $db->getSex($pid)->fetcharray()["sex"], $template);
  $template=str_replace("%[pid]%", $pid, $template);
  $template=str_replace("%[diagnosis]%", $db->getDiagnosis($pid)->fetcharray()["diagnosis"], $template);
  $template=str_replace("%[doa]%", $db->getAdmission($pid)->fetcharray()["admission"], $template);
  $template=str_replace("%[dod]%", $db->getDeparture($pid)->fetcharray()["departure"], $template);
  $template=str_replace("%[summary]%", $db->getSummary($pid)->fetcharray()["summary"], $template);
  $list=$db->getAdvice($pid);
  $view="";
  while($drug=$list->fetchArray()){
    $view=$view."\item ".$drug["drug"]." ".$drug["dose"]." ".$drug["route"]." ".$drug["frequency"]." ".$drug["duration"]." ".$drug["addl"]."\n";
  }
  if($view){
    $template=str_replace("%[advice]%", "\begin{enumerate}\n".$view."\\end{enumerate}", $template);
  }
  $reports=[];
  $reportsArray=$db->getAllData($pid, "reports");
  while($r=$reportsArray->fetchArray()){
    $template=str_replace("%[reports]%", json2tex($r["data"]), $template);
  }
  //echo $template;
  $f=str_replace("/", "", $pid)."-".time()."-".rand();
  file_put_contents("data/discharge/".$f.".tex", $template);
  exec("pdflatex --output-directory data/discharge/ data/discharge/".$f.".tex", $out, $ret);
  //var_dump($out);
  //var_dump($ret);
  if($ret!=0){
    header("Content-Type: text/plain");
    echo "Failed to generate discharge certificate. Please check whether patient information, summary, reports, discharge advices are properly filled up.";
  }
  else{
    $db->setDischarged($pid);
    header("Content-Type: application/pdf");
    readFile("data/discharge/".$f.".pdf");
    exec(" rm data/discharge/".$f."*");
  }
}
?>
