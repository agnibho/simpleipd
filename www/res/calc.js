function entrySort(i, j){
  if(i[0]==j[0]){
    return 0;
  }
  else if(i[0]>j[0]){
    return -1;
  }
  else{
    return 1;
  }
}
var io=[];
var clinical={pr:[], rr:[], spo2:[], sbp:[], dbp:[]};
var reports={};
var treatment={};
$(document).ready(function(){
  $.getJSON("chart.php?pid="+pid+"&get=nursing", function(data){
    flag="";
    $.each(data, function(num, entry){
      // INTAKE-OUTPUT
      stamp=new Date(entry.date+" "+entry.time);
      if(entry.io_from!="" && entry.io_to!=""){
        start=new Date(entry.date+" "+entry.io_from);
        end=new Date(entry.date+" "+entry.io_to);
        if(!isNaN(start.getTime()) && !isNaN(end.getTime())){
          if(end.getTime()-start.getTime()>24*60*60*1000){
            flag=entry.date;
          }
          if(start==end){
            flag=entry.date;
          }
          if(start>end){
            start.setDate(start.getDate()-1);
          }
          io.push({start: start, end: end, in: entry.intake, out: entry.output});
        }
      }
      // CLINICAL
      if(entry.pr){
        clinical.pr.push([stamp, entry.pr]);
      }
      if(entry.rr){
        clinical.rr.push([stamp, entry.rr]);
      }
      if(entry.spo2){
        clinical.spo2.push([stamp, entry.spo2]);
      }
      if(entry.bp){
        clinical.sbp.push([stamp, entry.bp.split("/")[0]]);
      }
      if(entry.bp){
        clinical.dbp.push([stamp, entry.bp.split("/")[1]]);
      }
    });
    // INTAKE-OUTPUT
    approxIn=0;
    approxOut=0;
    ioGap=0;
    io.forEach(function(i){
      if(ioGap<(24*60*60*1000)){
        ioBuff=ioGap+(i.end.getTime()-i.start.getTime());
        if(ioBuff>(24*60*60*1000)){
          frac=((24*60*60*1000)-ioGap)/(ioBuff-ioGap);
          ioGap=(24*60*60*1000);
        }
        else{
          frac=1;
          ioGap=ioBuff;
        }
        approxIn=approxIn+(Number(i.in)*frac);
        approxOut=approxOut+(Number(i.out)*frac);
        if(flag==""){
          $(".ioGap").each(function(){
            $(this).text(ioGap/3600000);
          });
          $("#approxIn").text(approxIn);
          $("#approxOut").text(approxOut);
          $("#ioData").removeClass("d-none");
        }
        else{
          $("#ioInconsistent").text(flag);
          $("#ioAlert").removeClass("d-none");
        }
      }
    });
    // CLINICAL
    $.getJSON("chart.php?pid="+pid+"&get=physician", function(data){
      $.each(data, function(num, entry){
        stamp=new Date(entry.date+" "+entry.time);
        if(entry.pr){
          clinical.pr.push([stamp, entry.pr]);
        }
        if(entry.rr){
          clinical.rr.push([stamp, entry.rr]);
        }
        if(entry.spo2){
          clinical.spo2.push([stamp, entry.spo2]);
        }
        if(entry.bp){
          clinical.sbp.push([stamp, entry.bp.split("/")[0]]);
        }
        if(entry.bp){
          clinical.dbp.push([stamp, entry.bp.split("/")[1]]);
        }
      });
      clinical.pr.sort(entrySort);
      Object.keys(clinical).forEach(function(i){
        $("#clinVar").html($("#clinVar").html()+"<option>"+i+"</option>");
      });
    });
    // REPORTS
    $.getJSON("chart.php?pid="+pid+"&get=reports", function(data){
      $.each(data, function(num, entry){
        stamp=new Date(entry.date+" "+entry.time);
        Object.keys(entry).forEach(function(i){
          if(entry[i] && !isNaN(entry[i])){
            if(!Array.isArray(reports[i])){
              reports[i]=[];
            }
            reports[i].push([stamp, entry[i]]);
          }
        });
      });
      reports=Object.keys(reports).sort().reduce(function(obj, key){
          obj[key]=reports[key];
          return obj;
        },{});
      Object.keys(reports).forEach(function(i){
        $("#reportsVar").html($("#reportsVar").html()+"<option>"+i+"</option>");
      });
    });
    // TREATMENT
    $.getJSON("chart.php?pid="+pid+"&get=treatment", function(data){
      $.each(data, function(num, entry){
        treat=[];
        treat[0]=entry.drug+" "+entry.dose+" "+entry.route+" "+entry.frequency;
        treat[1]=$.parseJSON(entry.administer);
        treatment[entry.drug]=treat;
      });
      Object.keys(treatment).forEach(function(i){
        $("#drugVar").html($("#drugVar").html()+"<option>"+i+"</option>");
      });
    });
  });
  // EVENTS
  $("#clinVar").change(function(){
    $("#clinData").html("");
    param=$("#clinVar").val();
    clinical[param].forEach(function(i){
      $("#clinData").html($("#clinData").html()+"<tr><td>"+i[0].toLocaleString()+"</td><td>"+i[1]+"</td></tr>");
    });
  });
  $("#reportsVar").change(function(){
    $("#reportsData").html("");
    param=$("#reportsVar").val();
    reports[param].forEach(function(i){
      $("#reportsData").html($("#reportsData").html()+"<tr><td>"+i[0].toLocaleString()+"</td><td>"+i[1]+"</td></tr>");
    });
  });
  $("#drugVar").change(function(){
    param=$("#drugVar").val();
    $("#drugData1").html(treatment[param][0]);
    $("#drugData2").html("");
    treatment[param][1].forEach(function(i){
      $("#drugData2").html($("#drugData2").html()+" <span class='badge badge-success'>"+new Date(i*1000).toLocaleString()+"</span>");
    });
  });
});
