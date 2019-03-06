<% if (count($REDIAPI.apis)>0) %>
<table class="table table-striped table-hover" >
<thead><tr>
			<th>App Name</th>
			<th>App ID</th>
			<th>App Key</th>
			<th>{LBL_OPTIONS}</th>
			</tr></thead>  
<% foreach from=$REDIAPI.apis item=row %>
 	 <tr>
  	<td><% $row.r_name %></td>
  	<td><% $row.r_apiid %></td>
  	<td><% $row.r_apikey %></td>
  	<td class="text-right"> <% foreach from=$row.icons item=picon name=cicons %><% $picon %><%/foreach%></td>
  	</tr>
  <%/foreach%>
</table>
<%/if%>


  