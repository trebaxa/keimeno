<div class="btn-group"><a class="btn btn-default" href="javascript:void(0)" data-toggle="modal" data-target="#ekomimailnew">Email erstellen</a></div>


<!-- Modal -->
<div class="modal fade" id="ekomimailnew" tabindex="-1" role="dialog" aria-labelledby="ekomimailnewLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
    <form action="<%$PHPSELF%>" method="post" class="jsonform form-inline">
    <input type="hidden" name="cmd" value="create_et"/>
    <input type="hidden" name="epage" value="<%$epage%>"/>    
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="ekomimailnewLabel">Email Template</h4>
      </div>
      <div class="modal-body">
       <table>
        <tr>
            <td>{LBL_TEMPLATENAME}:</td>
            <td><input type="text" class="form-control" name="FORM[name]" /></td>
        </tr>  
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <%$subbtn%>
      </div>
      </form> 
    </div>
  </div>
</div>


<div class="row">
    <div class="col-md-3">
        <select class="form-control" id="emailtselect"></select>
    </div>
</div>        
<form action="<%$PHPSELF%>" method="post" class="jsonform form-inline">
    <input type="hidden" name="cmd" value="save_et"/>
    <input type="hidden" name="filename" id="mailid" value=""/>
    <input type="hidden" name="epage" value="<%$epage%>"/>
<div>
   <div class="form-group">
        <label>Vorhandene Templates:</label><br>
        <textarea class="form-control" style="width:600px;height:300px;" name="mailarea"  id="mailarea"></textarea><br/>
    </div>
</div>

  <%$subbtn%>
</form> 

<script>
function reload_mail_tpls(selid) {
    $('#mailid').val($('#emailtselect').find(':selected').text());
    load_mail_tpls(selid);
   
}

function load_mail_tpls(selid) {
  $('#emailtselect').empty();
  $.getJSON('<%$PHPSELF%>?epage=<%$epage%>&cmd=get_mail_tpls', function( data ) {
  var selstr = "";
  var items = "<option value=''>-Template Auswahl-</option>";
  $.each( data.mailtpls, function( key, item ) {
    selstr="";
    if (item.key==selid) {
        selstr = "selected";
    }
    items += "<option "+selstr+" value='" + item.key + "'>" + item.file + "</option>";
  });

  $(items).appendTo( "#emailtselect" );
});
}

$( "#emailtselect" ).change(function() {
    $('#mailid').val($(this).val());
    $.getJSON( '<%$PHPSELF%>?epage=<%$epage%>&cmd=get_mail_con&ident='+$(this).val(), function( data ) {
        $('#mailarea').val(data.mailcontent);
   });   
});

reload_mail_tpls();
</script>