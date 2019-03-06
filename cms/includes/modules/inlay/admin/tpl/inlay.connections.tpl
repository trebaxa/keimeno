<div id="tab2" class="tabvisi" style="display:none">
<div class="page-header"><h1>Sichtbarkeit</h1></div>
<h3>Wo soll dieser Inhalt erscheinen?</h3>
<div style="height:30px">
    <div style="display:none" id="inlay-feed"></div>
</div>
<div class="width:600px">
<fieldset>
<form id="inlay-form-add" method="POST" action="<%$PHPSELF%>">
<input type="hidden" name="cmd" value="add_position">
<input type="hidden" name="epage" value="<%$epage%>">
<input type="hidden" name="FORM[inlay_id]" value="<%$REQUEST.id%>">
<table class="table table-striped table-hover">
<tr>
    <td><select class="form-control" id="i_tid" name="FORM[tid]">
        <% $INLAY.website_tree %>
        </select>
    </td>
    <td>
       <select class="form-control" id="i_pos" name="FORM[i_pos]">
            <option value="1">oben</option>
            <option value="2">unten</option>
       </select>
    </td>   
    <td><input type="submit" value="{LA_ADD}"></td> 
</tr>
</table>
</form>   

<div id="inlay-conn-table">

<% include file="inlay.conntable.tpl" %>
</div>
     
</fieldset>
</div>



</div>

<script>
function showResponseInlay(responseText, statusText, xhr, $form)  { 
   $('#inlay-feed').html(responseText);
   $("#inlay-feed").show();
   setTimeout('$("#inlay-feed").fadeOut();', 3000);
   simple_load_nocache('inlay-conn-table','<%$PHPSELF%>?epage=<%$epage%>&id=<%$REQUEST.id%>&cmd=reload_conn_table');
} 

   var options = { 
        success:       showResponseInlay   
     };  
  $('#inlay-form-add').ajaxForm(options); 


</script>
