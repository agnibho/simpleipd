<script src="res/jquery.min.js"></script>
<script src="res/moment.js"></script>
<script src="res/bootstrap/js/bootstrap.bundle.min.js"></script>

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
});
</script>
