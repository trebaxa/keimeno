<% if count($searchresult)>0 %>
<div style="width:800px">
<h3>Suchergebnis</h3>
<legend>{LBL_FOUND} <% $searchresult.totalfound %></legend>
<table class="table table-striped table-hover"  >
	<% foreach from=$searchresult.res_tab item=sritem %>
		<tr>
		<td><a href="<%$sritem.edit_link%>"><% $sritem.description %></a></td>
		<% if ($GET.show_active!=1)%><td><% $sritem.foundcount %></td><%/if%>
		<td><%$sritem.label%></td>
        <td><img src="<% $sritem.thumb %>" ></td>
		<td><%$sritem.edit_icon%><%$sritem.app_icon%></td>
	</tr>
	<%/foreach%>
</table>
	</div>
<script>set_ajaxapprove_icons();</script>    
<%else%>
 <% if ($cmd=='search') %><div class="alert alert-info"> {LBL_NOENTRIES} </div><%/if%>
<%/if%>