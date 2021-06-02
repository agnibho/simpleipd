function entrySort(i, j){
  if(i[0]==j[0]){
    return 0;
  }
  else if(i[0]>j[0]){
    return 1;
  }
  else{
    return -1;
  }
}
var io=[];
var clinical={pr:[], rr:[], temperature:[], spo2:[], sbp:[], dbp:[], cbg:[]};
var reports={};
var treatment={};
var clinDict={pr: "Pulse Rate", rr: "Respiratory Rate", temperature: "Temperature", spo2: "SpO2", sbp: "Systolic BP", dbp: "Diastolic BP", cbg: "CBG"}
var reportsDict={};
$(document).ready(function(){
  var ctx1=$("#clinChart")[0].getContext("2d");
  var ctx2=$("#reportsChart")[0].getContext("2d");
  var ctx3=$("#drugsChart")[0].getContext("2d");
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
      if(entry.temperature){
        clinical.temperature.push([stamp, entry.temperature]);
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
      if(entry.cbg){
        clinical.cbg.push([stamp, entry.cbg]);
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
            $(this).text(Math.round(ioGap/3600000));
          });
          $("#approxIn").text(Math.round(approxIn));
          $("#approxOut").text(Math.round(approxOut));
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
        if(entry.temperature){
          clinical.temperature.push([stamp, entry.temperature]);
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
        if(entry.cbg){
          clinical.cbg.push([stamp, entry.cbg]);
        }
      });
      Object.keys(clinical).forEach(function(i){
        clinical[i].sort(entrySort);
        $("#clinVar").html($("#clinVar").html()+"<option value="+i+">"+clinDict[i]+"</option>");
      });
    });
    // REPORTS
    $.getJSON("chart.php?pid="+pid+"&get=reports", function(data){
      ajaxArray=[];
      $.each(data, function(num, entry){
        if(entry.form){
          ajax=$.getJSON("forms/"+entry.form+".schema.json", function(data){
            stamp=new Date(entry.date+" "+entry.time);
            Object.keys(entry).forEach(function(i){
              name=i+"-"+entry.form;
              if(entry[i] && !isNaN(entry[i])){
                if(!Array.isArray(reports[name])){
                  reportsDict[name]=data.properties[i].description+" ("+data.description+")";
                  reports[name]=[];
                }
                reports[name].push([stamp, entry[i]]);
              }
            });
          });
          ajaxArray.push(ajax);
        }
      });
      $.when.apply($,ajaxArray).then(function(){
        reports=Object.keys(reports).sort().reduce(function(obj, key){
          obj[key]=reports[key];
          return obj;
        },{});
        Object.keys(reports).forEach(function(i){
          reports[i].sort(entrySort);
          $("#reportsVar").html($("#reportsVar").html()+"<option value="+i+">"+reportsDict[i]+"</option>");
        });
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
    data={labels: [], datasets: [{ label:clinDict[param], data: [], fill: false, borderColor: "rgb(75, 192, 192)", tension: 0.1 }]};
    clinical[param].forEach(function(i){
      data.labels.push(i[0].toLocaleString());
      data.datasets[0].data.push(i[1]);
      $("#clinData").html($("#clinData").html()+"<tr><td>"+i[0].toLocaleString()+"</td><td>"+i[1]+"</td></tr>");
    });
    try{
      clinChart.destroy();
    }catch(e){};
    if(data.datasets[0].data.length>1){
      clinChart=new Chart(ctx1, {
        type: "line",
        data: data
      });
    }
  });
  $("#reportsVar").change(function(){
    $("#reportsData").html("");
    param=$("#reportsVar").val();
    data={labels: [], datasets: [{ label: reportsDict[param], data: [], fill: false, borderColor: "rgb(75, 192, 192)", tension: 0.1 }]};
    reports[param].forEach(function(i){
      data.labels.push(i[0].toLocaleString());
      data.datasets[0].data.push(i[1]);
      $("#reportsData").html($("#reportsData").html()+"<tr><td>"+i[0].toLocaleString()+"</td><td>"+i[1]+"</td></tr>");
      try{
        reportsChart.destroy();
      }catch(e){};
      if(data.datasets[0].data.length>1){
        reportsChart=new Chart(ctx2, {
          type: "line",
          data: data
        });
      }
    });
  });
  $("#drugVar").change(function(){
    param=$("#drugVar").val();
    data={labels: [], datasets: [{ label: param, data: [], backgroundColor: "rgb(75, 192, 192)"}]};
    $("#drugData1").html(treatment[param][0]);
    $("#drugData2").html("");
    treatment[param][1].forEach(function(i){
      // HARDCODED FIX FOR CORRUPTED DATA DUE TO PREVIOUS BUG
      if(i<1622230200){
        return;
      }
      // PLAN TO REMOVE IN FUTURE
      d=new Date(i*1000);
      data.datasets[0].data.push({x:d.getTime()/(1000*3600*24), y:(24-d.getHours())});
      $("#drugData2").html($("#drugData2").html()+" <span class='badge badge-success'>"+d.toLocaleString()+"</span>");
    });
    try{
      drugsChart.destroy();
    }catch(e){};
    if(data.datasets[0].data.length>0){
      drugsChart=new Chart(ctx3, {
        type: "scatter",
        data: data,
        options: {
          pointRadius: 10,
          pointHoverRadius: 10,
          scales: {
            x: {
              type: "linear",
              position: "top",
              ticks: {
                callback: function(val, index){
                  if(Number.isInteger(val)){
                    return new Date(val*1000*3600*24).toLocaleDateString();
                  }
                }
              }
            },
            y: {
              suggestedMin: 0,
              suggestedMax: 24,
              ticks: {
                maxTicksLimit: 24,
                callback: function(val, index){
                  return (24-val)+":00";
                }
              }
            }
          }
        }
      });
    }
  });
});
