<h3>Anlegen / Bearbeiten</h3>
<form class="stdform form-inline" action="<%$PHPSELF%>" method="post" enctype="multipart/form-data">
<div style="width:800px">
<fieldset>	
<legend>Kategorie bearbeiten</legend>
<table class="table table-striped table-hover">
<tr>
	<td ><label>Titel:</label></td>
	<td><input type="text" class="form-control" name="FORM[lc_title]" value="<% $BALINK.lgroup.lc_title|hsc %>" size="30"></td>
</tr>
<tr>
	<td ><label>Kurzbeschreibung:</label></td>
	<td><input type="text" class="form-control" name="FORM[lc_description]" value="<% $BALINK.lgroup.lc_description|hsc %>" size="30"></td>
</tr>
</table>
<div class="subright"><%$subbtn%></div>
</fieldset>	
</div>
  <input type="hidden" name="cmd" value="savegroup">
  <input type="hidden" name="cid" value="<%$REQUEST.cid%>">
  <input type="hidden" name="epage" value="<%$epage%>">
</form>

<% if (count($BALINK.linklist_groups)>0) %>
<div style="width:800px">
<fieldset>	
<legend>Kategorien</legend>
 <table class="table table-striped table-hover">
 <thead><tr>
 	<th>Name</th>
 	<th>Anzahl Links</th>
 	<th></th>
 </tr></thead>
 		<% foreach from=$BALINK.linklist_groups item=linkitem %>         
 		          
        <tr>
            	<td><% $linkitem.lc_title %><br><span class="small"><%$linkitem.lc_description%></span></td>
            	<td><%$linkitem.LINKCOUNT%></td>
            	<td class="text-right"> <% foreach from=$linkitem.icons item=picon name=cicons %><% $picon %><%/foreach%></td>
         </tr>
 		<%/foreach%>
  </table>
</fieldset>	
</div>  

<%else%><div class="bg-info text-info">Keine Kategorien gefunden.</div><%/if%>
 		