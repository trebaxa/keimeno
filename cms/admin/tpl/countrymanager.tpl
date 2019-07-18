<% if ($cmd=='edit') %>
<%include file="cb.page.title.tpl" icon="" title="{LBL_REGIONS}"%>

    <div class="row">
    <div class="col-md-6">
    <%include file="cb.panel.header.tpl" icon="fa-language" title="{LBLA_EDIT}"%>        
        <form action="<%$PHPSELF%>" method="post" enctype="multipart/form-data">        
        <table class="table">
        <tr>
        	<td class="label">Region Name:</td>
        	<td><input type="text" class="form-control" name="FORM[lr_name]" value="<% $regionobj.lr_name|sthsc %>" size="30"></td>
        </tr>
        <tr>
        	<td class="label">Continent:</td>
        	<td>
        <select class="form-control custom-select" name="FORM[lr_continet_id]" >
        			<% foreach from=$continents item=continet  %>
        			<option <% if ($continet.id==$region.lr_continet_id) %>selected<%/if%> value="<% $continet.id %>"><% $continet.lc_name %></option>
        			<%/foreach%></select>
        			</td>
        			</tr>
        </table>
        <div class="subright"><%$subbtn%></div>
        
          <input type="hidden" name="cmd" value="region_saveregion">
          <input type="hidden" name="id" value="<%$REQUEST.id%>">
        	<input type="hidden" name="epage" value="<%$epage%>">
        </form>
    <%include file="cb.panel.footer.tpl"%>
    </div>
    </div>
<%/if%>

<% if ($cmd=='region') %>
<%include file="cb.page.title.tpl" icon="" title="{LBL_REGIONS}"%>
<div class="btn-group">
    <a class="btn btn-secondary" href="<%$PHPSELF%>?epage=<%$epage%>&cmd=edit">{LBL_ADD_REGION}</a>
</div>
<form action="<%$PHPSELF%>" method="post" enctype="multipart/form-data">
 <div class="row">
    <div class="col-md-6">

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
			<select class="form-control custom-select" name="REGIS[<%$region.RID%>][lr_continet_id]" >
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

</div>
</div>
  <input type="hidden" name="cmd" value="reg_save">
	<input type="hidden" name="epage" value="<%$epage%>">
</form>
<%/if%>