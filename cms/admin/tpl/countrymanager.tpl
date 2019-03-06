<% if ($cmd=='edit') %>
<h3>Add / Modify</h3>
<form action="<%$PHPSELF%>" method="post" enctype="multipart/form-data">
<div style="width:600px">
<fieldset>	
<legend>Region</legend>
<table>
<tr>
	<td class="label">Region Name:</td>
	<td><input type="text" class="form-control" name="FORM[lr_name]" value="<% $regionobj.lr_name|sthsc %>" size="30"></td>
</tr>
<tr>
	<td class="label">Continent:</td>
	<td>
<select class="form-control" name="FORM[lr_continet_id]" >
			<% foreach from=$continents item=continet  %>
			<option <% if ($continet.id==$region.lr_continet_id) %>selected<%/if%> value="<% $continet.id %>"><% $continet.lc_name %></option>
			<%/foreach%></select>
			</td>
			</tr>
</table>
<div class="subright"><%$subbtn%></div>
</fieldset>	
</div>
  <input type="hidden" name="cmd" value="region_saveregion">
  <input type="hidden" name="id" value="<%$REQUEST.id%>">
	<input type="hidden" name="epage" value="<%$epage%>">
</form>
<%/if%>

<% if ($cmd=='region') %>
<div class="page-header"><h1>{LBL_REGIONS}</h1></div>
<div class="btn-group">
    <a class="btn btn-default" href="<%$PHPSELF%>?epage=<%$epage%>&cmd=edit">{LBL_ADD_REGION}</a>
</div>
<form action="<%$PHPSELF%>" method="post" enctype="multipart/form-data">
<div style="width:600px">
<fieldset>	
		<table  class="table table-striped table-hover">
		 <thead><tr>
		 <th>Continent</th>
		 <th>Region</th>
		 <th></th>
		 </tr></thead>
	<% foreach from=$regions item=region %>	 
	<% if ($cname!=$region.lc_name) %>
		<tr class="trsubheader"><td colspan="3"><% $region.lc_name %></td></tr>
		<%/if%>
	<% assign var=cname value=$region.lc_name %>
	
	<tr>
			<td>
			<select class="form-control" name="REGIS[<%$region.RID%>][lr_continet_id]" >
			<% foreach from=$continents item=continet  %>
			<option <% if ($continet.id==$region.lr_continet_id) %>selected<%/if%> value="<% $continet.id %>"><% $continet.lc_name %></option>
			<%/foreach%></select>
			</td>
			<td>
			<input type="text" class="form-control" name="REGIS[<%$region.RID%>][lr_name]" <%$region.lr_name|ts%> value="<%$region.lr_name|sthsc%>">
			<input type="hidden" name="REGIS[<%$region.RID%>][id]" value="<%$region.RID%>">
			</td>			
			<td class="text-right"> <% foreach from=$region.icons item=picon name=cicons %><% $picon %><%/foreach%></td>
	</tr>		
	<%/foreach%>		
</table>
<div class="subright"><%$subbtn%></div>
</fieldset>
</div>
  <input type="hidden" name="cmd" value="reg_save">
	<input type="hidden" name="epage" value="<%$epage%>">
</form>
<%/if%>