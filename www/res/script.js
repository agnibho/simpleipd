today=new Date();
$.ajaxPrefilter(function(options, originalOptions, jqXHR){
  nocache=today.toISOString().split(":");
  if(options.url.indexOf("?")==-1){
    options.url=options.url+"?_nocache="+nocache[0]+nocache[1];
  }
  else{
    options.url=options.url+"&_nocache="+nocache[0]+nocache[1];
  }
});
$(document).ready(function(){
  $(".confirm").each(function(){
    $(this).click(function(event){
      if(!confirm("Are you sure?")){
        event.preventDefault();
      }
    });
  });
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
  $("[type='date']").each(function(){
    if($(this).val()==""){
      $(this).val(today.getFullYear()+"-"+("0"+(today.getMonth()+1)).slice(-2)+"-"+("0"+today.getDate()).slice(-2));
    }
  });
  $("[type='time']").each(function(){
    if($(this).val()==""){
      $(this).val(("0"+today.getHours()).slice(-2)+":"+("0"+today.getMinutes()).slice(-2));
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
      val=$("#get-sample").text();
      $("[name='sample']").each(function(){
        $(this).autocomplete({source:data.sample, highlightClass:'text-danger',treshold:0});
        if(val.length>0){
          $(this).val(val);
        }
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
