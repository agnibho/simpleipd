<script src="res/jquery.min.js"></script>
<script src="res/moment.js"></script>
<script src="res/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="res/bootstrap-4-autocomplete.min.js"></script>

<script>
$(document).ready(function(){
  $("[name='date']").each(function(){
    if($(this).val()==""){
      $(this).val(moment().format("YYYY-MM-DD"));
    }
  });
  $("[name='time']").each(function(){
    if($(this).val()==""){
      $(this).val(moment().format("HH:MM"));
    }
  });
  if($("[name='drug']").length){
    $(this).prop("autocomplete","off");
    $.get("autocomplete/drugs.json", function(data){
      $("[name='drug']").each(function(){
        $(this).autocomplete({source:data, highlightClass:'text-danger',treshold:2});
      });
    });
  };
  if($("[name='route']").length){
    $(this).prop("autocomplete","off");
    $.get("autocomplete/route.json", function(data){
      $("[name='route']").each(function(){
        $(this).autocomplete({source:data, highlightClass:'text-danger',treshold:1});
      });
    });
  };
  if($("[name='frequency']").length){
    $(this).prop("autocomplete","off");
    $.get("autocomplete/frequency.json", function(data){
      $("[name='frequency']").each(function(){
        $(this).autocomplete({source:data, highlightClass:'text-danger',treshold:1});
      });
    });
  };
});
</script>
