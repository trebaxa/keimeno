 <form method=post action="<%$PHPSELF%>" enctype="multipart/form-data" id="stdformval">
 <input type="hidden" name="epage" value="<%$epage%>">
 <input type="hidden" name="id" value="<% $GET.id %>">
 <input type="hidden" name="cmd" value="save_link">
 <div style="width:700px">
	<fieldset>
	<legend>Related Link</legend>
	<% if ($BALINK.rl.url!="") %>
	URL: <a href="<% $BALINK.rl.url %>" target="_blank"><% $BALINK.rl.url %></a>
	<%/if%>
 <table class="table table-striped table-hover" >
	<tr><td>Homepage Title:</td><td><input class="validate[required]" id="rl-title" value="<% $BALINK.rl.title|hsc %>" size="30" name="FORM[title]"></td></tr>
	<tr id="links_url_tr"><td>URL to homepage:</td><td><input id="links_url"  value="<% $BALINK.rl.url|hsc %>"  size="30" name="FORM[url]"></td></tr>
	<tr><td>Category:</td><td>  	<select class="form-control custom-select" name="FORM[cat_id]">
            	 		<% foreach from=$BALINK.linklist_groups item=gi %>         
 									<option <% if ($gi.id==$BALINK.rl.cat_id) %>selected<%/if%> value="<%$gi.id%>"><%$gi.lc_title%></option>
 									<%/foreach%>
            	</select></td></tr>
  <tr>
    <td>Type:</td>
    <td><select class="form-control custom-select" id="links_type" name="FORM[lb_type]" onChange="setform();">
        <option <% if ($BALINK.rl.lb_type=='U') %>selected<%/if%> value="U">Banner</option>
        <option <% if ($BALINK.rl.lb_type=='S') %>selected<%/if%> value="S">Script</option>
        <option <% if ($BALINK.rl.lb_type=='F') %>selected<%/if%> value="F">Flash</option>
    </select></td>
  </tr>    
  <tr>
    <td>Position:</td>
    <td><select class="form-control custom-select" name="FORM[lb_position]">
        <option <% if ($BALINK.rl.lb_verticalpos=='T' && $BALINK.rl.lb_horpos=='L') %>selected<%/if%> value="TL">Top Left</option>
        <option <% if ($BALINK.rl.lb_verticalpos=='T' && $BALINK.rl.lb_horpos=='C') %>selected<%/if%> value="TC">Top Center</option>
        <option <% if ($BALINK.rl.lb_verticalpos=='T' && $BALINK.rl.lb_horpos=='R') %>selected<%/if%> value="TR">Top Right</option>
        <option <% if ($BALINK.rl.lb_verticalpos=='M' && $BALINK.rl.lb_horpos=='L') %>selected<%/if%> value="ML">Middle Left</option>
        <option <% if ($BALINK.rl.lb_verticalpos=='M' && $BALINK.rl.lb_horpos=='C') %>selected<%/if%> value="MC">Middle Center</option>
        <option <% if ($BALINK.rl.lb_verticalpos=='M' && $BALINK.rl.lb_horpos=='R') %>selected<%/if%> value="MR">Middle Right</option>
        <option <% if ($BALINK.rl.lb_verticalpos=='B' && $BALINK.rl.lb_horpos=='L') %>selected<%/if%> value="BL">Bottom Left</option>
        <option <% if ($BALINK.rl.lb_verticalpos=='B' && $BALINK.rl.lb_horpos=='C') %>selected<%/if%> value="BC">Bottom Center</option>
        <option <% if ($BALINK.rl.lb_verticalpos=='B' && $BALINK.rl.lb_horpos=='R') %>selected<%/if%> value="BR">Bottom Right</option>
    </select></td>
  </tr>    
  
	<tr>
        <td>Sichtbarkeit Toplevel:</td>
            <td>           
           	<% foreach from=$BALINK.rl.toplevel item=gi %> 
               <input type="checkbox" <% if ($gi.id|in_array:$BALINK.rl.tmatrix) %> checked<%/if%> name="TOPLEVEL[]" value="<%$gi.id%>">  <%$gi.description%><br>      
           <%/foreach%>
         </td>
    </tr>         
  <tr id="links_script"><td colspan="2" width="900">Script:<br><textarea class="form-control" cols="60" rows="6" name="FORM[lb_script]"><% $BALINK.rl.lb_script|hsc %></textarea></td></tr>
  <tr><td colspan="2" width="900">Text:<br><% $BALINK.rl.RLFCK %></td></tr>
  <tr><td colspan="2">Bild:<br><input type="file" name="aicon" size="30" class="submit" value="search" />(jpg,png,gif)
	<% if ($BALINK.rl.bild!="") %>
	  <br><img id="rl-image"  src="../file_data/links/<% $BALINK.rl.bild %>?a=<%$cms_token%>" >
	  <a href="javascript:void(0);" title="{LBL_DELETE}"><i id="del-<% $BALINK.rl.id %>" class="fa fa-trash delete"></i></a>
	<%/if%>
	</td>
	</tr>
</table>
<div class="subright"><%$subbtn%></div>
</fieldset>
</div>
</form>

<script type="text/javascript" charset="utf-8">
function setform() {
    if ($('#links_type').val()=='S') {
        $('#links_script').show();
        } else {
          $('#links_script').hide();  
        }
if ($('#links_type').val()=='U') {        
       $('#links_url').addClass('validate[required]');
       $('#links_url_tr').show();   
      } else {
        $('#links_url').removeClass('validate[required]');
        $('#links_url_tr').hide();
      }
}
setform();

$("table td img.delete").click(function () {
    execrequest('<%$PHPSELF%>?epage=<%$epage%>&cmd=axdelete_icon&id=' + $(this).attr('id'));
    $(this).hide();
    $('#rl-image').fadeOut();
    return false;
});


</script>

<% if ($BALINK.rl.id>0)%>
	 <h3>Import Meta:</h3>
     
	 <form action="<%$PHPSELF%>" method="post">
 	 <input type="hidden" name="id" value="<%$BALINK.rl.id%>">
 	 <input type="hidden" name="cmd" value="show_meta_import">
 	 <input type="hidden" name="epage" value="<% $epage %>">
 	 <input type="hidden" name="metaids[]" value="<%$BALINK.rl.id%>">
      <div style="width:700px">
	<fieldset><div class="alert alert-info">Importieren Sie die Meta Daten dieser Homepage.</div> 
 	 <%$btnimport%>
      </fieldset>
</div>
      </form>
     
 <%/if%>
