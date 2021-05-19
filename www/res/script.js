$(document).ready(function(){
  $("#upload").change(function(){
    lim=$("#size-limit").text().split("MB")[0]*1000*1000;
    if(this.files[0]["size"]>lim){
      $("#upload-error").html(" <span class='text-danger'>[Selected file exceeds size limit]</span>");
    }
    else if(["image/jpeg", "image/jpg", "image/png", "image/gif", "application/pdf"].indexOf(this.files[0]["type"])==-1){
      $("#upload-error").html(" <span class='text-danger'>"+this.files[0]["type"]+" files are not supported</span>");
    }
    else{
      $("#upload-error").text("");
    }
  });
  $("[name='date']").each(function(){
    if($(this).val()==""){
      $(this).val(moment().format("YYYY-MM-DD"));
    }
  });
  $("[name='time']").each(function(){
    if($(this).val()==""){
      $(this).val(moment().format("hh:mm"));
    }
  });
  $("[data-toggle='popover']").popover({"placement": "top", "trigger": "focus"});
  if($("[name='drug']").length){
    $(this).prop("autocomplete","off");
    $.get("autocomplete/drugs.json", function(data){
      $("[name='drug']").each(function(){
        $(this).autocomplete({source:data, highlightClass:'text-danger',treshold:1});
      });
    });
  };
  if($("[name='route']").length){
    $(this).prop("autocomplete","off");
    $.get("autocomplete/route.json", function(data){
      $("[name='route']").each(function(){
        $(this).autocomplete({source:data, highlightClass:'text-danger',treshold:0});
      });
    });
  };
  if($("[name='frequency']").length){
    $(this).prop("autocomplete","off");
    $.get("autocomplete/frequency.json", function(data){
      $("[name='frequency']").each(function(){
        $(this).autocomplete({source:data, highlightClass:'text-danger',treshold:0});
      });
    });
  };
  if($("[name='sample']").length){
    $(this).prop("autocomplete","off");
    $.get("autocomplete/investigation.json", function(data){
      $("[name='sample']").each(function(){
        $(this).autocomplete({source:data.sample, highlightClass:'text-danger',treshold:0});
      });
    });
  };
  if($(".abinter").length){
    $(this).prop("autocomplete","off");
    $.get("autocomplete/vitek.json", function(data){
      $(".abinter").each(function(){
        $(this).autocomplete({source:data.interpretation, highlightClass:'text-danger',treshold:0});
      });
    });
  };
  if($("#discharge-note").length){
    $.get("autocomplete/discharge.json", function(data){
      $("#discharge-note").val(data.note);
    });
  }
});
